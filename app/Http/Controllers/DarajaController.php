<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MpesaTransaction;

class DarajaController extends Controller
{
    private function getAccessToken()
    {
        $consumer_key = env('MPESA_CONSUMER_KEY');
        $consumer_secret = env('MPESA_CONSUMER_SECRET');
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $response = Http::withBasicAuth($consumer_key, $consumer_secret)->get($url);

        return $response['access_token'] ?? null;
    }

    public function initiateStk(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'name' => ['required', 'string'],
        ]);

        // Convert phone to 254 format if user types 0712345678
		$rawPhone = preg_replace('/\D/', '', $request->phone); // remove non-digits

		if (preg_match('/^0\d{9}$/', $rawPhone)) {
			$phone = '254' . substr($rawPhone, 1);
		} elseif (preg_match('/^2547\d{8}$/', $rawPhone)) {
			$phone = $rawPhone;
		} else {
			return back()->with('error', 'Invalid phone number. Must start with 0XXXXXXXXX or 2547XXXXXXXX.');
		}

        $grandTotal = collect(session('cart', []))->sum(fn($item) => $item['price'] * $item['quantity']);
        if ($grandTotal <= 0) {
            return back()->with('error', 'Your cart is empty or invalid amount.');
        }

        $token = $this->getAccessToken();
        $timestamp = date('YmdHis');
        $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);

        try {
            $response = Http::withToken($token)->post("https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest", [
                "BusinessShortCode" => env('MPESA_SHORTCODE'),
                "Password" => $password,
                "Timestamp" => $timestamp,
                "TransactionType" => "CustomerPayBillOnline",
                "Amount" => $grandTotal,
                "PartyA" => $phone,
                "PartyB" => env('MPESA_SHORTCODE'),
                "PhoneNumber" => $phone,
                "CallBackURL" => env('MPESA_CALLBACK_URL'),
                "AccountReference" => "Mini Shop",
                "TransactionDesc" => "Order Payment"
            ]);

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                $checkoutRequestID = $response['CheckoutRequestID'];

                session([
                    'checkout_request_id' => $checkoutRequestID,
                    'pending_payment' => [
                        'CheckoutRequestID' => $checkoutRequestID,
                        'amount' => $grandTotal,
                        'phone' => $phone,
                        'name' => $request->name,
                    ]
                ]);

                return back()->with('success', 'STK push sent to your phone. Complete the payment...');
            }

            Log::error('STK Initiation Failed:', $response->json());
            return back()->with('error', 'Failed to initiate payment. Try again.');

        } catch (\Exception $e) {
            Log::error('STK Exception:', ['message' => $e->getMessage()]);
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }



    public function mpesaCallback(Request $request)
    {
        Log::info('M-Pesa Callback Received:', $request->all());

        $data = $request->all();
        $callback = $data['Body']['stkCallback'] ?? [];

        $this->processMpesaResult(
            $callback['CheckoutRequestID'] ?? null,
            $callback['ResultCode'] ?? null,
            $callback['ResultDesc'] ?? 'No description',
            $callback['CallbackMetadata']['Item'] ?? null
        );

        return response()->json(['status' => 'ok']);
    }

    private function queryStkStatus($checkoutRequestID)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return null;

            $url = "https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query";
            $timestamp = date('YmdHis');
            $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);

            $response = Http::timeout(30)->withToken($token)->post($url, [
                "BusinessShortCode" => env('MPESA_SHORTCODE'),
                "Password" => $password,
                "Timestamp" => $timestamp,
                "CheckoutRequestID" => $checkoutRequestID
            ]);

            Log::info('STK Query Response:', $response->json() ?? []);
            return $response->json();

        } catch (\Exception $e) {
            Log::error('STK Query Exception:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        $checkoutRequestID = $request->query('CheckoutRequestID');
        if (!$checkoutRequestID) return response()->json(['status' => 'pending']);

        $transaction = MpesaTransaction::where('CheckoutRequestID', $checkoutRequestID)->first();

        if ($transaction) {
            $success = $transaction->ResultCode == '0';
            $this->finalCleanup($success);

            session()->flash('success', $success ? 'Payment successful!' : 'Payment failed. Please try again.');
            session()->forget('checkout_request_id');
            return response()->json(['status' => 'done', 'success' => $success]);
        }

        // Query M-Pesa for latest status
        $queryResponse = $this->queryStkStatus($checkoutRequestID);
        if (!$queryResponse || !isset($queryResponse['ResultCode'])) {
            return response()->json(['status' => 'pending']);
        }

        $this->processMpesaResult(
            $checkoutRequestID,
            $queryResponse['ResultCode'],
            $queryResponse['ResultDesc'] ?? 'No description',
            $queryResponse['CallbackMetadata']['Item'] ?? null
        );

        $success = $queryResponse['ResultCode'] == '0';
        $this->finalCleanup($success);

        session()->flash('success', $success ? 'Payment successful!' : 'Payment failed. Please try again.');
        return response()->json(['status' => 'done', 'success' => $success]);
    }


    private function processMpesaResult($checkoutRequestID, $resultCode, $resultDesc, $metadataItems = null)
    {
        $pending = session('pending_payment', []);

        $amount = $receipt = $phone = null;
        if ($resultCode == '0' && $metadataItems) {
            foreach ($metadataItems as $item) {
                switch ($item['Name'] ?? '') {
                    case 'Amount': $amount = $item['Value']; break;
                    case 'MpesaReceiptNumber': $receipt = $item['Value']; break;
                    case 'PhoneNumber': $phone = $item['Value']; break;
                }
            }
        }

        MpesaTransaction::create([
            'MerchantRequestID' => $pending['MerchantRequestID'] ?? null,
            'CheckoutRequestID' => $checkoutRequestID,
            'ResultCode' => $resultCode,
            'ResultDesc' => $resultDesc,
            'Amount' => $amount,
            'MpesaReceiptNumber' => $receipt,
            'PhoneNumber' => $phone,
            'customer_name' => $pending['name'] ?? null,
            'amount_paid' => $pending['amount'] ?? null,
            'customer_phone' => $pending['phone'] ?? null,
            'paid_at' => ($resultCode == '0') ? now() : null,
        ]);
    }

    private function finalCleanup($success = false)
    {
        session()->forget(['checkout_request_id', 'pending_payment']);
        if ($success) {
            session()->forget('cart');
        }
    }
}

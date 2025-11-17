<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MpesaTransaction;

class DarajaController extends Controller
{
    // Get M-Pesa Access Token
    private function getAccessToken()
    {
        $consumer_key = env('MPESA_CONSUMER_KEY');
        $consumer_secret = env('MPESA_CONSUMER_SECRET');
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $response = Http::withBasicAuth($consumer_key, $consumer_secret)->get($url);
        return $response['access_token'];
    }

    // STK Push
    public function initiateStk(Request $request)
    {
        try {
            $grandTotal = 0;
            foreach (session('cart', []) as $item) {
                $grandTotal += $item['price'] * $item['quantity'];
            }
            $phone = $request->phone;
            $amount = $grandTotal;
            $token = $this->getAccessToken();
            $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
            $timestamp = date('YmdHis');
            $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);
            $response = Http::withToken($token)->post($url, [
                "BusinessShortCode" => env('MPESA_SHORTCODE'),
                "Password" => $password,
                "Timestamp" => $timestamp,
                "TransactionType" => "CustomerPayBillOnline",
                "Amount" => $amount,
                "PartyA" => $phone,
                "PartyB" => env('MPESA_SHORTCODE'),
                "PhoneNumber" => $phone,
                "CallBackURL" => env('MPESA_CALLBACK_URL'),
                "AccountReference" => "Mini Shop",
                "TransactionDesc" => "Order Payment"
            ]);

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == 0) {
                session(['checkout_request_id' => $response['CheckoutRequestID']]);
                return back()->with('success', 'STK push sent to your phone. Complete the payment. Waiting for confirmation...');
            } else {
                Log::error('STK Initiation Failed:', $response->json());
                return back()->with('error', 'Failed to initiate STK Push: ' . ($response['ResponseDescription'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('STK Initiation Exception:', ['message' => $e->getMessage()]);
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // Handle callback (keep as is, for when it works)
    public function mpesaCallback(Request $request)
    {
        Log::info('M-Pesa Callback Received:', $request->all());
        $data = $request->all();
        // Extract payment data
        $callback = $data['Body']['stkCallback'];
        $MerchantRequestID = $callback['MerchantRequestID'];
        $CheckoutRequestID = $callback['CheckoutRequestID'];
        $ResultCode = $callback['ResultCode'];
        $ResultDesc = $callback['ResultDesc'];
        // Default values
        $amount = null;
        $receipt = null;
        $phone = null;
        // ResultCode 0 = success
        if ($ResultCode == 0) {
            foreach ($callback['CallbackMetadata']['Item'] as $item) {
                if ($item['Name'] === 'Amount') {
                    $amount = $item['Value'];
                }
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $receipt = $item['Value'];
                }
                if ($item['Name'] === 'PhoneNumber') {
                    $phone = $item['Value'];
                }
            }
        }
        // Save in database
        MpesaTransaction::create([
            'MerchantRequestID' => $MerchantRequestID,
            'CheckoutRequestID' => $CheckoutRequestID,
            'ResultCode' => $ResultCode,
            'ResultDesc' => $ResultDesc,
            'Amount' => $amount,
            'MpesaReceiptNumber' => $receipt,
            'PhoneNumber' => $phone,
        ]);
        return response()->json(['status' => 'ok']);
    }

    // New: Query STK Status via API
    private function queryStkStatus($checkoutRequestID)
    {
        try {
            $token = $this->getAccessToken();
            $url = "https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query";
            $timestamp = date('YmdHis');
            $password = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);
            $response = Http::withToken($token)->post($url, [
                "BusinessShortCode" => env('MPESA_SHORTCODE'),
                "Password" => $password,
                "Timestamp" => $timestamp,
                "CheckoutRequestID" => $checkoutRequestID
            ]);

            Log::info('STK Query Response:', $response->json());

            return $response->json();
        } catch (\Exception $e) {
            Log::error('STK Query Exception:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    // Updated: Check Payment Status (with polling fallback)
    public function checkPaymentStatus(Request $request)
    {
        $checkoutRequestID = $request->query('CheckoutRequestID');
        if (!$checkoutRequestID) {
            return redirect('/payment-failed');
        }

        $payment = MpesaTransaction::where('CheckoutRequestID', $checkoutRequestID)->first();

        if ($payment) {
            // Already in DB (from callback or previous query)
            if ($payment->ResultCode == 0) {
                session()->forget(['cart', 'checkout_request_id']); // Clear on success
                return redirect('/payment-success');
            }
            return redirect('/payment-failed');
        }

        // Not in DB: Query API as fallback
        $queryResponse = $this->queryStkStatus($checkoutRequestID);

        if (!$queryResponse || !isset($queryResponse['ResultCode'])) {
            // Still pending or error: Keep polling
            return response()->json(['status' => 'pending']);
        }

        // Parse and save (structure similar to callback)
        $MerchantRequestID = $queryResponse['MerchantRequestID'] ?? null;
        $ResultCode = $queryResponse['ResultCode'];
        $ResultDesc = $queryResponse['ResultDesc'];
        $amount = null;
        $receipt = null;
        $phone = null;

        if ($ResultCode == 0 && isset($queryResponse['CallbackMetadata']['Item'])) {
            foreach ($queryResponse['CallbackMetadata']['Item'] as $item) {
                if ($item['Name'] === 'Amount') {
                    $amount = $item['Value'];
                }
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $receipt = $item['Value'];
                }
                if ($item['Name'] === 'PhoneNumber') {
                    $phone = $item['Value'];
                }
            }
        }

        // Save to DB
        MpesaTransaction::create([
            'MerchantRequestID' => $MerchantRequestID,
            'CheckoutRequestID' => $checkoutRequestID,
            'ResultCode' => $ResultCode,
            'ResultDesc' => $ResultDesc,
            'Amount' => $amount,
            'MpesaReceiptNumber' => $receipt,
            'PhoneNumber' => $phone,
        ]);

        // Redirect based on final status
        if ($ResultCode == 0) {
            session()->forget(['cart', 'checkout_request_id']);
            return redirect('/payment-success');
        }
        return redirect('/payment-failed');
    }
}
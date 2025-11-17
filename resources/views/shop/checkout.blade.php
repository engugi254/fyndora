<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mini Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Checkout</h2>

    <form action="/initiate-stk" method="POST" class="card p-4 shadow-sm">
		@csrf
		<div class="mb-3">
			<label class="form-label">Full Name</label>
			<input type="text" name="name" class="form-control" required>
		</div>

		<div class="mb-3">
			<label class="form-label">Email Address</label>
			<input type="email" name="email" class="form-control" required>
		</div>

		<div class="mb-3">
			<label class="form-label">Shipping Address</label>
			<textarea name="address" class="form-control" rows="3" required></textarea>
		</div>

		<div class="mb-3">
			<label class="form-label">Phone Number (M-Pesa)</label>
			<input type="text" name="phone" class="form-control" placeholder="2547XXXXXXXX" required>
		</div>

		<button type="submit" class="btn btn-success w-100">
			Pay with M-Pesa (STK Push)
		</button>
	</form>

</div>
@if(session('checkout_request_id'))
<script>
    function checkPaymentStatus() {
        fetch('/check-payment-status?CheckoutRequestID={{ session('checkout_request_id') }}')
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    response.json().then(data => {
                        if (data.status === 'pending') {
                            console.log('Still waiting...'); // Continue polling
                        } else {
                            window.location.href = '/payment-failed'; // Fallback
                        }
                    });
                }
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Poll every 5 seconds
    const pollInterval = setInterval(checkPaymentStatus, 5000);

    // Stop after 5 minutes (timeout)
    setTimeout(() => clearInterval(pollInterval), 300000);
</script>
@endif
</body>
</html>

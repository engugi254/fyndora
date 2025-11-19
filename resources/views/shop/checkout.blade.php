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

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- WAITING SPINNER (SHOWN AFTER STK PUSH) -->
    <div id="waitingBox"
         class="card p-4 text-center bg-white shadow-sm mb-3"
         style="display: {{ session('checkout_request_id') ? 'block' : 'none' }};">

        <div class="spinner-border text-success mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>

        <h5>Please complete the payment on your phone...</h5>
        <p class="text-muted">This page will automatically refresh when done.</p>
        <small class="text-muted">
            If nothing happens after 2 minutes, <a href="{{ url('/') }}">go back to shop</a>
        </small>
    </div>


    <!-- CHECKOUT FORM -->
    <div id="checkoutForm"
         style="display: {{ session('checkout_request_id') ? 'none' : 'block' }};">

        <form action="/initiate-stk" method="POST" class="card p-4 shadow-sm bg-white">
            @csrf

            <input type="text" name="name"
                   class="form-control mb-3"
                   placeholder="Full Name" required>

            <input type="email" name="email"
                   class="form-control mb-3"
                   placeholder="Email" required>

            <textarea name="address"
                      class="form-control mb-3"
                      placeholder="Address" required></textarea>

            <input type="text" name="phone"
                   class="form-control mb-3"
                   placeholder="07XXXXXXXX" required>

            <button type="submit" class="btn btn-success w-100">Pay with M-Pesa</button>
        </form>
    </div>

</div>

<!-- POLLING SCRIPT -->

    @if(session('checkout_request_id'))
		<script>
		const checkoutID = "{{ session('checkout_request_id') }}";
		const waitingDiv = document.getElementById('waiting');
		const formDiv = document.getElementById('checkout-form');

		// Show spinner only
		waitingDiv.style.display = 'block';
		formDiv.style.display = 'none';

		// Polling for payment status
		function checkPayment() {
			fetch('/check-payment-status?CheckoutRequestID=' + checkoutID)
				.then(res => res.json())
				.then(data => {
					if (data.status === 'done') {
						clearInterval(pollInterval);

						if (data.success) {
							alert("Payment successful! Returning to shop...");
							window.location.href = "/";     // ✅ Go back to homepage
						} else {
							alert("Payment failed. Please try again.");
							window.location.href = "/checkout"; // ✅ Reload checkout form
						}
					}
				})
				.catch(err => console.warn('Polling error:', err));
		}

		// Poll every 8 seconds
		const pollInterval = setInterval(checkPayment, 8000);

		// Stop after 5 minutes
		setTimeout(() => clearInterval(pollInterval), 300000);
		</script>
		@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

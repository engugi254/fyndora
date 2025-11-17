<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center">

<div class="container mt-5">
    <div class="card p-5 shadow-sm">
        <h2>Thank You, {{ $name }}!</h2>
        <p>Your order has been placed successfully.</p>
        <a href="/" class="btn btn-primary mt-3">Back to Shop</a>
    </div>
</div>

</body>
</html>

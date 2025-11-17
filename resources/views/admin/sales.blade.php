<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin">🛠️ Admin Dashboard</a>
            <a href="/" class="btn btn-outline-light">🏠 View Shop</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="fw-bold mb-4">💰 Sales</h2>

        <!-- Placeholder table -->
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Total (KES)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>#1001</td>
                    <td>John Doe</td>
                    <td>5,000</td>
                    <td><button class="btn btn-sm btn-info">View</button></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>#1002</td>
                    <td>Jane Smith</td>
                    <td>7,500</td>
                    <td><button class="btn btn-sm btn-info">View</button></td>
                </tr>
            </tbody>
        </table>

    </div>

</body>
</html>

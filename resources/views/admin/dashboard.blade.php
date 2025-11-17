<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Fyndora</title>
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

        <div class="row g-4">

            <!-- Products Section -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                        🛒 Products
						<span class="badge bg-light text-dark">{{ $productCount }}</span>                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <a href="/admin/products" class="btn btn-outline-primary w-100 mb-2">Manage Products</a>
                        <a href="/admin/create" class="btn btn-primary w-100">Add Product</a>
                    </div>
                </div>
            </div>

            <!-- Customers Section -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                        👥 Customers
						<span class="badge bg-light text-dark">{{ $customerCount }}</span>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <a href="/admin/customers" class="btn btn-outline-success w-100">View Customers</a>
                    </div>
                </div>
            </div>

            <!-- Sales Section -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center">
                        💰 Sales
						<span class="badge bg-dark text-white">{{ $salesCount }}</span>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <a href="/admin/sales" class="btn btn-outline-warning w-100">View Sales</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Logout Button -->
        <div class="container mt-5 mb-4 d-flex justify-content-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg px-5">
                    🚪 Logout
                </button>
            </form>
        </div>

    </div>

</body>
</html>

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Product Management</h2>
            <a href="/admin/create" class="btn btn-primary">➕ Add Product</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price (KES)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td><img src="{{ asset('images/' . $product->image) }}" width="80" class="rounded"></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <a href="/admin/edit/{{ $product->id }}" class="btn btn-warning btn-sm">✏️ Edit</a>
                            <a href="/admin/delete/{{ $product->id }}" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                               🗑️ Delete
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No products available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>

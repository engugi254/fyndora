<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin Dashboard</title>
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
        <div class="card shadow p-4">
            <h3 class="mb-4 text-center fw-bold">➕ Add New Product</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/store" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (KES)</label>
                    <input type="number" name="price" class="form-control" placeholder="Enter price" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Save Product</button>
                    <a href="/admin" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

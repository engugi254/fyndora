<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fyndora</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f9f9f9;
        }

        .navbar {
            background-color: #212529;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

        /* Base card style */
        .product-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        /* Hover effect */
        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Zoomed-in effect */
        .product-card.zoomed {
            position: relative;
            z-index: 10;
            transform: scale(1.4);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        /* Blur the others */
        .blur {
            filter: blur(4px);
            pointer-events: none;
            transition: filter 0.3s ease;
        }

        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 220px;
            object-fit: cover;
        }
        .search-box {
    max-width: 600px;
    margin: 0 auto 30px;
}

.footer {
    background-color: #212529;
    color: #adb5bd;
    margin-top: 60px;
    padding: 40px 0 20px;
}

.footer h5 {
    color: #ffffff;
    margin-bottom: 15px;
}

.footer a {
    color: #adb5bd;
    text-decoration: none;
}

.footer a:hover {
    color: #ffffff;
}

.footer-bottom {
    border-top: 1px solid #495057;
    margin-top: 30px;
    padding-top: 20px;
    text-align: center;
}
    </style>
</head>
<body>

    <!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="/">Fyndora</a>
			<div>
				<a href="/cart" class="btn btn-outline-light position-relative">
					🛒 Cart
					<span id="cart-count" 
						  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
						  {{ count(session('cart', [])) }}
					</span>
				</a>
			</div>
		</div>
	</nav>

    <!-- Product Grid -->
    <div class="container mt-5">
    <h2 class="mb-4 text-center">Our Products</h2>

    <form method="GET" action="{{ url('/') }}" class="search-box">
        <div class="input-group">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search products..."
                value="{{ $search ?? '' }}"
            >
            <button class="btn btn-primary" type="submit">
                Search
            </button>

            @if (!empty($search))
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    Clear
                </a>
            @endif
        </div>
    </form>

    @if (!empty($search))
        <p class="text-center text-muted">
            Search results for: <strong>{{ $search }}</strong>
        </p>
    @endif
        <div class="row g-4" id="productGrid">
            @if ($products->isEmpty())
                <div class="col-12 text-center py-5">
                    <h4>No products found</h4>
                    <p class="text-muted">
                        Try searching for a different product.
                    </p>
                </div>
            @else
            @foreach ($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card text-center product-card" data-product-id="{{ $product->id }}">
                        <img src="{{ asset('images/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text fw-bold text-success">KES {{ $product->price }}</p>
                            <form method="POST" action="/add-to-cart/{{ $product->id }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif
        </div>
    </div>

        <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Fyndora</h5>
                    <p>
                        A simple e-commerce platform built with Laravel,
                        providing customers with an easy way to browse
                        products and place orders.
                    </p>
                </div>

                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <p><a href="{{ url('/') }}">Home</a></p>
                    <p><a href="{{ url('/cart') }}">Shopping Cart</a></p>
                </div>

                <div class="col-md-4 mb-4">
                    <h5>Contact</h5>
                    <p>Email: support@fyndora.test</p>
                    <p>Online Store & Customer Support</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">
                    &copy; {{ date('Y') }} Fyndora. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Zoom & Blur Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const cards = document.querySelectorAll(".product-card");
            const grid = document.getElementById("productGrid");
            let activeCard = null;

            cards.forEach(card => {
                card.addEventListener("click", (e) => {
                    e.stopPropagation(); // Prevent event bubbling

                    if (activeCard === card) {
                        // If clicked again, reset zoom
                        card.classList.remove("zoomed");
                        cards.forEach(c => c.classList.remove("blur"));
                        activeCard = null;
                    } else {
                        // Zoom in this card, blur others
                        cards.forEach(c => {
                            if (c !== card) c.classList.add("blur");
                            else c.classList.add("zoomed");
                        });
                        activeCard = card;
                    }
                });
            });

            // Click anywhere outside — reset all
            document.addEventListener("click", () => {
                if (activeCard) {
                    activeCard.classList.remove("zoomed");
                    cards.forEach(c => c.classList.remove("blur"));
                    activeCard = null;
                }
            });
        });
    </script>
</body>
</html>

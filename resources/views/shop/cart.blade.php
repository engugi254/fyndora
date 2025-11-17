<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
	<style>
    th:last-child, td:last-child {
        border-left: none !important;
        border-right: none !important;
    }
	</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">🛒 Your Shopping Cart</h2>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if (count($cart) > 0)
        <table class="table table-striped table-bordered align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th>Product</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th style="border-right: none;">Action</th> <!-- Added this -->
        </tr>
    </thead>
    <tbody id="cart-body">
    @php $grandTotal = 0; @endphp
    @foreach ($cart as $id => $item)
        @php 
            $total = $item['price'] * $item['quantity']; 
            $grandTotal += $total; 
        @endphp
        <tr data-id="{{ $id }}">
            <td>{{ $item['name'] }}</td>
            <td><img src="{{ asset('images/' . $item['image']) }}" width="80"></td>

            <!-- ✅ Editable quantity -->
            <td>
                <div class="d-flex justify-content-center align-items-center">
                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
                        class="form-control w-50 text-center quantity-input" data-id="{{ $id }}">
                </div>
            </td>

            <td>KES {{ $item['price'] }}</td>
            <td class="item-total">KES {{ $total }}</td>
            <td>
                <a href="/remove-from-cart/{{ $id }}" class="btn btn-outline-danger btn-sm">
                    🗑️ Remove
                </a>
            </td>
        </tr>
    @endforeach

    <tr class="table-secondary">
        <td colspan="4" class="text-end fw-bold">Grand Total</td>
        <td id="grand-total" class="fw-bold">KES {{ $grandTotal }}</td>
    </tr>
</tbody>


</table>


        <div class="text-center mt-4">
            <a href="/" class="btn btn-secondary">Continue Shopping</a>
            <a href="/checkout" class="btn btn-success">Proceed to Checkout</a>
        </div>

    @else
        <div class="alert alert-info text-center">
            Your cart is empty. <a href="/">Shop Now</a>
        </div>
    @endif
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $(".quantity-input").on("change", function() {
        const id = $(this).data("id");
        const quantity = $(this).val();

        $.ajax({
            url: `/update-cart/${id}`,
            method: "POST",
            data: {
                quantity: quantity,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                // Update totals instantly
                $(`tr[data-id='${id}'] .item-total`).text(`KES ${response.itemTotal}`);
                $("#grand-total").text(`KES ${response.grandTotal}`);

                // ✅ Update the cart count dynamically
                $("#cart-count").text(response.totalItems);
            }
        });
    });
});
</script>

</body>
</html>

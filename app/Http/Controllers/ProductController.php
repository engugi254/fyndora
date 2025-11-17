<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Display all products
    public function index()
    {
        $products = Product::all();
        return view('shop.index', compact('products'));
    }

    // Display the cart
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    // Add to cart
   public function addToCart($id)
{
    $product = Product::findOrFail($id);

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'name' => $product->name,
            'quantity' => 1,
            'price' => $product->price,
            'image' => $product->image
        ];
    }

    session()->put('cart', $cart);
    return redirect()->back()->with('success', 'Product added to cart!');
}
	public function updateCart(Request $request, $id)
{
    $quantity = $request->quantity;
    $cart = session()->get('cart', []);

    if(isset($cart[$id])) {
        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);
    }

    // Recalculate totals
    $grandTotal = 0;
    $totalItems = 0;
    foreach($cart as $item) {
        $grandTotal += $item['price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }

    return response()->json([
        'itemTotal' => $cart[$id]['price'] * $cart[$id]['quantity'],
        'grandTotal' => $grandTotal,
        'totalItems' => $totalItems
    ]);
}


    // Checkout page
    public function checkout()
    {
        return view('shop.checkout');
    }

    // Remove from cart
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Product removed successfully');
    }
}

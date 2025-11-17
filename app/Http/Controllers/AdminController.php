<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class AdminController extends Controller
{
    // ✅ Require authentication for all admin pages
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 🏠 Show all products in admin panel
    public function index()
{
		$productCount = Product::count(); // total products
		$customerCount = 2; // placeholder
		$salesCount = 2;    // placeholder

		return view('admin.dashboard', compact('productCount', 'customerCount', 'salesCount'));
	}


    // 🛒 Product Management
    public function products()
    {
        $products = Product::all();
        return view('admin.pr_management', compact('products'));
    }

    // 👥 Customers
    public function customers()
    {
        return view('admin.customers');
    }

    // 💰 Sales
    public function sales()
    {
        return view('admin.sales');
    }

    // ➕ Show create form
    public function create()
    {
        return view('admin.create');
    }

    // 💾 Store new product in database
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle image upload
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        // Save to DB
        Product::create([
            'name'  => $request->name,
            'price' => $request->price,
            'image' => $imageName,
        ]);

        return redirect('/admin')->with('success', 'Product added successfully!');
    }

    // ✏️ Show edit form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.edit', compact('product'));
    }

    // 🔄 Update existing product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->name  = $request->name;
        $product->price = $request->price;
        $product->save();

        return redirect('/admin')->with('success', 'Product updated successfully!');
    }

    // 🗑️ Delete product
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect('/admin')->with('success', 'Product deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|url',
            'thumbnail_file' => 'nullable|image|max:2048',
            'is_service' => 'sometimes|boolean',
        ]);

        $data['slug'] = Str::slug($data['name'] . '-' . Str::random(4));
        $data['is_service'] = $request->boolean('is_service');

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('products', 'public');
            $data['thumbnail'] = Storage::url($path);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk dibuat');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'thumbnail' => 'nullable|url',
            'thumbnail_file' => 'nullable|image|max:2048',
            'is_service' => 'sometimes|boolean',
        ]);

        $data['slug'] = Str::slug($data['name'] . '-' . Str::random(4));
        $data['is_service'] = $request->boolean('is_service');

        if ($request->hasFile('thumbnail_file')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
            }
            $path = $request->file('thumbnail_file')->store('products', 'public');
            $data['thumbnail'] = Storage::url($path);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Produk dihapus');
    }
}


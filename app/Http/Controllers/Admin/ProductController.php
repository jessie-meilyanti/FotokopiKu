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
    public function index(Request $request)
    {
        $categoryId = $request->integer('category');

        $query = Product::with('category')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->latest();

        $products = $query->paginate(10);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories', 'categoryId'));
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
            'remove_thumbnail' => 'sometimes|boolean',
        ]);

        $data['slug'] = Str::slug($data['name'] . '-' . Str::random(4));
        $data['is_service'] = $request->boolean('is_service');

        // Handle remove thumbnail checkbox
        if ($request->boolean('remove_thumbnail')) {
            if ($product->thumbnail && str_starts_with($product->thumbnail, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
            }
            $data['thumbnail'] = null;
        }
        // Handle photo upload
        elseif ($request->hasFile('thumbnail_file')) {
            // Delete old photo only if it exists and is in storage
            if ($product->thumbnail && str_starts_with($product->thumbnail, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
            }
            $path = $request->file('thumbnail_file')->store('products', 'public');
            $data['thumbnail'] = Storage::url($path);
        } elseif (!$request->filled('thumbnail')) {
            // Keep existing thumbnail if no file upload and no URL provided
            unset($data['thumbnail']);
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


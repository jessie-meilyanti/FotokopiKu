<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');
        $query = Product::query()->latest()->with('category');

        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('catalog.index', compact('products', 'categories', 'categorySlug'));
    }

    public function show(string $slug)
    {
        $product = Product::with('category')->whereSlug($slug)->firstOrFail();

        return view('catalog.show', compact('product'));
    }
}


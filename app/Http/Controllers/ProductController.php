<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    public function stocks(Product $product)
    {
        $stocks = Stock::where('product_id', $product->id)->get();
        return response()->json($stocks);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Stock;

/**
 * Class ProductController
 *
 * Оперции с товаром
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Вывод списка товаров с их остатками по складам
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function stocks(Product $product)
    {
        $stocks = Stock::where('product_id', $product->id)->get();
        return response()->json($stocks);
    }
}

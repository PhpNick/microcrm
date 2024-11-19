<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // TODO Добавляем фильтры

        // Добавляем пагинацию
        $perPage = $request->input('per_page', 5);
        $orders = $query->paginate($perPage);

        return $this->response($orders);
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $order = Order::create([
                'customer' => $request->input('customer'),
                'warehouse_id' => $request->input('warehouse_id'),
                'status' => 'active',
            ]);

            foreach ($request->input('items') as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Товар не найден');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'count' => $item['count'],
                ]);
            }
        });

        return $this->response(['message' => 'Заказ успешно создан'], 201);
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        DB::transaction(function () use ($request, $order) {
            $order->update([
                'customer' => $request->input('customer'),
                'warehouse_id' => $request->input('warehouse_id'),
            ]);

            // Обновляем позиции из заказа
            $order->orderItems()->delete();
            foreach ($request->input('items') as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }
        });

        return $this->response(['message' => 'Заказ успешно обновлен']);
    }

    public function complete(Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->orderItems as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $order->warehouse_id)
                    ->first();

                if ($stock) {
                    if ($stock->stock < $item->count) {
                        throw new \Exception('На складе не хватает товара с id: ' . $item->product_id);
                    }

                    $stock->stock -= $item->count;
                    $stock->save();
                }
            }

            $order->update([
                'status' => 'completed',
                'completed_at' =>  Carbon::now()
            ]);
        });

        return $this->response(['message' => 'Заказ успешно завершен']);
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        $order->update(['status' => 'cancelled']);

        return $this->response(['message' => 'Заказ успешно отменен']);
    }


    public function resume(Order $order)
    {
        if ($order->status !== 'cancelled') {
            return $this->response(['error' => 'Только отмененные заказы могут быть возобновлены'], 400);
        }

        $order->update(['status' => 'active']);

        return $this->response(['message' => 'Заказ успешно возобновлен']);
    }

    private function response($message, $status = 200)
    {
        return response()->json(
            $message,
            $status,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

}

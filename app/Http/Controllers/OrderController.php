<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Models\Movement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Carbon\Carbon;

/**
 * Class OrderController
 *
 * Управление заказами (создание, обновление, завершение, отмена, возобновление)
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * Вывод списка заказов.
     *
     * @param Request $request
     * @param OrderFilter $filters
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, OrderFilter $filters)
    {
        $perPage = $request->input('per_page', 5);
        $page = $request->input('page', 1);

        $orders = Order::filter($filters)->paginate($perPage, ['*'], 'page', $page);

        return $this->response($orders);
    }

    /**
     * Создание нового заказа
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Если заказ не найден или нет достаточного количества товара
     */
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

            $this->stock($order, 'subtraction');
        });

        return $this->response(['message' => 'Заказ успешно создан'], 201);
    }

    /**
     * Обновление заказа
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        DB::transaction(function () use ($request, $order) {
            // Возвращаем на склад старое значение остатков по заказу
            $this->stock($order, 'addition');
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

            $this->stock($order, 'subtraction');
        });

        return $this->response(['message' => 'Заказ успешно обновлен']);
    }

    /**
     * Завершение заказа
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        $order->update([
            'status' => 'completed',
            'completed_at' =>  Carbon::now()
        ]);

        return $this->response(['message' => 'Заказ успешно завершен']);
    }

    /**
     * Отмена товара
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order)
    {
        if ($order->status !== 'active') {
            return $this->response(['error' => 'Не могу обновить заказ со статусом: ' . $order->status], 400);
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);
            $this->stock($order, 'addition');
        });

        return $this->response(['message' => 'Заказ успешно отменен']);
    }


    /**
     * Возобновление заказа
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function resume(Order $order)
    {
        if ($order->status !== 'cancelled') {
            return $this->response(['error' => 'Только отмененные заказы могут быть возобновлены'], 400);
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'active']);
            $this->stock($order, 'subtraction');
        });

        return $this->response(['message' => 'Заказ успешно возобновлен']);
    }

    /**
     * Генерация ответа в формате JSON с нужным статусом
     *
     * @param mixed $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    private function response($message, $status = 200)
    {
        return response()->json(
            $message,
            $status,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Обновление остатков товара (списывание/возвращение)
     *
     * @param Order
     * @param string $movementType Тип действия (списывание/возвращение) (addition/subtraction)
     * @throws \Exception Если заказ не найден или нет достаточного количества товара
     */
    private function stock($order, $movementType)
    {
        $order->refresh();

        foreach ($order->orderItems as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->first();

            if ($stock) {
                if ($stock->stock < $item->count) {
                    throw new \Exception('На складе не хватает товара с id: ' . $item->product_id);
                }

                if ($movementType == 'addition') {
                    $stock->stock += $item->count;
                } else {
                    $stock->stock -= $item->count;
                }
                $stock->save();

                // Движение товара
                $product = Product::find($item['product_id']);
                Movement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $order->warehouse_id,
                    'quantity' => $item['count'],
                    'type' => $movementType
                ]);
            }
        }
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{
    public function showOrders()
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', auth()->user()->id)
            ->get();

        if(!$orders){
            return $this->sendError('Orders not found');
        }

        return $this->sendResponse($orders, 'Orders retrieved successfully');
    }

    public function showOrderById($orderId){
        $order = Order::with('orderItems.product')
            ->where('user_id', auth()->user()->id)
            ->findOrFail($orderId);

        if(!$order){
            return $this->sendError('Order not found');
        }

        return $this->sendResponse($order, 'Order retrieved successfully');
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    $order->delete();
                    return $this->sendError('Insufficient stock for product ' . $product->name, code: 400);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price * $item['quantity'],
                ]);

                // Reduce stock
                $product->stock -= $item['quantity'];
                $product->save();
            }

            return $this->sendResponse($order, 'Order created successfully', 201);
        });
    }

    public function updateOrder(Request $request, $orderId)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($orderId);

        if ($order->status != 'pending') {
            return $this->sendError('Only pending orders can be updated', code: 400);
        }

        return DB::transaction(function () use ($validated, $order) {
            // Array untuk menyimpan item valid yang sudah diverifikasi stoknya
            $newOrderItems = [];

            // Validasi stok untuk setiap produk dalam order baru
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    return $this->sendError('Insufficient stock for product ' . $product->name, code: 400);
                }

                // Simpan item yang valid dalam array
                $newOrderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price * $item['quantity'],
                ];
            }

            // Jika validasi stok sukses, lakukan rollback stok dari item order sebelumnya
            foreach ($order->orderItems as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->stock += $item->quantity;
                $product->save();
            }

            // Hapus item order yang lama
            $order->orderItems()->delete();

            // Tambahkan item order baru dan kurangi stoknya
            foreach ($newOrderItems as $newItem) {
                OrderItem::create($newItem);

                // Kurangi stok produk setelah item ditambahkan
                $product = Product::findOrFail($newItem['product_id']);
                $product->stock -= $newItem['quantity'];
                $product->save();
            }

            // Simpan perubahan pada order
            $order->save();

            return $this->sendResponse($order, 'Order updated successfully', 200);
        });
    }

    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status != 'pending') {
            return $this->sendError('Only pending orders can be canceled', code: 400);
        }

        return DB::transaction(function () use ($order) {
            // Revert stock for current items
            foreach ($order->orderItems as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->stock += $item->quantity;
                $product->save();
            }

            $order->status = 'cancelled';
            $order->save();

            return $this->sendResponse($order, 'Order cancelled successfully', 200);
        });
    }
}

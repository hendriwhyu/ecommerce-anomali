<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * @unauthenticated
     */
    public function showProducts(Request $request){
        $productSearch = $request->query->get('item');
        $categorySearch = $request->query->get('category');

        if($productSearch && $categorySearch){
            $dataProducts = Product::with(['categories'])
            ->where('name', 'LIKE', "%$productSearch%")
            ->whereHas('categories', function ($query) use ($categorySearch) {
                $query->where('name', 'LIKE', "%$categorySearch%");
            })
            ->get();
        }elseif ($categorySearch || $categorySearch){
            $dataProducts = Product::with(['categories'])
            ->where('name', 'LIKE', "%$productSearch%")
            ->orWhereHas('categories', function ($query) use ($categorySearch) {
                $query->where('name', 'LIKE', "%$categorySearch%");
            })
            ->get();
        }else{
            $dataProducts = Product::with(['categories'])->get();
        }

        $data = ProductCollection::collection($dataProducts);

        return $this->sendResponse($data, 'Products retrieved successfully');
    }

    /**
     * @unauthenticated
     */
    public function showProductById(Product $product){
        $productById = $product->with(['categories'])->first();

        if(!$productById){
            return $this->sendError('Product not found.');
        }

        $data = new ProductCollection($productById);

        return $this->sendResponse($data, 'Product retrieved successfully');
    }

    public function checkStock($productId, Request $request){
        $quantity = $request->input('quantity');
        $product = Product::LockForUpdate()->findOrFail($productId);
        if ($product->stock < $quantity) {
            throw new Exception('Insufficient stock for product ' . $product->name);
        }

        return $this->sendResponse($product->stock, 'Product stock checked successfully');
    }

    public function decreaseStock($id, Request $request){
        $quantity = $request->input('quantity');
        $product = Product::LockForUpdate()->findOrFail($id);
        $product->stock -= $quantity;
        $product->save();
    }

    public function returnStock($id, Request $request){
        $quantity = $request->input('quantity');
        $product = Product::LockForUpdate()->findOrFail($id);
        $product->stock += $quantity;
        $product->save();
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
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

    public function showProductById($id){
        $dataProduct = Product::with(['categories'])->findOrFail($id);

        if(!$dataProduct){
            return $this->sendError('Product not found.');
        }

        $data = new ProductCollection($dataProduct);

        return $this->sendResponse($data, 'Product retrieved successfully');
    }
}
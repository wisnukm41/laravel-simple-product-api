<?php

namespace App\Http\Controllers;

use App\Classes\ResponseClass;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{   
    public function __construct(private ProductRepository $productRepository)
    {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->productRepository->index();

        return ResponseClass::sendResponse(ProductResource::collection($data),'',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $details = [
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->file('image')->store('images','public'),
            'price' => $request->price,
        ];

        DB::beginTransaction();

        try{
             $product = $this->productRepository->store($details);

             DB::commit();
             return ResponseClass::sendResponse(new ProductResource($product),'Product Create Successful',201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productRepository->getById($id);

        return ResponseClass::sendResponse(new ProductResource($product),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ];
        if($request->hasFile('image')){
            $updateDetails['image'] = $request->file('image')->store('images','public');
            $product = $this->productRepository->getById($id);
            if(Storage::disk('public')->exists($product->image)){
                Storage::disk('public')->delete($product->image);
            }
        }

        DB::beginTransaction();
        try{
             $product = $this->productRepository->update($updateDetails,$id);

             DB::commit();
             return ResponseClass::sendResponse('Product Update Successful','',201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->productRepository->delete($id);

        return ResponseClass::sendResponse('Product Delete Successful','',204);
    }
}

<?php

namespace App\Repositories;
use App\Models\Product;

class ProductRepository
{
    public function index(){
        return Product::all();
    }

    public function getById($id){
       return Product::findOrFail($id);
    }

    public function store(array $data){
       return Product::create($data);
    }

    public function update(array $data,$id){
       return Product::whereId($id)->update($data);
    }
    
    public function delete($id){
       Product::destroy($id);
    }
}

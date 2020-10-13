<?php

namespace App\Http\Controllers;

use App\ProductCategory;
use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class ProductCategoryController extends Controller
{
    const ITEM_PER_PAGE = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $categories = ProductCategory::paginate( $limit);
        return response()->json(new JsonResponse(['categories' => $categories]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|unique:product_categories',
        ]);
        
        $category = new ProductCategory();
        $category->title =  $request->get('title');      
        $category->save();
        return $category;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product_category  $product_category
     * @return \Illuminate\Http\Response
     */
    public function show(Product_category $product_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product_category  $product_category
     * @return \Illuminate\Http\Response
     */
    public function edit(Product_category $product_category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product_category  $product_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product_category $product_category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product_category  $product_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product_category $product_category)
    {
        //
    }
}

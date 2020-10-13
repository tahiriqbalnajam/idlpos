<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\PurchaseProducts;
use Validator;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class PurchasesController extends Controller
{

    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $date = $request->get('daterange');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $purchases = Purchase::with([
            'supplier',
            'products' => function($query) {
                $query->with('product');
            }

        ])
        ->when($date[0], function($query) use ($date) {
            return $query->whereBetween('created_at', [$date[0], $date[1]]);
        })
        ->select('id','subtotal','quantity','discount', 'supplier_id','created_at')
        ->paginate( $limit);

        return response()->json(new JsonResponse(['purchases' => $purchases]));
    }

    public function store(Request $request)
    {
        try {
            $messages = array(
                'supplier.required' => 'Please enter supplier.'
            );
            $validator = $this->validate($request,[
                'supplier'=>'required',
                'ttlQuantity'=>'required|min:1',
                'ttlQuantity'=>'required|min:1',
            ], $messages );

            $purchz = new Purchase();
            $purchz->subtotal = $request->ttlAmount;
            $purchz->supplier_id = $request->supplier;
            $purchz->quantity = $request->ttlQuantity;
            $purchz->discount = $request->discount ?? 0;
            $products = $request->products;
            foreach($products as $product) {
                $productsArr[] = new PurchaseProducts( [
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'] ?? 0,
                ]);                 
    
            }
            $purchz->save();
            $purchz->products()->saveMany($productsArr);
            return response()->json($purchz);
            
        } catch (\Throwable $th) {
            return response()->json( $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

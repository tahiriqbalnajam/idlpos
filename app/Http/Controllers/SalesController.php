<?php

namespace App\Http\Controllers;

use App\Sale;
use App\SaleProducts;
use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class SalesController extends Controller
{
    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $date = $request->get('daterange');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $purchases = Sale::with([
            'customer',
            'products' => function($query) {
                $query->with('product');
            }

        ])
        ->when($date[0], function($query) use ($date) {
            return $query->whereBetween('created_at', [$date[0], $date[1]]);
        })
        ->select('id','total','discount','totalpiad','quantity','total_items', 'customer_id','created_at')
        ->paginate( $limit);

        return response()->json(new JsonResponse(['sales' => $purchases]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validateDate = $this->validate($request,[
                'ttlAmount'=>'required|min:1',
                'ttlItems'=>'required|min:1',
                'ttlQuantity'=>'required|min:1',
            ]);
            $sale = new Sale();
            $sale->total = $request->ttlAmount;
            $sale->discount = $request->discount ?? 0;
            $sale->totalpiad = $request->ttlPaid ??  $request->ttlAmount;
            $sale->quantity = $request->ttlQuantity ??  $request->ttlQuantity;
            $sale->total_items = $request->ttlItems ??  $request->ttlItems;
            $products = $request->products;
            foreach($products as $product) {
                $productsArr[] = new SaleProducts( [
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'] ?? 0,
                   ]);                 
    
            }
            $sale->save();
            $sale->products()->saveMany($productsArr);
            return response()->json($sale);
        } catch (\Throwable $th) {
            return response()->json( $th->getMessage(), 403);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }
}

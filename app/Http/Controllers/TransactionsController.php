<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;
use App\AccountTransactions as ATrans;

class TransactionsController extends Controller
{
    const ITEM_PER_PAGE = 10;
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $date = $request->get('daterange');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $transactions = ATrans::with([
            'account',

        ])
        ->when($date[0], function($query) use ($date) {
            return $query->whereBetween('created_at', [$date[0], $date[1]]);
        })
        ->select('id','account_id','type','amount', 'comments','created_at')
        ->paginate( $limit);
        $transactions_naam = [];
        $transactions_jama = [];
        foreach($transactions as $a){
            if($a['type'] == 'naam'){
                $transactions_naam[] = $a;
            }
            if($a['type'] == 'jama'){
                $transactions_jama[] = $a;
            }
        }
        return response()->json(new JsonResponse(['transactions' => $transactions_naam,'transaction_jama' =>$transactions_jama]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

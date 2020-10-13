<?php

namespace App\Http\Controllers;

use App\Accounts;
use App\AccountTypes;
use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class AccountsController extends Controller
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
        $customers = Accounts::with(array('account_type'=>function($query){
                        $query->select('id','title');
                     }))->paginate($limit);

        return response()->json(new JsonResponse(['accounts' => $customers]));
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
        $customer = new Accounts([
            'name' => $request->get('name'),
            'phone' =>$request->get('phone'),
            'address' => $request->get('address'),
            'account_type_id'=> $request->get('type'),
        ]);
        $customer->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Accounts $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Accounts $account)
    {
        $customer = Accounts::find($request->id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->account_type_id = $request->type;
        $customer->save();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accounts $account,$id)
    {
        $data = Accounts::findOrFail($id);
        $data->delete();
        return response()->json(['status'=>'Data Deleted']);
    }

    public function search(Request $request, Accounts $account){
        $query = $request->get('query');
        if($query){
            $data = Accounts::where('name','LIKE',"%{$query}%")
                              ->orWhere('address','LIKE',"%{$query}%") 
                              ->orWhere('phone','LIKE',"%{$query}%")   
                              ->get();
                           //   dd($data);
            return response()->json(new JsonResponse(['data' => $data]));                  
        
        }
    }
}

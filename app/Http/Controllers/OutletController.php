<?php

namespace App\Http\Controllers;

use App\Outlet;
use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class OutletController extends Controller
{
    const ITEM_PER_PAGE = 10;

    public function index()
    {
        $outlets = Outlet::all();
        return response()->json(new JsonResponse(['outlets' => $outlets]));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $status = $request->get('status');
        $status = ($status == 'true')? 'true':'false';
        $outlet = new Outlet([
            'name' => $request->get('name'),
            'status' =>$status,
        ]);
        $outlet->save();
        return response()->json(new JsonResponse(['customer' => $outlet]));
    }

    public function show(Outlet $outlet)
    {
        //
    }


    public function edit(Outlet $outlet)
    {
        //
    }

    public function update(Request $request, Outlet $outlet)
    {
        $outlet = Outlet::find($request->id);
        $outlet->name = $request->name;
        $outlet->status = $request->status;
        $outlet->save();
    }

    public function destroy($id)
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->delete();
        return response()->json(['status'=>'Outlet Deleted']);
    }
    public function search(Request $request, Outlet $outlet){
        $query = $request->get('query');
        if($query){
            $result = Outlet::where('name','LIKE',"%{$query}%")  
                              ->get();
                           //   dd($data);
            return response()->json(new JsonResponse(['result' => $result]));                  
        
        }
    }
}

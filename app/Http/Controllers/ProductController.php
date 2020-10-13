<?php

namespace App\Http\Controllers;
use DB;
use App\Product;
use App\ProductStock as ProductStock;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    const ITEM_PER_PAGE = 10;

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = $request->get('keyword');
        $select = ($request->get('select') == 'productonly') ? ['id','name','sale_price','code'] : "*" ;
        $products = Product::select($select)
                    ->when($select == "*", function ($query) use ($keyword) {
                        return $query->with(array('category'=>function($query){
                            $query->select('id','title');
                         }));
                    })
                    ->when($keyword, function ($query) use ($keyword) {
                        return $query->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('code', 'like', '%' . $keyword . '%');
                    })
                    ->paginate( $limit);

        return response()->json(new JsonResponse(['products' => $products]));
    }

    public function featured(Request $request) {
        $select =  ['id','name','sale_price','code'];
        $products = Product::select($select)->where('featured', 'yup')->get();
        return response()->json(new JsonResponse(['products' => $products]));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:products',
            'code' => 'required|unique:products',
            'purchase_price' => 'required',
            'category_id' => 'required',
            'sale_price' => 'required',
        ]);
        
        $product = new Product();
        $product->name =  $request->get('name');
        $product->code =  $request->get('code');
        $product->purchase_price =  $request->get('purchase_price');
        $product->sale_price =  $request->get('sale_price');
        $product->wholesale_price =  $request->get('wholesale_price');
        $product->category_id =  $request->get('category_id');        
        $product->save();
        return $product;
    }

    public function show(Product $product)
    {
        return $product->load('category');
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:products,name,'.$product->id,
            'purchase_price' => 'required',
            'category_id' => 'required',
            'sale_price' => 'required',
            'featured' => 'required',
            'code' => 'required',
        ]);
        
        $product->name =  $request->get('name');
        $product->purchase_price =  $request->get('purchase_price');
        $product->sale_price =  $request->get('sale_price');
        $product->wholesale_price =  $request->get('wholesale_price');
        $product->category_id =  $request->get('category_id');
        $product->status =  $request->get('status');
        $product->featured =  $request->get('featured');
        $product->code =  $request->get('code');

        $product->save();
        return $product;
    }

    public function destroy(Product $product)
    {
        //
    }

    public function getstock($id) {
        $product = Product::find($id)->stock()->orderBy('created_at', 'desc')->get();
        //$stock = ProductStock::where('product_id', $id);
        return response()->json(new JsonResponse(['stock' =>  $product]));
    }

    public function addstock(Request $request) {
        $stock = new ProductStock();
        $stock->quantity = $request->get('quantity');
        $stock->outlet_id = $request->get('outlet_id');
        $stock->product_id = $request->get('product_id');
        $stock->remarks = $request->get('remarks');
        $stock->save();
        return response()->json(new JsonResponse(['stock' =>  $stock]));

    }
}

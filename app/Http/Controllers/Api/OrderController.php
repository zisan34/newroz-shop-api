<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Submit order'])->only(['store']);
        $this->middleware(['permission:View orders'])->only(['index', 'show']);
        $this->middleware(['permission:Change order status'])->only(['changeStatus']);
        $this->middleware(['permission:Edit orders'])->only(['update']);
        $this->middleware(['permission:Add notes to order'])->only(['addNote']);
    }
    public function addNote(Request $request, $id)
    {
        try {
            Order::findOrFail($id)->notes()->create([
                'title' => $request->title,
                'description' => $request->description
            ]);
            return $this->apiResponse(200, "Success");
        } catch (\Throwable $th) {
            throw $th;
            return $this->apiErrorResponse($th->getCode(), $th->getMessage());
        }
    }
    public function changeStatus(Request $request, $id)
    {
        $this->validate($request, [
            'order_status' => 'required|string'
        ]);
        
        try {
            Order::where('id', $id)->update(['order_status' => $request->order_status]);
            return $this->apiResponse(200, "Success");
        } catch (\Throwable $th) {
            return $this->apiErrorResponse($th->getCode(), $th->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return $this->apiResponse(200, "Orders found", [
            'orders' => Order::with(['notes'])
                            ->when($request->customer_id, function($query) use($request){
                                return $query->where('customer_id', $request->customer_id);
                            })
                            ->get()
        ]);
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
    public function store(OrderRequest $request)
    {
        //
        try {
            $insertable_data = $request->only(['name', 'email', 'address', 'remarks']);
            $product_ids = collect($request->products)->pluck('id')->toArray();
            $products = Product::with('images')->whereIn('id', $product_ids)->get();
            $insertable_data['products'] = $products->toArray();
            $insertable_data['customer_id'] = auth()->user()->id;
            $order = Order::create($insertable_data);

            return $this->apiResponse(200, 'Order submitted successfully', [
                'order' => $order,
            ]);
        } catch (\Throwable $th) {
            return $this->apiErrorResponse(422, $th->getMessage());
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
        try {
            $order = Order::findOrFail($id);
            return $this->apiResponse(200, "Success", [
                'order' => $order
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiErrorResponse($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(OrderRequest $request, $id)
    {
        //
        try {
            $insertable_data = $request->only(['address', 'email', 'name', 'order_status', 'payment_status', 'products', 'remarks', 'tracking_status']);
            $order = Order::where('id', $id)->update($insertable_data);

            return $this->apiResponse(200, 'Order updated successfully', [
                'order' => $order,
            ]);
        } catch (\Throwable $th) {
            return $this->apiErrorResponse(422, $th->getMessage());
        }
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

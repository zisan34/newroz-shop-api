<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ModelImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Create products', 'auth:sanctum'])->only(['store']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return $this->apiResponse(200, "Products found", [
            'products' => Product::with('images')->orderBy('updated_at', 'desc')->get()
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
    public function store(ProductRequest $request)
    {
        //
        try {
            DB::beginTransaction();
            $insertable_data = $request->only(['title', 'description', 'price']);
            $product = Product::create($insertable_data);

            if($request->hasFile('images')){
                $insertable_images_data = [];
                foreach ($request->images as $key => $image) {
                    $insertable_images_data[] = [
                        'original_name' => $image->getClientOriginalName(),
                        'image_path' => FileUploadService::upload($image, 'uploads/products'),
                        'imageable_type' => Product::class,
                        'imageable_id' => $product->id
                    ];
                }
                ModelImages::insert($insertable_images_data);
            }
            DB::commit();
            return $this->apiResponse(200, 'Product created successfully', [
                'product' => $product->load('images'),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
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

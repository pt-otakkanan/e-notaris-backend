<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Product::orderBy('id','desc')->get();
        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => $data
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeProduct(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048'
        ]);

        if($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data',
                'data' => $validasi->errors()
            ],400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        } else {
            $imagePath = null;
        }

        $data = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan data',
            'data' => $data
        ],200);

    }

    
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProduct(Request $request, $id)
    {
        $validasi = Validator::make($request->all(),[
            'name' => 'sometimes|nullable',
            'price' => 'sometimes|nullable',
            'description' => 'sometimes|nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048'
        ]);

        if($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi edit gagal',
                'data' => $validasi->errors()
            ],401);
        }

        $product = Product::where('id',$id)->first();
        
        if($product == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ],400);
        }

        if($request->name != null) {
            $product->name = $request->name;
        }
        if($request->price != null) {
            $product->price = $request->price;
        }
        if($request->description != null) {
            $product->description = $request->description;
        }

        if ($request->hasFile('image')) {
            $filePath = public_path($product->image);
            if (file_exists($filePath) && $product->image != null) {
                unlink($filePath);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
            $product->image = $imagePath;
        }

        $product->save();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil edit product',
            'data' => $product
        ],200);

    }

    public function detailProduct(Request $request) {
        $id = $request->id;
        if($id == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
                'data' => $validasi->errors()
            ],401);
        }

        $product = Product::where('id',$id)->first();
        if($product == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ],400);
        }

        $data = $product;
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan detail product',
            'data' => $data
        ],200);
    }
    public function destroyProduct(Request $request)
    {

        $id = $request->id;
        if($id == null) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi edit gagal',
                'data' => $validasi->errors()
            ],401);
        }

        $product = Product::where('id',$id)->first();
        if($product == null) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ],400);
        }
        if($product->image != null) {
            $filePath = public_path($product->image);
            if (file_exists($filePath) && $product->image != null) {
                unlink($filePath);
            }
        }


        $data = $product;
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil hapus product',
            'data' => $data
        ],200);

    }
}

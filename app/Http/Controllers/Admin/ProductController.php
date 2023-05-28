<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use File;
class ProductController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $products = Product::latest()->when(request()->q, function($products) {
            $products = $products->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.product.index', compact('products'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.product.create', compact('categories'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
       $this->validate($request, [
           'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
           'title'          => 'required|unique:products',
           'category_id'    => 'required',
           'content'        => 'required',
           'weight'         => 'required',
           'price'          => 'required',
           'discount'       => 'required',
       ]);

        $product = new Product();
        $product->title = $request->get('title');
        $product->slug = Str::slug($request->get('title'));
        $product->category_id = $request->get('category_id');
        $product->content= $request->get('content');
        $product->weight= $request->get('weight');
        $product->price= $request->get('price');
        $product->discount= $request->get('discount');


       //upload image
       if ($request->hasFile('image')) {
            // $post->delete_image();
            $image = $request->file('image');
            // echo $photo_profile;exit;
            $name = rand(1000, 9999) . $image->hashName();
            $image->move('img', $name);
            $product->image = $name;
        }
        $product->save();

       if($product){
            //redirect dengan pesan sukses
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * edit
     *
     * @param  mixed $product
     * @return void
     */
    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        return view('admin.product.edit', compact('product', 'categories'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $product
     * @return void
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'          => 'required|unique:products,title,' .$id,
            'category_id'    => 'required',
            'content'        => 'required',
            'weight'         => 'required',
            'price'          => 'required',
            'discount'       => 'required',
        ]);

        $product = Product::findOrFail($id);
        $product->title = $request->get('title');
        $product->slug = Str::slug($request->get('title'));
        $product->category_id = $request->get('category_id');
        $product->content= $request->get('content');
        $product->weight= $request->get('weight');
        $product->price= $request->get('price');
        $product->discount= $request->get('discount');

        if ($request->hasFile('image')) {
            // $post->delete_image();

            if($request->file('image') == ""){
                $image = $request->file('image_old');
            }else{
                 $image = $request->file('image');
            }
            // echo $photo_profile;exit;
            $name = rand(1000, 9999) . $image->hashName();
            $image->move('img', $name);
            $product->image = $name;
            // dd($category);
        }
        $product->save();


        if($product){
            //redirect dengan pesan sukses
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $image_path = public_path("/img/") .$product->image;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        else{
            $product->delete();
            //abort(404);
        }

        $product->delete();

        if($product){
            return response()->json([
                'status' => 'success'
            ]);
        }else{
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}

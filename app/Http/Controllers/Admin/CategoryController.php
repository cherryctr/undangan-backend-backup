<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use File;
class CategoryController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $categories = Category::latest()->when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.category.index', compact('categories'));
    }

    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('admin.category.create');
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
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'name'     => 'required',

        ]);

        //upload image
        $category = new Category();
        $category->image = $request->get('image');
        $category->name = $request->get('name');
        $category->slug = Str::slug($request->get('name'));


        if ($request->hasFile('image')) {
            // $post->delete_image();
            $image = $request->file('image');
            // echo $photo_profile;exit;
            $name = rand(1000, 9999) . $image->hashName();
            $image->move('img', $name);
            $category->image = $name;
        }
        $category->save();

       if($category){
            //redirect dengan pesan sukses
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * edit
     *
     * @param  mixed $request
     * @param  mixed $category
     * @return void
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $category
     * @return void
     */
//     public function update(Request $request, Category $category)
//    {
//     $this->validate($request, [
//         'name'  => 'required|unique:categories,name,'.$category->id,
//         // 'image' => 'required|file|size:',
//         // 'image' => 'required | mimes:jpeg,jpg,png | max:30000',
//     ]);

//     //check jika image kosong
//     if($request->file('image') == '') {

//         //update data tanpa image
//         $category = Category::findOrFail($category->id);
//         $category->name = $request->get('name');
//         $category->slug = Str::slug($request->get('name'));
//         $category->save();

//     } else {
//         // dd("DATA ADA");
//         $image_path = public_path("/img/") .$category->image;

//         if(File::exists($image_path)) {
//             File::delete($image_path);
//         }
//         else{
//             //upload image baru
//             // $image = $request->file('image');
//             $category = Category::findOrFail($category->id);
//             $category->image = $request->get('image');
//             $category->name = $request->get('name');
//             $category->slug = Str::slug($request->get('name'));

//             $image = $request->file('image');

//             // echo $photo_profile;exit;
//             $name = rand(1000, 9999) . $image->hashName();
//             $image->move(public_path('img'), $name);
//             // dd($image);
//             // dd($image);
//             $category->save();
//         }
//     }

    // if($category){
    //     //redirect dengan pesan sukses
    //     return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Diupdate!']);
    // }else{
    //     //redirect dengan pesan error
    //     return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Diupdate!']);
    // }
//    }

    public function update(Request $request, $id)
    {
         $this->validate($request, [
            // 'gamba'     => 'image|mimes:png,jpg,jpeg',
            // 'judul'     => 'required',
            'name'   => 'required'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->get('name');
        $category->slug = Str::slug($request->get('name'));

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
            $category->image = $name;
            // dd($category);
        }
        $category->save();


        if($category){
            //redirect dengan pesan sukses
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Diupdate!']);
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
        $category = Category::findOrFail($id);
        $image_path = public_path("/img/") .$category->image;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        else{
            $category->delete();
            //abort(404);
        }
        $category->delete();

        if($category){
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

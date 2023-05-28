<?php

namespace App\Http\Controllers\Admin;

use App\Models\Fitur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;

class FiturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $fiturs = Fitur::latest()->when(request()->q, function($fiturs) {
            $fiturs = $fiturs->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.fitur.index', compact('fiturs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $categories = Category::latest()->get();
        return view('admin.fitur.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $this->validate($request, [
           'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
           'title'          => 'required|unique:fiturs',
           'content'        => 'required',
       ]);

        $fitur = new Fitur();
        $fitur->title = $request->get('title');
        $fitur->content= $request->get('content');



       //upload image
       if ($request->hasFile('image')) {
            // $post->delete_image();
            $image = $request->file('image');
            // echo $photo_profile;exit;
            $name = rand(1000, 9999) . $image->hashName();
            $image->move('img', $name);
            $fitur->image = $name;
        }
        $fitur->save();

        // dd($fitur);

       if($fitur){
            //redirect dengan pesan sukses
            return redirect()->route('admin.fitur.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.fitur.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fitur $fitur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fitur $fitur)
    {
        // $categories = Category::latest()->get();
        return view('admin.fitur.edit', compact('fitur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'          => 'required|unique:fiturs,title,' .$id,
            // 'category_id'    => 'required',
            'content'        => 'required',

        ]);

        $fitur = Fitur::findOrFail($id);
        $fitur->title = $request->get('title');
        $fitur->content= $request->get('content');

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
            $fitur->image = $name;
            // dd($category);
        }
        $fitur->save();
        // dd($fitur);

        if($fitur){
            //redirect dengan pesan sukses
            return redirect()->route('admin.fitur.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.fitur.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fiturs = Fitur::findOrFail($id);
        //dd($fiturs);
        $image_path = public_path("/img/") .$fiturs->image;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        else{
            $fiturs->delete();
            //abort(404);
        }

        $fiturs->delete();

        if($fiturs){
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

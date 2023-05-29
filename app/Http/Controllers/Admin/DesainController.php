<?php

namespace App\Http\Controllers\Admin;

use App\Models\Desain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;

class DesainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $desain = Desain::latest()->when(request()->q, function($desain) {
            $desain = $desain->where('title', 'like', '%'. request()->q . '%');
        })->paginate(10);

        return view('admin.desain.index', compact('desain'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.desain.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'link'          => 'required|unique:desains',

        ]);

         $desain = new Desain();
        //  $desain->title = $request->get('title');
         $desain->link= $request->get('link');



        //upload image
        if ($request->hasFile('image')) {
             // $post->delete_image();
             $image = $request->file('image');
             // echo $photo_profile;exit;
             $name = rand(1000, 9999) . $image->hashName();
             $image->move('img', $name);
             $desain->image = $name;
         }
         $desain->save();

        //  dd($desain);

        if($desain){
             //redirect dengan pesan sukses
             return redirect()->route('admin.desain.index')->with(['success' => 'Data Berhasil Disimpan!']);
         }else{
             //redirect dengan pesan error
             return redirect()->route('admin.desain.index')->with(['error' => 'Data Gagal Disimpan!']);
         }
    }

    /**
     * Display the specified resource.
     */
    public function show(Desain $desain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Desain $desain)
    {
        //
        return view('admin.desain.edit',compact('desain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desain $desain,$id)
    {
        //
        $this->validate($request, [
            // 'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
            // 'link'          => 'required|unique:desains',

        ]);

        $desain = Desain::findOrFail($id);
        $desain->link = $request->get('link');
        // $desain->content= $request->get('content');

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
            $desain->image = $name;
            // dd($category);
        }
        $desain->save();
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
        //
        $desain = Desain::findOrFail($id);
        //dd($fiturs);
        $image_path = public_path("/img/") .$desain->image;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        else{
            $desain->delete();
            //abort(404);
        }

        $desain->delete();

        if($desain){
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

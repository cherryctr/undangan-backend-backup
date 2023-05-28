<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Storage;
use File;

class SliderController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $sliders = Slider::latest()->paginate(5);
        return view('admin.slider.index', compact('sliders'));
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
            'link'     => 'required',

        ]);

        //upload image
        $slider = new Slider();
        $slider->image = $request->get('image');
        $slider->link = $request->get('link');


        if ($request->hasFile('image')) {
            // $post->delete_image();
            $image = $request->file('image');
            // echo $photo_profile;exit;
            $name = rand(1000, 9999) . $image->hashName();
            $image->move('img', $name);
            $slider->image = $name;
        }
        $slider->save();


        if($slider){
             //redirect dengan pesan sukses
             return redirect()->route('admin.slider.index')->with(['success' => 'Data Berhasil Disimpan!']);
         }else{
             //redirect dengan pesan error
             return redirect()->route('admin.slider.index')->with(['error' => 'Data Gagal Disimpan!']);
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
        $slider = Slider::findOrFail($id);
        $image_path = public_path("/img/") .$slider->image;

        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        else{
            $slider->delete();
            //abort(404);
        }

        $slider->delete();

        if($slider){
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

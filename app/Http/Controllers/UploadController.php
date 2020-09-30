<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Faker\Provider\File;
use Faker\Provider\Image;
use Illuminate\Http\Request;



class UploadController extends Controller
{
   public $imagePath = '';
   public $thumbnailPath = '';

    /**
     * Upload form
     */
   public function getUploadForm()
   {
       $images = Images::get();
       return view('upload-image',compact('images'));
   }

    /**
     * @function CreateDirectory
     *
     */
   public function createDirecrotory()
   {
       $paths = [
           'image_path' => public_path('images/'),
           'thumbnail_path' => public_path('images/thumbs/')
       ];

       foreach ($paths as $key => $path){
           if (!File::isDirectory($path)){
               File::makeDirectory($path,0777, true,true);
           }
       }

       $this->imagePath = $paths['image_path'];
       $this->thumbnailPath = $paths['thumbnail_path'];

   }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */

   public function postUploadForm(Request $request)
   {
       $request->validate([
          'upload.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
       ]);

       if($request->hasFile('upload')){
           $this->createDirecrotory();

           foreach ($request->upload as $file){

               $image = \Intervention\Image\Facades\Image::make($file);
               $imageName = time().'-'.$file->getClientOriginalName();

               $image->save($this->imagePath.$imageName);

               $image->resize(150,150);
               $image->save($this->thumbnailPath.$imageName);

               $upload = new Images();
               $upload->file = $imageName;
               $upload->save();
           }

           return back()-with('success','Your images has been uploaded');
       }
   }
}

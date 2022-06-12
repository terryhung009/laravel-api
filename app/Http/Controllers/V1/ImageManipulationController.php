<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResizeImageRequest;
use App\Http\Resources\V1\ImageManipulationResource;
use App\Models\Album;
use App\Models\ImageManipulation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageManipulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function byAlbum(Album $album)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImageManipulationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resize(ResizeImageRequest $request)
    {
        $all = $request->all();

        /** @Var UploadedFile|string $image
         * 
         * 
         * 
         * 
         * 
         */


        $image = $all['image'];
        unset($all['image']);
        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => null,
        ];

        if (isset($all['album_id'])) {
            //TODO

            $data['album_id'] = $all['album_id'];
        }

        $dir = 'images/' . Str::random() . '/';
        $absolutePath = public_path($dir);
        File::makeDirectory($absolutePath);


        //image/dash2324dsaf/test.jpg
        //image/dash2324dsaf/test-resized.jpg
        if ($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName();

            $filename = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absolutePath . $data['name'];

            $data['path'] = $dir . $data['name'];
            $image->move($absolutePath, $data['name']);
            
        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absolutePath . $data['name'];

            // copy($image, $absolutePath.$data['name']);
            copy($image, $originalPath);
            $data['path'] = $dir . $data['name'];
        }
        

        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($image, $width, $height ) = $this->getImageWidthAndHeight($w, $h, $originalPath);

        // echo '<pre>';
        // var_dump($width,$height);
        // echo '</pre>';
        // exit;

        $resizedFilename = $filename . '-resized.' . $extension;

        $image-> resize($width, $height)->save($absolutePath . $resizedFilename);

        $data['output_path'] = $dir . $resizedFilename;

        $ImageManipulation =  ImageManipulation::create($data);

        // dd($ImageManipulation);

        return $ImageManipulation;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function show(ImageManipulation $imageManipulation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateImageManipulationRequest  $request
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateImageManipulationRequest $request, ImageManipulation $imageManipulation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageManipulation $imageManipulation)
    {
        //
    }

    protected function getImageWidthAndHeight($w, $h, string $originalPath)
    {
        $image =  Image::make($originalPath);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if (str_ends_with($w, '%')) {
            $ratioW = (float)(str_replace('%', '', $w));
            $ratioH = $h ? (float)(str_replace('%', '', $h)) : $ratioW;

            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioW / 100;
        } else {
            $newWidth = (float)$w;
            /**
             * $originalWidth  -  $newWidth
             * $originalHeight -  $newHeight
             * -----------------------------
             * $newHeight =  $originalHeight * $newWidth/$originalWidth
             */

            // $newHeight =  $originalHeight * $newWidth/$originalWidth;
            $newHeight = $h ? (float)$h : ($originalHeight * $newWidth / $originalWidth);
        }

        return [$newWidth, $newHeight, $image];
    }
}

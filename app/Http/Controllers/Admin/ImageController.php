<?php

namespace App\Http\Controllers\Admin;

use App\Models\CommonImage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\CustomHelper;
use Illuminate\Validation\Rule;

use Validator;
use DB;
use Storage;
use Hash;

class ImageController extends Controller {

    private $limit;

    private $imgPath;
    private $thumbPath;

    private $ADMIN_ROUTE_NAME;

    private static $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');

    public function __construct(){
        //die;
        $this->limit = 20;

        $folderName = (request()->has('tbl'))?request('tbl'):'';

        $this->imgPath = $folderName.'/';
        $this->thumbPath = "$folderName/thumb/";

        //prd($this->imgPath);

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request){
        //pr($request->toArray());
        $data = [];

        $keyword = (isset($request->keyword))?$request->keyword:'';

        $data['page_heading'] = 'Images';

        return view('admin.images.index', $data);
    }

    public function upload(Request $request){
        //prd($request->toArray());
        $data = [];

        $tbl_id = (isset($request->tid))?$request->tid:0;
        $tbl_name = (isset($request->tbl))?$request->tbl:'';

        if(!empty($tbl_name)){

            $images = CommonImage::where(['tbl_id'=>$tbl_id, 'tbl_name'=>$tbl_name])->get();

            $path = $this->imgPath;
            $thumb_path = $this->thumbPath;

            $data['page_heading'] = 'Upload Images';
            $data['tbl_id'] = $tbl_id;
            $data['tbl_name'] = $tbl_name;
            $data['images'] = $images;
            $data['path'] = $path;
            $data['thumb_path'] = $thumb_path;

            return view('admin.images.upload', $data);
        }

        return redirect(url($this->ADMIN_ROUTE_NAME));
    }

    /* ajax_check_exist */
    public function ajaxCheckExist(Request $request){
        //prd($request->toArray());

        if($request->method() == 'POST' || $request->method() == 'post'){

            $filename = (isset($request->filename))?$request->filename:'';

            $storage = Storage::disk('public');

            $path = $this->imgPath;
            $thumb_path = $this->thumbPath;

            if(!empty($filename) && $storage->exists($path.$filename)){
                return 1;
            }
        }
        return 0;
    }

    /* ajax_upload */
    public function ajaxUpload(Request $request){

        $response = [];
        $is_inserted = '';
        $response['success'] = false;

        $status_code = 503;

        if($request->method() == 'POST' || $request->method() == 'post'){

            $tbl_id = (isset($request->tid))?$request->tid:0;
            $tbl_name = (isset($request->tbl))?$request->tbl:'';

            $file = ($request->hasFile('Filedata'))?$request->file('Filedata'):'';
            //prd($file);
            if ($file && !empty($tbl_name)) {

                $path = $this->imgPath;
                $thumb_path = $this->thumbPath;

                //pr($path);
                //prd($thumb_path);

                $IMG_HEIGHT = CustomHelper::WebsiteSettings('COMMON_IMG_HEIGHT');
                $IMG_WIDTH = CustomHelper::WebsiteSettings('COMMON_IMG_WIDTH');
                $THUMB_HEIGHT = CustomHelper::WebsiteSettings('COMMON_THUMB_HEIGHT');
                $THUMB_WIDTH = CustomHelper::WebsiteSettings('COMMON_THUMB_WIDTH');

                $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1600;
                $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:640;
                $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:400;
                $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:400;

                $images_data = [];

                $ext = 'jpg,jpeg,png';

                $upload_result = CustomHelper::UploadImage($file, $path, $ext, $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

                //prd($upload_result);
                if($upload_result['success']){
                    $imagesData = [];
                    $imagesData['tbl_id'] = $tbl_id;
                    $imagesData['tbl_name'] = $tbl_name;
                    $imagesData['name'] = $upload_result['file_name'];

                    $isSaved = CommonImage::create($imagesData);

                    if($isSaved){

                        $storage = Storage::disk('public');

                        $viewData = [];
                        $viewData['image'] = $isSaved;
                        $viewData['storage'] = $storage;
                        $viewData['path'] = $path;
                        $viewData['thumb_path'] = $thumb_path;

                        //prd($viewData);

                        $imgRow = view('admin.images._row', $viewData)->render();

                        $response['success'] = true;
                        $response['imgRow'] = $imgRow;
                        $status_code = 200;
                    }
                }

            }
            return response()->json($response, $status_code);

        }

    }

    /* ajax_delete_images */
    public function ajaxDeleteImages(Request $request){
        //prd($request->toArray());

        $response = [];
        $response['success'] = false;

        $img = (isset($request->img))?$request->img:'';

        if(!empty($img)){

            $storage = Storage::disk('public');

            $path = $this->imgPath;
            $thumb_path = $this->thumbPath;

            $is_deleted = '';

            if($storage->exists($path.$img)){
                $is_deleted = $storage->delete($path.$img);
            }
            if($storage->exists($thumb_path.$img)){
                $is_deleted = $storage->delete($thumb_path.$img);
            }

            if($is_deleted){
                $response['success'] = true;
            }
        }

        return response()->json($response);
    }

    /* ajax_update */
    public function ajaxUpdate(Request $request){
        //prd($request->toArray());

        $response = [];
        $response['success'] = false;

        $idArr = (isset($request->ids))?$request->ids:'';

        $titleArr = (isset($request->title))?$request->title:'';
        $linkArr = (isset($request->link))?$request->link:'';
        $sortOrderArr = (isset($request->sort_order))?$request->sort_order:0;

        $isSaved = false;

        if(!empty($idArr) && count($idArr) > 0){
            foreach($idArr as $id){
                $image = CommonImage::find($id);

                if(isset($image->id) && $image->id == $id){

                    $title = (isset($titleArr[$id]))?$titleArr[$id]:'';
                    $link = (isset($linkArr[$id]))?$linkArr[$id]:'';
                    $sort_order = (isset($sortOrderArr[$id]))?$sortOrderArr[$id]:0;

                    $image->title = $title;
                    $image->link = $link;
                    $image->sort_order = $sort_order;

                    //prd($image->toArray());

                    $isSaved = $image->save();
                }
            }
        }

        if($isSaved){

            $response['success'] = true;

            session()->flash('alert-success', 'Image(s) has been updated successfully.');
        }

        return response()->json($response);
    }

    /* ajax_delete */
    public function ajaxDelete(Request $request){
        //prd($request->toArray());

        $response = [];
        $response['success'] = false;

        $id = (isset($request->id))?$request->id:0;

        if(is_numeric($id) && $id > 0){

            $image = CommonImage::find($id);

            if(isset($image->id) && $image->id == $id){

                $storage = Storage::disk('public');

                $img = $image->name;

                $path = $image->tbl_name."/";
                $thumb_path = "$path/thumb/";

                $is_deleted = $image->delete();

                if($is_deleted){

                    if($storage->exists($path.$img)){
                        $storage->delete($path.$img);
                    }
                    if($storage->exists($thumb_path.$img)){
                        $storage->delete($thumb_path.$img);
                    }

                    $response['success'] = true;

                    session()->flash('alert-success', 'Image has been deleted successfully.');
                }
            }
            
        }

        return response()->json($response);
    }

/* End of controller */
}

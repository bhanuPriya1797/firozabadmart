<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImageCategory;
use App\Models\TableToDomain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Storage;
use App\Helpers\CustomHelper;
use DB;
use Image;

class ImageCategoryController extends Controller{

    private $limit;
    private $ADMIN_ROUTE_NAME;

    public function __construct(){
        $this->limit = 20;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;
        
        $parent_id = (isset($request->parent_id))?$request->parent_id:0;
        $name = (isset($request->name))?$request->name:'';
        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';
        $language = (isset($request->language))?$request->language:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');
        $category_query = ImageCategory::orderBy('id', 'desc');

        if(is_numeric($parent_id) && $parent_id > 0){
            $category_query->where('parent_id', $parent_id);
        }
        else{
            $category_query->where(function($query){
                $query->where('parent_id', 0);
                $query->orWhereNull('parent_id');
            });
        }

            if(!empty($name)){
                $category_query->where('name', 'like', '%'.$name.'%');
            }

            if(!empty($language)){
                $category_query->where('language', $language);
            }

            if( strlen($status) > 0 ){
                $category_query->where('status', $status);
            }

            if(!empty($from_date)){
                $category_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
            }

            if(!empty($to_date)){
                $category_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
            }

            $categories = $category_query->paginate($limit);


            $data['categories'] = $categories;
            $data['limit'] = $limit;
            //prd($categories->toArray());
        return view('admin.images.image_categories', $data);

    }


    public function add(Request $request){
        //prd($request->toArray());
        $data = [];
        $id = (isset($request->id))?$request->id:0;
        $parent_id = (isset($request->parent_id))?$request->parent_id:0;

        $category = '';
        $user = auth()->user();

        if(is_numeric($id) && $id > 0){
            $category = ImageCategory::find($id);
        }

        if($request->method() == 'POST' || $request->method() == 'post'){
            //prd($request->toArray());
            $ext = 'jpg,jpeg,png,gif';

            $back_url = (isset($request->back_url))?$request->back_url:'';

            if(empty($back_url)){
                $back_url = $this->ADMIN_ROUTE_NAME.'/image_categories';
            }

            $req_id = (isset($request->id))?$request->id:0;
            $old_image = (isset($request->old_image))?$request->old_image:'';
            $rules = [];

            //$rules['hindi_name'] = 'required';
            $rules['name'] = 'required';
            $rules['status'] = 'required';
            //$rules['domain'] = 'required';
            //$rules['domain.*'] = 'required|numeric';

            //$rules['image'] = 'nullable|image|mimes:'.$ext;
            /*if(empty($old_image))
            {
                $rules['image'] = 'required|image|mimes:'.$ext;
            }*/

            $this->validate($request, $rules);

            $req_data = [];

            $req_data = $request->except(['_token', 'id', 'image', 'old_image','back_url','domain']);
            
            $slug = CustomHelper::GetSlug('image_categories', 'id', $id, $request->name);
            $req_data['slug'] = $slug;
            $req_data['featured'] = (isset($request->featured)) ? 1:0;
            $req_data['parent_id'] = (isset($request->parent_id)) ? $request->parent_id:0;
        
            if(!empty($category) && $category->count() > 0 && $req_id == $id){
                $isSaved = ImageCategory::where(['id'=>$category->id])->update($req_data);
                //prd($isSaved);
            }
            else{
                //prd($req_data);
                $isSaved = ImageCategory::create($req_data);
                $id = $isSaved->id;
            }
            
            if ($isSaved) {

                //$this->saveDomains($request, $id);

                if($request->hasFile('image')) {
                    $file = $request->file('image');
                    $image_result = $this->saveImage($file,$id);
                    if($image_result['success'] == false){     
                        session()->flash('alert-danger', 'Image could not be added');
                    }
                }

                return redirect(url($back_url))->with('alert-success', 'Image Category has been saved successfully.');
            } else {
                return back()->with('alert-danger', 'Image Category cannot be added, please try again or contact the administrator.');
            }
        }
        
        $page_heading = 'Add Album';
        if(isset($category->name)){
        $page_heading = 'Edit Album - '.$category->name;
        }

        if(is_numeric($parent_id) && $parent_id > 0){
            $parentData = ImageCategory::find($parent_id);
            $page_heading = 'Add Album('.$parentData->name." )";
        }

        
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['category'] = $category;
        $data['parent_id'] = $parent_id;
        $data['parent_id'] = $parent_id;

        

        return view('admin.images.image_categories_form', $data);

    }


    public function saveImage($file, $id){
        //prd($file); 
        //echo $id; die;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) 
        {
            $path = 'gallery/';
            $thumb_path = 'gallery/thumb/';

            $IMG_HEIGHT = CustomHelper::WebsiteSettings('COMMON_IMG_HEIGHT');
            $IMG_WIDTH = CustomHelper::WebsiteSettings('COMMON_IMG_WIDTH');
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings('COMMON_THUMB_HEIGHT');
            $THUMB_WIDTH = CustomHelper::WebsiteSettings('COMMON_THUMB_WIDTH');

            $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:768;
            $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:768;
            $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:336;
            $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:336;

            $uploaded_data = CustomHelper::UploadImage($file, $path, $ext='', $IMG_WIDTH, $IMG_HEIGHT, $is_thumb=true, $thumb_path, $THUMB_WIDTH, $THUMB_HEIGHT);

            if($uploaded_data['success']){
                $new_image = $uploaded_data['file_name'];

                if(is_numeric($id) && $id > 0){
                    $video = ImageCategory::find($id);

                    if(!empty($video) && $video->count() > 0){

                        $storage = Storage::disk('public');

                        $old_image = $video->image;

                        $video->image = $new_image;

                        $isUpdated = $video->save();

                        if($isUpdated){

                            if(!empty($old_image) && $storage->exists($path.$old_image)){
                                $storage->delete($path.$old_image);
                            }

                            if(!empty($old_image) && $storage->exists($thumb_path.$old_image)){
                                $storage->delete($thumb_path.$old_image);
                            }
                        }
                    }
                }
            }

            if(!empty($uploaded_data))
            {   
                return $uploaded_data;
            }
        }
    }
    

    public function ajax_delete_image(Request $request){
        //prd($request->toArray());
        $result['success'] = false;

        $image_id = ($request->has('image_id'))?$request->image_id:0;

        if (is_numeric($image_id) && $image_id > 0) {
            $is_img_deleted = $this->delete_images($image_id);
            if($is_img_deleted)
            {
                $result['success'] = true;
                $result['msg'] = '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Image has been delete successfully.</div>';
            }
        }

        if($result['success'] == false){
            $result['msg'] = '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Something went wrong, please try again.</div>';
        }
        return response()->json($result);
    }

    public function delete_images($id){

        $is_deleted = '';
        $is_updated = '';
        $storage = Storage::disk('public');
        $path = 'gallery/';
        $video = ImageCategory::find($id);
        
        $image = (isset($video->image))?$video->image:'';
        if(!empty($image) && $storage->exists($path.'thumb/'.$image))
        {
            $is_deleted = $storage->delete($path.'thumb/'.$image);
        }
        if(!empty($image) && $storage->exists($path.$image))
        {
            $is_deleted = $storage->delete($path.$image);
        }
        if($is_deleted){
           $video->image = '';
           $is_updated = $video->save();

       }
       return $is_updated;
   }


    public function delete($id){
        $category = ImageCategory::find($id);
        if ($category->images() && $category->images()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->videos()->count() . ' images associated with it. Please remove the videos first.');
        }
        // The Category must not have any associated Sub-categories to be deleted
        if ($category->children() && $category->children()->count() > 0) {
            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->children()->count() . ' images sub-categories associated with it. Please remove the video sub-categories first.');
        }
        else {      
            $category->delete();

            return back()->with('alert-success', 'The category has been removed successfully.');
        }
    }

    /* end of controller */
}
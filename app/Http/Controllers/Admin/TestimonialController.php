<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use App\Helpers\CustomHelper;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use DB;
use Auth;

use Validator;
use Storage;
use Carbon\Carbon;


class TestimonialController extends Controller{

    private $limit;
    private $ADMIN_ROUTE_NAME;
    protected $currentUrl;

    public function __construct(){
        $this->limit = 20;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        $this->currentUrl = url()->current();
    }

    public function index(Request $request){
        $data = [];
        $limit = $this->limit;
        $id = (isset($request->id))?$request->id:0;
        $name = isset($request->name) ? $request->name : "";
        $status = isset($request->status) ? $request->status : "";

        $testimonial_query = Testimonial::orderBy('id', 'desc');
        if (!empty($name)) {
            $testimonial_query->where("name", "like", "%" . $name . "%");
        }
        if (strlen($status) > 0) {
            $testimonial_query->where("status", $status);
        }

        $testimonials = $testimonial_query->paginate($limit);

        $data['testimonials'] = $testimonials;

        return view('admin.testimonials.index', $data);

    }

   public function add(Request $request)
    {
        $id = isset($request->id) ? $request->id : 0;
        $testimonial = [];
        $title = "Add Testimonial";

        if (is_numeric($id) && $id > 0) {
            $testimonial = Testimonial::find($id);
            $title =
                "Edit Testimonial(" . $testimonial->name . ")";
        }
        if ($request->method() == "POST" || $request->method() == "post") {
            $ext = "jpg,jpeg,png,gif";

            $rules['name'] = 'required|max:255';
            $rules['description'] = 'required';
            $rules["image"] = "nullable|image|mimes:" . $ext;

            $this->validate($request, $rules);

            $req_data = [];
            $req_data = $request->except([
                "_token",
                "back_url",
                "image",
                "old_image",
                "id",
                "featured",
            ]);
            $req_data['featured'] = isset($request->featured)
                ?($request->featured):0;
                
            $date_on = (isset($request->date_on))?$request->date_on:'';
            $date = CustomHelper::DateFormat($date_on, 'Y-m-d H:i:s', 'd/m/y');
            $req_data['date_on'] = (!empty($date))?$date:Carbon::now()->toDateTimeString();
            $req_data['updated_at'] = Carbon::now()->toDateTimeString();
            $req_data['package_id'] = (isset($request->package_id) && !empty($request->package_id)) ? json_encode($request->package_id) : '[]';
            if (!empty($testimonial) && $testimonial->id == $id) {
                $isSaved = Testimonial::where(
                    "id",
                    $testimonial->id
                )->update($req_data);
                $msg = "Testimonial has been updated successfully.";

                $description = json_encode($req_data);
                $function_name = $this->currentUrl;
                $action_table = "testimonials";
                $row_id = $id;
                $action_type = "Edit On Testimonial";
                $action_description = "Edit On (" . $request->name . ")";
                $description =
                    "Update(" . $request->name . ") " . $description;
            } else {
                $req_data['created_at'] = Carbon::now()->toDateTimeString();
                $isSaved = Testimonial::create($req_data);
                $id = $isSaved->id;
                $msg = "Testimonial has been added successfully.";

                $description = json_encode($req_data);
                $function_name = $this->currentUrl;
                $action_table = "testimonials";
                $row_id = $id;
                $action_type = "Add On Testimonial";
                $action_description = "Add On (" . $request->name . ")";
                $description =
                    "Add(" . $request->name . ") " . $description;
            }

            if ($isSaved) {
                if ($request->hasFile("image")) {
                    $file = $request->file("image");
                    $image_result = $this->saveImage($file, $id, "image");
                    if ($image_result["success"] == false) {
                        session()->flash(
                            "alert-danger",
                            "Image could not be added"
                        );
                    }
                }

                cache()->forget("testimonials");

                CustomHelper::recordActionLog(
                    $function_name,
                    $action_table,
                    $row_id,
                    $action_type,
                    $action_description,
                    $description
                );

                return redirect(
                    url($this->ADMIN_ROUTE_NAME . "/testimonials")
                )->with("alert-success", $msg);
            } else {
                return back()->with(
                    "alert-danger",
                    "The Testimonial could be added, please try again or contact the administrator."
                );
            }
        }

        $data = [];
        $data["page_heading"] = $title;
        $data["testimonial"] = $testimonial;
        $data["id"] = $id;

        return view("admin.testimonials.form", $data);
    }

    public function view(Request $request)
    {
        $id = isset($request->id) ? $request->id : 0;
        $testimonial = "";
        $title = "Testimonial";

        if (is_numeric($id) && $id > 0) {
            $testimonial = Testimonial::where("id", $id)->first();
            //prd($testimonial);
            $title = "Testimonial";
        }

        $data = [];
        $data["page_heading"] = $title;
        $data["testimonial"] = $testimonial;
        $data["id"] = $id;
        return view("admin.testimonials.view", $data);
    }

// Add Image Uploading Code Here.......
    public function saveImage($file, $id, $type)
    {
        $result["org_name"] = "";
        $result["file_name"] = "";

        if ($file) {
            $path = "testimonials/";
            $thumb_path = "testimonials/thumb/";

            $IMG_HEIGHT = CustomHelper::WebsiteSettings(
                "Testimonials_IMG_HEIGHT"
            );
            $IMG_WIDTH = CustomHelper::WebsiteSettings(
                "Testimonials_IMG_WIDTH"
            );
            $THUMB_HEIGHT = CustomHelper::WebsiteSettings(
                "Testimonials_IMG_THUMB_WIDTH"
            );
            $THUMB_WIDTH = CustomHelper::WebsiteSettings(
                "Testimonials_IMG_THUMB_HEIGHT"
            );

            $IMG_WIDTH = !empty($IMG_WIDTH) ? $IMG_WIDTH : 768;
            $IMG_HEIGHT = !empty($IMG_HEIGHT) ? $IMG_HEIGHT : 768;
            $THUMB_WIDTH = !empty($THUMB_WIDTH) ? $THUMB_WIDTH : 336;
            $THUMB_HEIGHT = !empty($THUMB_HEIGHT) ? $IMG_WIDTH : 336;

            $uploaded_data = CustomHelper::UploadImage(
                $file,
                $path,
                $ext = "",
                $IMG_WIDTH,
                $IMG_HEIGHT,
                $is_thumb = true,
                $thumb_path,
                $THUMB_WIDTH,
                $THUMB_HEIGHT
            );

            if ($uploaded_data["success"]) {
                $new_image = $uploaded_data["file_name"];

                if (is_numeric($id) && $id > 0) {
                    $testimonial = Testimonial::find($id);

                    if (!empty($testimonial)) {
                        $storage = Storage::disk("public");

                        $old_image = $testimonial->image;
                        $testimonial->image = $new_image;

                        $isUpdated = $testimonial->save();

                        if ($isUpdated) {
                            if (
                                !empty($old_image) &&
                                $storage->exists($path . $old_image)
                            ) {
                                $storage->delete($path . $old_image);
                            }

                            if (
                                !empty($old_image) &&
                                $storage->exists($thumb_path . $old_image)
                            ) {
                                $storage->delete($thumb_path . $old_image);
                            }
                        }
                    }
                }
            }

            if (!empty($uploaded_data)) {
                return $uploaded_data;
            }
        }
    }

    public function ajax_delete_image(Request $request)
    {
        //prd($request->toArray());
        $result["success"] = false;

        $image_id = $request->has("image_id") ? $request->image_id : 0;
        $type = $request->has("type") ? $request->type : "";

        if (is_numeric($image_id) && $image_id > 0) {
            $is_img_deleted = $this->delete_images($image_id, $type);
            if ($is_img_deleted) {
                $result["success"] = true;
                $result["msg"] =
                    '<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Image has been delete successfully.</div>';
            }
        }

        if ($result["success"] == false) {
            $result["msg"] =
                '<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Something went wrong, please try again.</div>';
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $method = $request->method();

        //prd($id);
        $is_deleted = 0;
        $storage = Storage::disk("public");
        $path = "testimonials/";

        if ($method == "POST") {
            if (is_numeric($id) && $id > 0) {
                $testimonial = Testimonial::find($id);
                $function_name = $this->currentUrl;
                $action_table = "testimonials";
                $row_id = $id;
                $action_type = "Delete Testimonials";
                $action_description = "Delete (" . $testimonial->name . ")";
                $description = "Delete (" . $testimonial->name . ")";

                if (!empty($testimonial) && count([$testimonial]) > 0) {
                    if (
                        count([$testimonial]) > 0 &&
                        !empty($testimonial->image)
                    ) {
                        $image = $testimonial->image;
                        if (
                            !empty($image) &&
                            $storage->exists($path . "thumb/" . $image)
                        ) {
                            $is_deleted = $storage->delete(
                                $path . "thumb/" . $image
                            );
                        }
                        if (
                            !empty($image) &&
                            $storage->exists($path . $image)
                        ) {
                            $is_deleted = $storage->delete($path . $image);
                        }
                    }
                    $is_deleted = $testimonial->delete();
                }
            }
        }
        if ($is_deleted) {
            CustomHelper::recordActionLog(
                $function_name,
                $action_table,
                $row_id,
                $action_type,
                $action_description,
                $description
            );

            return redirect(
                url($this->ADMIN_ROUTE_NAME . "/testimonials")
            )->with(
                "alert-success",
                "The Testimonials has been deleted successfully."
            );
        } else {
            return redirect(
                url($this->ADMIN_ROUTE_NAME . "/testimonials")
            )->with(
                "alert-danger",
                "The Page cannot be deleted, please try again or contact the administrator."
            );
        }
    }

    public function delete_images($id, $type)
    {
        $is_deleted = "";
        $is_updated = "";
        $storage = Storage::disk("public");
        $path = "testimonials/";
        $testimonial = Testimonial::find($id);

        $image = isset($testimonial->image) ? $testimonial->image : "";

        if ($type == "image") {
            if (!empty($image) && $storage->exists($path . "thumb/" . $image)) {
                $is_deleted = $storage->delete($path . "thumb/" . $image);
            }
            if (!empty($image) && $storage->exists($path . $image)) {
                $is_deleted = $storage->delete($path . $image);
            }

            if ($is_deleted) {
                if ($type == "image") {
                    $testimonial->image = "";
                }
                $is_updated = $testimonial->save();
            }
        }
        return $is_updated;
    }

    
    /* end of controller */
}
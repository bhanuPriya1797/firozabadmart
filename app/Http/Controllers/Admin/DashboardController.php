<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Enquiry;
use App\Models\ActivityLog;
use App\Models\Destination;
use App\Models\TourPackage;
use App\Models\GalleryImage;
use App\Models\NewsletterSubscriber;
use App\Models\Blog;
use App\Models\Media; // Added for Media Manager integration
use Illuminate\Support\Facades\Storage; // Added for Media Manager integration

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use App\Helpers\CustomHelper;
use Validator;

use DB;
use Hash;
use Auth;

class DashboardController extends Controller {

	// Dashboard - URL: /admin
	public function index()
	{
	    $counts = [
	        'enquiries' => Enquiry::count(),
	        'newsletter' => NewsletterSubscriber::count(),
	        'gallery_images' => GalleryImage::count(),
	        // 'blogs' => Blog::count(),
            'news' => Blog::where('content_type', 'news')->count(),
            'events' => Blog::where('content_type', 'event')->count(),
	    ];
	    
	    $months = [];
	    $enquirySeries = [];
	    for ($i = 5; $i >= 0; $i--) {
	        $start = Carbon::now()->subMonths($i)->startOfMonth();
	        $end = Carbon::now()->subMonths($i)->endOfMonth();
	        $months[] = $start->format('M Y');
	        $enquirySeries[] = Enquiry::whereBetween('created_at', [$start, $end])->count();
	    }
	    
	    return view('admin.dashboard', [
	        'counts' => $counts,
	        'chartMonths' => $months,
	        'chartEnquiries' => $enquirySeries,
	    ]);
	}

	public function verify_password(Request $request){

		if($request->method() == 'POST'){
				//prd($request->toArray());

			$auth_user = auth()->guard('admin')->user();

			$message = [];
			$rules = [];

			$rules['password'] = 'required';

			$validator = Validator::make($request->all(), $rules, $message);

			$validator->after(function($validator) use ($auth_user){
				if (!Hash::check(request('password'), $auth_user->password)){
					$validator->errors()->add('password', 'Password did not matched!');
				}
				else{
					session(['verify_password'=>TRUE, 'verify_time'=>date('Y-m-d H:i:s')]);
				}
			});

			if ($validator->fails()){
				return back()->withErrors($validator);
			}
			elseif(!empty($back_url)){
				return redirect(url($back_url));
			}
			else{
				return back()->with('success', 'Password has been verified!');
			}
		}
		else{
			return back();
		}

	}

    /* ck_upload */
    public function ckUpload(Request $request){
        //pr(csrf_token());
        //prd($request->toArray());

        $response = [];

        $response['success'] = false;

        $type = (isset($request->type))?$request->type:'';

        if ($request->hasFile('upload')){

            $file = $request->file('upload');

            $path = 'ck';

            /*if(!empty($type)){
                $path = $type.'/'.'ck';
            }*/

            //UploadFile($file, $path, $ext='')

            $ext='jpg,jpeg,png,gif';

            $uploadResult = CustomHelper::UploadFile($file, $path, $ext);

            //prd($upload_result);

            if($uploadResult['success']){

            	$fileName = $uploadResult['file_name'];
                $storedPath = $uploadResult['file_path'] ?? '';

                // START: Media Manager Integration
                try {
                    $mediaPath = $storedPath;
                    // Ensure path is relative to public disk root (remove leading slashes or storage/ prefix if any)
                    // CustomHelper::UploadFile usually returns path relative to public disk, e.g. "ck/image.jpg"
                    
                    if(Storage::disk('public')->exists($mediaPath)){
                        $mime = Storage::disk('public')->mimeType($mediaPath);
                        $size = Storage::disk('public')->size($mediaPath);
                        
                        Media::create([
                            'name' => $fileName,
                            'file_name' => $fileName,
                            'path' => $mediaPath,
                            'disk' => 'public',
                            'mime_type' => $mime,
                            'size' => $size,
                            'folder_id' => null, // Uploaded to root or we could create a 'ck' folder
                            'user_id' => auth()->id() ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to register CKEditor upload to Media Manager: " . $e->getMessage());
                }
                // END: Media Manager Integration

                $src = storage_path('app/public/' . ltrim($storedPath, '/'));
                $publicDir = public_path('storage/'.$path);
                $publicDirAlt = public_path($path);
                if(!is_dir($publicDir)) { @mkdir($publicDir, 0777, true); }
                if(!is_dir($publicDirAlt)) { @mkdir($publicDirAlt, 0777, true); }
                if(is_file($src)) { @copy($src, $publicDir.'/'.$fileName); }
                
                $funcNum = $request->CKEditorFuncNum;
                // Optional: instance name (might be used to load a specific configuration file or anything else).
                $CKEditor = $request->CKEditor;
                // Optional: might be used to provide localized messages.
                $langCode = $request->langCode;

                // Check the $_FILES array and save the file. Assign the correct path to a variable ($url).
                //$url = $uploadResult['fileUrl'];

            	$url = asset('storage/'.$path.'/'.$fileName);
                // Usually you will only assign something here if the file could not be uploaded.
                $message = 'Image/file uploaded successfully.';

                echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
            }
            else{
                return response()->json($response);
            }
        }
    }
    public function ckBrowse(Request $request){
        return redirect()->route(\App\Helpers\CustomHelper::getAdminRouteName() . '.media.index', [
            'popup' => 1, 
            'CKEditorFuncNum' => $request->get('CKEditorFuncNum')
        ]);
    }
    public function ckUploadJson(Request $request){
        $result = ['success' => false];
        if ($request->hasFile('upload')){
            $file = $request->file('upload');
            $path = 'ck';
            $ext = 'jpg,jpeg,png,gif';
            $uploadResult = CustomHelper::UploadFile($file, $path, $ext);
            if($uploadResult['success']){
                $fileName = $uploadResult['file_name'];
                $storedPath = $uploadResult['file_path'] ?? '';

                // START: Media Manager Integration
                try {
                    $mediaPath = $storedPath;
                    if(Storage::disk('public')->exists($mediaPath)){
                        $mime = Storage::disk('public')->mimeType($mediaPath);
                        $size = Storage::disk('public')->size($mediaPath);
                        
                        Media::create([
                            'name' => $fileName,
                            'file_name' => $fileName,
                            'path' => $mediaPath,
                            'disk' => 'public',
                            'mime_type' => $mime,
                            'size' => $size,
                            'folder_id' => null,
                            'user_id' => auth()->id() ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to register CKEditor JSON upload to Media Manager: " . $e->getMessage());
                }
                // END: Media Manager Integration

                $src = storage_path('app/public/' . ltrim($storedPath, '/'));
                $publicDir = public_path('storage/'.$path);
                $publicDirAlt = public_path($path);
                if(!is_dir($publicDir)) { @mkdir($publicDir, 0777, true); }
                if(is_file($src)) { @copy($src, $publicDir.'/'.$fileName); }
                $url = asset('storage/'.$path.'/'.$fileName);
                return response()->json(['success'=>true,'url'=>$url]);
            }
            return response()->json($uploadResult);
        }
        return response()->json($result);
    }
    public function ckDelete(Request $request){
        $name = $request->input('name');
        if(!$name) return response()->json(['success'=>false,'message'=>'Missing filename'], 422);
        $dirPublic = public_path('storage/ck');
        $dirStorage = storage_path('app/public/ck');
        $deleted = false;
        $paths = [
            $dirPublic . '/' . $name,
            $dirStorage . '/' . $name,
            public_path('ck/' . $name),
        ];
        foreach($paths as $p){
            if(is_file($p)){
                @unlink($p);
                $deleted = true;
            }
        }
        return response()->json(['success'=>$deleted]);
    }
    /* end of controller */
}

<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CustomFieldController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SuccessStoryController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ArcherController;
use App\Http\Controllers\Admin\CircularController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ArcherAuthController;
use Illuminate\Support\Facades\DB; // Added for database testing
use Illuminate\Support\Facades\Artisan;

Route::get('phpartisan', function(){
    $cmd = request('cmd');
    if(!empty($cmd)){
        $exitCode = Artisan::call("$cmd");
    }
});

//=========Admin-Related-Routes===========
$ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
Route::match(['get', 'post'], $ADMIN_ROUTE_NAME.'/login', [LoginController::class, 'index'])->name($ADMIN_ROUTE_NAME . '.login');
// Route::post('ck_upload', 'HomeController@ckUpload')->name('ck_upload');

Route::get('/'.$ADMIN_ROUTE_NAME, function () use($ADMIN_ROUTE_NAME){
    return redirect()->route($ADMIN_ROUTE_NAME . '.login');
});

// Admin
    Route::group(['namespace' => 'Admin', 'prefix' => $ADMIN_ROUTE_NAME, 'as' => $ADMIN_ROUTE_NAME.'.', 'middleware' => ['authadmin']], function() {

    //==========logout - URL: /admin/logout============
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::match(['get','post'],'change-password', [AdminController::class, 'index'])->name('change_password');
        

    //=========Dashboard - URL: /admin==========
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('index')->middleware('permission:dashboard.view');
    Route::match(['get', 'post'], 'verify-password', [DashboardController::class, 'verify_password']);
    Route::post('ck-upload', [DashboardController::class, 'ckUpload'])->name('ck_upload');
    Route::get('ck-browse', [DashboardController::class, 'ckBrowse'])->name('ck_browse');
    Route::post('ck-upload-json', [DashboardController::class, 'ckUploadJson'])->name('ck_upload_json');
    Route::post('ck-delete', [DashboardController::class, 'ckDelete'])->name('ck_delete');

    

    //=========Add-Manage-Activity-Log============

    Route::group(['prefix' => 'activities', 'as' => 'activities'], function() {
        Route::get('/', [ActivityLogController::class, 'index'])->name('.index')->middleware('permission:activities.view');
        Route::match(['get', 'post'], 'view/{id}', [ActivityLogController::class, 'view'])->name('.view')->middleware('permission:activities.details');
    });

    //========Admins==========    
    Route::group(['prefix' => 'users', 'as' => 'users'], function() {
        Route::get('/', [AdminController::class, 'index'])->name('.index')->middleware('permission:users.view');
        Route::match(['get', 'post'], 'add/', [AdminController::class, 'add'])->name('.add')->middleware('permission:users.create');
        Route::match(['get', 'post'], 'edit/{id}', [AdminController::class, 'add'])->name('.edit')->middleware('permission:users.edit');
        Route::post('get-user', [AdminController::class, 'getUser'])->name('.getUser');
        Route::post('delete/{id}', [AdminController::class, 'delete'])->name('.delete')->middleware('permission:users.delete');
    });

    //========Profile========== 
    Route::group(['prefix' => 'profile', 'as' => 'profile'], function() {
        Route::get('/', [ProfileController::class, 'index'])->name('.index')->middleware('permission:profile.view');
        Route::post('/update', [ProfileController::class, 'update'])->name('.update')->middleware('permission:profile.edit');
    });

    //========Roles & Permissions========== 
    Route::group(['prefix' => 'roles', 'as' => 'roles'], function () {
        Route::get('/', [RolesController::class, 'index'])->name('.index')->middleware('permission:roles.view');
        Route::match(['get', 'post'], 'add/', [RolesController::class, 'save'])->name('.add')->middleware('permission:roles.create');
        Route::match(['get', 'post'], 'edit/{id}', [RolesController::class, 'save'])->name('.edit')->middleware('permission:roles.edit');
        Route::post('get-role', [RolesController::class, 'getRole'])->name('.getRole')->middleware('permission:roles.view');
        Route::post('delete/{id}', [RolesController::class, 'delete'])->name('.delete')->middleware('permission:roles.delete');
    });

    //========Settings========== 
    Route::group(['prefix' => 'settings', 'as' => 'settings'], function () {
        Route::get('/', [SettingsController::class, 'index'])->name('.index')->middleware('permission:settings.view');
        Route::post('/update', [SettingsController::class, 'update'])->name('.update')->middleware('permission:settings.edit');
        Route::post('/store', [SettingsController::class, 'store'])->name('.store')->middleware('permission:settings.edit');
        Route::match(['get','post'],'/regenerate-images', [SettingsController::class, 'regenerateImages'])->name('.regenerate-images')->middleware('permission:settings.edit');
    });

    //========CMS========== 
    Route::group(['prefix' => 'cms', 'as' => 'cms'], function () {
        Route::get('/', [CmsController::class, 'index'])->name('.index')->middleware('permission:cms.view');
        Route::get('/create', [CmsController::class, 'create'])->name('.create')->middleware('permission:cms.create');
        Route::post('/store', [CmsController::class, 'store'])->name('.store')->middleware('permission:cms.create');
        Route::get('/{id}/edit', [CmsController::class, 'edit'])->name('.edit')->middleware('permission:cms.edit');
        Route::put('/{id}/update', [CmsController::class, 'update'])->name('.update')->middleware('permission:cms.edit');
        Route::delete('/{id}', [CmsController::class, 'destroy'])->name('.destroy')->middleware('permission:cms.delete');
        Route::post('/{id}/delete-image', [CmsController::class, 'deleteImage'])->name('.delete-image')->middleware('permission:cms.edit');
        Route::post('/{id}/delete-custom-field-image', [CmsController::class, 'deleteCustomFieldImage'])->name('.delete-custom-field-image')->middleware('permission:cms.edit');
    });

    //========Custom Fields========== 
    Route::group(['prefix' => 'custom-fields', 'as' => 'custom_fields'], function () {
        Route::get('/', [CustomFieldController::class, 'index'])->name('.index')->middleware('permission:custom_fields.view');
        Route::get('/{id}/edit', [CustomFieldController::class, 'edit'])->name('.edit')->middleware('permission:custom_fields.edit');
        Route::post('/store', [CustomFieldController::class, 'store'])->name('.store')->middleware('permission:custom_fields.create');
        Route::post('/{id}/update', [CustomFieldController::class, 'update'])->name('.update')->middleware('permission:custom_fields.edit');
        Route::delete('/{id}', [CustomFieldController::class, 'destroy'])->name('.destroy')->middleware('permission:custom_fields.delete');
        Route::post('/delete-file', [CustomFieldController::class, 'deleteFile'])->name('.delete-file')->middleware('permission:custom_fields.edit');
    });

    //==========Menus===========
    Route::group(['prefix' => 'menus', 'as' => 'menus'], function() {

        Route::get('/', [MenuController::class, 'index'])->name('.index')->middleware('permission:menus.view');

        Route::match(['get', 'post'], 'add', [MenuController::class, 'add'])->name('.add')->middleware('permission:menus.create');
        Route::match(['get', 'post'], 'edit/{id}', [MenuController::class, 'add'])->name('.edit')->middleware('permission:menus.edit');
        Route::match(['get', 'post'], 'items/{id}/{item_id?}', [MenuController::class, 'items'])->name('.items')->middleware('permission:menu_items.manage');

        Route::post('ajax_get_link_type_list', [MenuController::class, 'ajaxGetLinkTypeList'])->name('.ajax_get_link_type_list');
        Route::post('ajax_update_items', [MenuController::class, 'ajaxUpdateItems'])->name('.ajax_update_items');
        Route::post('ajax_delete_item', [MenuController::class, 'ajaxDeleteItem'])->name('.ajax_delete_item');
        
        Route::post('ajax_delete_image', [MenuController::class, 'ajax_delete_image'])->name('.ajax_delete_image');
        Route::post('ajax_delete_element', [MenuController::class, 'ajaxDeleteElement'])->name('.ajax_delete_element');

        Route::post('delete/{id}', [MenuController::class, 'delete'])->name('.delete')->middleware('permission:menus.delete');
    });

    
    
    //===========Gallery============
    Route::prefix('gallery')->name('gallery')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('.index')->middleware('permission:gallery.view');
        Route::get('/folders/create', [GalleryController::class, 'createFolder'])->name('.folders.create')->middleware('permission:gallery.create');
        Route::post('/folders/store', [GalleryController::class, 'storeFolder'])->name('.folders.store')->middleware('permission:gallery.create');
        Route::get('/folders/{id}/edit', [GalleryController::class, 'editFolder'])->name('.folders.edit')->middleware('permission:gallery.edit');
        Route::put('/folders/{id}/update', [GalleryController::class, 'updateFolder'])->name('.folders.update')->middleware('permission:gallery.edit');
        Route::delete('/folders/{id}', [GalleryController::class, 'destroyFolder'])->name('.folders.destroy')->middleware('permission:gallery.delete');
        Route::get('/images', [GalleryController::class, 'imagesIndex'])->name('.images.index')->middleware('permission:gallery.view');
        Route::post('/images/upload', [GalleryController::class, 'uploadImages'])->name('.images.upload')->middleware('permission:gallery.create');
        Route::post('/images/upload-chunk', [GalleryController::class, 'uploadChunk'])->name('.images.upload-chunk')->middleware('permission:gallery.create');
        Route::post('/images/add-media-from-library', [GalleryController::class, 'addMediaFromLibrary'])->name('.images.addMediaFromLibrary')->middleware('permission:gallery.create');
        Route::get('/folders/{id}/images', [GalleryController::class, 'folderImages'])->name('.folders.images')->middleware('permission:gallery.view');
        Route::delete('/images/{id}', [GalleryController::class, 'destroyImage'])->name('.images.destroy')->middleware('permission:gallery.delete');
    });

    //=============Banners============
    Route::prefix('banners')->name('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('.index')->middleware('permission:banners.view');
        Route::match(['get', 'post'], 'add', [BannerController::class, 'add'])->name('.add')->middleware('permission:banners.create');
        Route::get('edit/{id}', [BannerController::class, 'edit'])->name('.edit')->middleware('permission:banners.edit');
        Route::post('save/{id?}', [BannerController::class, 'save'])->name('.save');
        Route::post('delete/{id}', [BannerController::class, 'delete'])->name('.delete')->middleware('permission:banners.delete');
        
        // Upload routes
        Route::post('upload-images', [BannerController::class, 'uploadImages'])->name('.uploadImages');
        Route::post('upload-video', [BannerController::class, 'uploadVideo'])->name('.uploadVideo');
        Route::post('add-media-from-library', [BannerController::class, 'addMediaFromLibrary'])->name('.addMediaFromLibrary');
        Route::post('delete-image', [BannerController::class, 'deleteImage'])->name('.deleteImage');
        
        // Media management routes
        Route::get('media/{id}', [BannerController::class, 'media'])->name('.media')->middleware('permission:banners.media_manage');
        Route::post('update-media', [BannerController::class, 'updateMedia'])->name('.updateMedia');
        Route::post('update-all-media', [BannerController::class, 'updateAllMedia'])->name('.updateAllMedia');
        Route::post('update-media-order', [BannerController::class, 'updateMediaOrder'])->name('.updateMediaOrder');
        
        // Video management routes
        Route::post('update-video-info', [BannerController::class, 'updateVideoInfo'])->name('.updateVideoInfo');
        Route::post('delete-video', [BannerController::class, 'deleteVideo'])->name('.deleteVideo');
        
        // Legacy routes for backward compatibility
        Route::match(['get', 'post'], 'add-old', [BannerController::class, 'add'])->name('.add-old');
        Route::match(['get', 'post'], 'edit-old/{banner_id}', [BannerController::class, 'add'])->name('.edit-old');
        Route::get('{banner_id}/images', [BannerController::class, 'images'])->name('.images');
        Route::post('{banner_id}/images/upload', [BannerController::class, 'uploadImages'])->name('.uploadImages-old');
        Route::post('ajax_delete_image', [BannerController::class, 'ajax_delete_image'])->name('.ajax_image_delete');
        Route::post('ajax_delete_video', [BannerController::class, 'ajax_delete_video'])->name('.ajax_delete_video');
    });

    //==========Blog-Categories===========
    Route::group(['prefix' => 'blogs-categories', 'as' => 'blogs_categories'], function() {

        Route::get('/', [BlogCategoryController::class, 'index'])->name('.index')->middleware('permission:blog_categories.view');
        Route::match(['get', 'post'], 'add', [BlogCategoryController::class, 'add'])->name('.add')->middleware('permission:blog_categories.create');
        Route::match(['get', 'post'], 'edit/{id}', [BlogCategoryController::class, 'add'])->name('.edit')->middleware('permission:blog_categories.edit');
        Route::get('categories-view/{id}', [BlogCategoryController::class, 'categories_view'])->name('.categories_view')->middleware('permission:blog_categories.view');
        Route::post('ajax_delete_image', [BlogCategoryController::class, 'ajax_delete_image'])->name('.ajax_delete_image');
        Route::match(['get', 'post'],'delete/{id}', [BlogCategoryController::class, 'delete'])->name('.delete')->middleware('permission:blog_categories.delete');
    });

    //==========Blogs===========

    Route::group(['prefix' => 'blogs', 'as' => 'blogs'], function() {
        
        Route::get('/', [BlogController::class, 'index'])->name('.index')->middleware('permission:blogs.view');
        Route::match(['get', 'post'], 'add', [BlogController::class, 'add'])->name('.add')->middleware('permission:blogs.create');
        Route::match(['get', 'post'], 'edit/{id}', [BlogController::class, 'add'])->name('.edit')->middleware('permission:blogs.edit');
        Route::match(['get','post'],'view/{id}', [BlogController::class, 'view'])->name('.view')->middleware('permission:blogs.view');
        Route::post('ajax_delete_image', [BlogController::class, 'ajax_delete_image'])->name('.ajax_delete_image');
        Route::match(['get', 'post'],'delete/{id}', [BlogController::class, 'delete'])->name('.delete')->middleware('permission:blogs.delete');
    });

    //==========News===========

    Route::group(['prefix' => 'news', 'as' => 'news'], function() {
        
        Route::get('/', [NewsController::class, 'index'])->name('.index')->middleware('permission:news.view');
        Route::match(['get', 'post'], 'add', [NewsController::class, 'add'])->name('.add')->middleware('permission:news.create');
        Route::match(['get', 'post'], 'edit/{id}', [NewsController::class, 'add'])->name('.edit')->middleware('permission:news.edit');
        Route::match(['get','post'],'view/{id}', [NewsController::class, 'view'])->name('.view')->middleware('permission:news.view');
        Route::post('ajax_delete_image', [NewsController::class, 'ajax_delete_image'])->name('.ajax_delete_image');
        Route::match(['get', 'post'],'delete/{id}', [NewsController::class, 'delete'])->name('.delete')->middleware('permission:news.delete');
    });

    //==========Events===========

    Route::group(['prefix' => 'events', 'as' => 'events'], function() {
        
        Route::get('/', [EventsController::class, 'index'])->name('.index')->middleware('permission:events.view');
        Route::match(['get', 'post'], 'add', [EventsController::class, 'add'])->name('.add')->middleware('permission:events.create');
        Route::match(['get', 'post'], 'edit/{id}', [EventsController::class, 'add'])->name('.edit')->middleware('permission:events.edit');
        Route::match(['get','post'],'view/{id}', [EventsController::class, 'view'])->name('.view')->middleware('permission:events.view');
        Route::post('ajax_delete_image', [EventsController::class, 'ajax_delete_image'])->name('.ajax_delete_image');
        Route::match(['get', 'post'],'delete/{id}', [EventsController::class, 'delete'])->name('.delete')->middleware('permission:events.delete');
    });

    //==========Circulars===========
    Route::group(['prefix' => 'circulars', 'as' => 'circulars'], function() {
        Route::get('/', [CircularController::class, 'index'])->name('.index')->middleware('permission:circulars.view');
        Route::get('add', [CircularController::class, 'add'])->name('.add')->middleware('permission:circulars.create');
        Route::get('edit/{id}', [CircularController::class, 'edit'])->name('.edit')->middleware('permission:circulars.edit');
        Route::post('save/{id?}', [CircularController::class, 'save'])->name('.save'); // gated by page-level permissions
        Route::post('delete/{id}', [CircularController::class, 'delete'])->name('.delete')->middleware('permission:circulars.delete');
    });

    //==========Calendar===========
    Route::group(['prefix' => 'calendar', 'as' => 'calendar'], function () {
        Route::get('/', [CalendarController::class, 'index'])->name('.index')->middleware('permission:calendar.view');
        Route::get('/create', [CalendarController::class, 'create'])->name('.create')->middleware('permission:calendar.create');
        Route::post('/', [CalendarController::class, 'store'])->name('.store')->middleware('permission:calendar.create');
        Route::get('/{id}/edit', [CalendarController::class, 'edit'])->name('.edit')->middleware('permission:calendar.edit');
        Route::put('/{id}', [CalendarController::class, 'update'])->name('.update')->middleware('permission:calendar.edit');
        Route::delete('/{id}', [CalendarController::class, 'destroy'])->name('.destroy')->middleware('permission:calendar.delete');
    });

    //==========Team Members===========
    Route::group(['prefix' => 'team-members', 'as' => 'team-members'], function () {
        Route::get('/', [TeamMemberController::class, 'index'])->name('.index')->middleware('permission:team_members.view');
        Route::get('/create', [TeamMemberController::class, 'create'])->name('.create')->middleware('permission:team_members.create');
        Route::post('/', [TeamMemberController::class, 'store'])->name('.store')->middleware('permission:team_members.create');
        Route::get('/{id}/edit', [TeamMemberController::class, 'edit'])->name('.edit')->middleware('permission:team_members.edit');
        Route::put('/{id}', [TeamMemberController::class, 'update'])->name('.update')->middleware('permission:team_members.edit');
        Route::delete('/{id}', [TeamMemberController::class, 'destroy'])->name('.destroy')->middleware('permission:team_members.delete');
    });

    //==========Achievements===========
    Route::group(['prefix' => 'achievements', 'as' => 'achievements'], function () {
        Route::get('/', [AchievementController::class, 'index'])->name('.index')->middleware('permission:achievements.view');
        Route::get('/create', [AchievementController::class, 'create'])->name('.create')->middleware('permission:achievements.create');
        Route::post('/', [AchievementController::class, 'store'])->name('.store')->middleware('permission:achievements.create');
        Route::get('/{id}/edit', [AchievementController::class, 'edit'])->name('.edit')->middleware('permission:achievements.edit');
        Route::put('/{id}', [AchievementController::class, 'update'])->name('.update')->middleware('permission:achievements.edit');
        Route::delete('/{id}', [AchievementController::class, 'destroy'])->name('.destroy')->middleware('permission:achievements.delete');
        Route::post('/toggle-status', [AchievementController::class, 'toggleStatus'])->name('.toggle-status')->middleware('permission:achievements.edit');
    });

    //===========Manage-Enquires=============
    Route::group(['prefix' => 'enquiries', 'as' => 'enquiries'], function() {
        Route::get('/', [EnquiryController::class, 'index'])->name('.index')->middleware('permission:enquiries.view');
        Route::post('/delete/{id}', [EnquiryController::class, 'delete'])->name('.delete')->middleware('permission:enquiries.delete');
        Route::get('/view/{id}', [EnquiryController::class, 'view'])->name('.view')->middleware('permission:enquiries.view');
        Route::post('/mark-read', [EnquiryController::class, 'markRead'])->name('.mark_read')->middleware('permission:enquiries.edit');
    });

    //===========Newsletter=============
    Route::group(['prefix' => 'newsletter', 'as' => 'newsletter'], function() {
        Route::get('/', [NewsletterController::class, 'index'])->name('.index')->middleware('permission:newsletter.view');
        Route::post('delete/{id}', [NewsletterController::class, 'delete'])->name('.delete')->middleware('permission:newsletter.delete');
        Route::get('export', [NewsletterController::class, 'exportXls'])->name('.export')->middleware('permission:newsletter.view');
    });
    
    

    //===========FAQ Categories=============
    Route::group(['prefix' => 'faq-categories', 'as' => 'faq-categories'], function() {
        Route::get('/', [FaqCategoryController::class, 'index'])->name('.index')->middleware('permission:faq_categories.view');
        Route::match(['get', 'post'], 'add/', [FaqCategoryController::class, 'add'])->name('.add')->middleware('permission:faq_categories.create');
        Route::match(['get', 'post'], 'edit/{id}', [FaqCategoryController::class, 'add'])->name('.edit')->middleware('permission:faq_categories.edit');
        Route::post('delete/{id}', [FaqCategoryController::class, 'delete'])->name('.delete')->middleware('permission:faq_categories.delete');
    });

    //===========FAQs=============
    Route::group(['prefix' => 'faqs', 'as' => 'faqs'], function() {
        Route::get('/', [FaqController::class, 'index'])->name('.index')->middleware('permission:faqs.view');
        Route::match(['get', 'post'], 'add/', [FaqController::class, 'add'])->name('.add')->middleware('permission:faqs.create');
        Route::match(['get', 'post'], 'edit/{id}', [FaqController::class, 'add'])->name('.edit')->middleware('permission:faqs.edit');
        Route::post('delete/{id}', [FaqController::class, 'delete'])->name('.delete')->middleware('permission:faqs.delete');
        Route::get('pages', [FaqController::class, 'ajaxPages'])->name('.ajax_pages');
    });

    //===========Blog Comments=============
    Route::group(['prefix' => 'blog-comments', 'as' => 'blog-comments'], function() {
        Route::get('/', [BlogCommentController::class, 'index'])->name('.index')->middleware('permission:blog_comments.view');
        Route::post('update-status', [BlogCommentController::class, 'updateStatus'])->name('.update-status')->middleware('permission:blog_comments.edit');
    });

    //===========Partners=============
    Route::group(['prefix' => 'partners', 'as' => 'partners'], function() {
        Route::get('/', [PartnerController::class, 'index'])->name('.index')->middleware('permission:partners.view');
        Route::get('add', [PartnerController::class, 'add'])->name('.add')->middleware('permission:partners.create');
        Route::get('edit/{id}', [PartnerController::class, 'edit'])->name('.edit')->middleware('permission:partners.edit');
        Route::post('save/{id?}', [PartnerController::class, 'save'])->name('.save');
        Route::post('delete/{id}', [PartnerController::class, 'delete'])->name('.delete')->middleware('permission:partners.delete');
    });

    //===========Success Stories=============
    Route::group(['prefix' => 'success-stories', 'as' => 'success-stories'], function() {
        Route::get('/', [SuccessStoryController::class, 'index'])->name('.index')->middleware('permission:success_stories.view');
        Route::get('/create', [SuccessStoryController::class, 'create'])->name('.create')->middleware('permission:success_stories.create');
        Route::post('/', [SuccessStoryController::class, 'store'])->name('.store')->middleware('permission:success_stories.create');
        Route::get('/{id}', [SuccessStoryController::class, 'show'])->name('.show')->middleware('permission:success_stories.view');
        Route::get('/{id}/edit', [SuccessStoryController::class, 'edit'])->name('.edit')->middleware('permission:success_stories.edit');
        Route::put('/{id}', [SuccessStoryController::class, 'update'])->name('.update')->middleware('permission:success_stories.edit');
        Route::delete('/{id}', [SuccessStoryController::class, 'destroy'])->name('.destroy')->middleware('permission:success_stories.delete');
        Route::match(['get', 'post'], 'add', [SuccessStoryController::class, 'create'])->name('.add')->middleware('permission:success_stories.create');
        Route::match(['get', 'post'], 'edit-old/{id}', [SuccessStoryController::class, 'edit'])->name('.edit-old')->middleware('permission:success_stories.edit');
        Route::post('delete-old/{id}', [SuccessStoryController::class, 'destroy'])->name('.delete')->middleware('permission:success_stories.delete');
    });

    //===========Destinations=============
    Route::group(['prefix' => 'destinations', 'as' => 'destinations'], function() {
        Route::get('/', [DestinationController::class, 'index'])->name('.index')->middleware('permission:destinations.view');
        Route::get('create', [DestinationController::class, 'create'])->name('.create')->middleware('permission:destinations.create');
        Route::post('store', [DestinationController::class, 'store'])->name('.store')->middleware('permission:destinations.create');
        Route::get('edit/{id}', [DestinationController::class, 'edit'])->name('.edit')->middleware('permission:destinations.edit');
        Route::post('update/{id}', [DestinationController::class, 'update'])->name('.update')->middleware('permission:destinations.edit');
        Route::post('destroy/{id}', [DestinationController::class, 'destroy'])->name('.destroy')->middleware('permission:destinations.delete');
        Route::post('delete-image', [DestinationController::class, 'deleteImage'])->name('.delete_image')->middleware('permission:destinations.edit');
        Route::post('duplicate/{id}', [DestinationController::class, 'duplicate'])->name('.duplicate')->middleware('permission:destinations.create');
        Route::post('delete-gallery-image', [DestinationController::class, 'deleteGalleryImage'])->name('.delete_gallery_image')->middleware('permission:destinations.edit');
        Route::post('upload-temp-image-chunk', [DestinationController::class, 'uploadTempImageChunk'])->name('.upload_temp_image_chunk')->middleware('permission:destinations.create');
        
        // Info sub-routes (placeholder if controller exists, otherwise these might fail if clicked, but won't crash app load)
        // Route::group(['prefix' => 'info', 'as' => 'info'], function() {
        //     Route::get('/{destination_id}', [DestinationInfoController::class, 'index'])->name('.index');
        // });
    });

    //===========Services=============
    Route::group(['prefix' => 'services', 'as' => 'services'], function() {
        Route::get('/', [ServiceController::class, 'index'])->name('.index');
        Route::match(['get', 'post'], 'add', [ServiceController::class, 'add'])->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', [ServiceController::class, 'add'])->name('.edit');
        Route::post('delete/{id}', [ServiceController::class, 'delete'])->name('.delete');
    });

    //===========Media=============
    Route::group(['prefix' => 'media', 'as' => 'media'], function() {
        Route::get('/', [MediaController::class, 'index'])->name('.index');
        Route::get('api/files', [MediaController::class, 'apiFiles'])->name('.api_files');
        Route::post('folder/create', [MediaController::class, 'createFolder'])->name('.folder.create');
        Route::post('api/folders', [MediaController::class, 'createFolder'])->name('.api_folders_create');
        Route::delete('api/folders', [MediaController::class, 'deleteFolder'])->name('.api_folders_delete');
        Route::delete('api/files', [MediaController::class, 'deleteFile'])->name('.api_files_delete');
        Route::put('api/files/alt', [MediaController::class, 'updateAltText'])->name('.api_files_alt');
        Route::post('api/files/chunk', [MediaController::class, 'uploadChunkGeneric'])->name('.api_files_chunk');
        Route::post('folder/delete', [MediaController::class, 'deleteFolder'])->name('.folder.delete');
        Route::post('file/delete', [MediaController::class, 'deleteFile'])->name('.file.delete');
        Route::post('file/update-alt', [MediaController::class, 'updateAltText'])->name('.file.update_alt');
        Route::post('upload-chunk', [MediaController::class, 'uploadChunkGeneric'])->name('.upload_chunk');
    });

    //===========Archers=============
    Route::group(['prefix' => 'archers', 'as' => 'archers'], function() {
        Route::get('/', [ArcherController::class, 'index'])->name('.index')->middleware('permission:archers.view');
        Route::get('show/{id}', [ArcherController::class, 'show'])->name('.show')->middleware('permission:archers.view');
        Route::get('edit/{id}', [ArcherController::class, 'edit'])->name('.edit')->middleware('permission:archers.edit');
        Route::post('update/{id}', [ArcherController::class, 'update'])->name('.update')->middleware('permission:archers.edit');
        Route::delete('destroy/{id}', [ArcherController::class, 'destroy'])->name('.destroy')->middleware('permission:archers.delete');
        Route::post('bulk-status', [ArcherController::class, 'bulkStatus'])->name('.bulk-status')->middleware('permission:archers.edit');
        Route::post('bulk-delete', [ArcherController::class, 'bulkDelete'])->name('.bulk-delete')->middleware('permission:archers.delete');
        Route::post('toggle-status', [ArcherController::class, 'toggleStatus'])->name('.toggle-status')->middleware('permission:archers.edit');
        Route::get('export', [ArcherController::class, 'export'])->name('.export')->middleware('permission:archers.export');
        Route::post('update-app-status', [ArcherController::class, 'updateAppStatus'])->name('.update-app-status')->middleware('permission:archers.edit');
    });

});

// Temp route for settings
Route::get('/setup-settings-temp', function() {
    $keys = ['logo', 'site_logo', 'header_logo', 'frontend_logo', 'footer_logo', 'middle_logo'];
    $settings = \App\Models\Setting::whereIn('key', $keys)->get();
    $results = [];

    foreach ($keys as $key) {
        $s = $settings->firstWhere('key', $key);
        if ($s) {
            $results[] = "Found $key: " . $s->type;
        } else {
            $results[] = "Missing $key";
            if ($key === 'middle_logo') {
                \App\Models\Setting::create([
                    'key' => 'middle_logo',
                    'label' => 'Header Middle Logo',
                    'group_name' => 'General',
                    'type' => 'file',
                    'is_fixed' => 0
                ]);
                $results[] = "Created middle_logo";
            }
             if ($key === 'footer_logo') {
                \App\Models\Setting::create([
                    'key' => 'footer_logo',
                    'label' => 'Footer Logo',
                    'group_name' => 'General',
                    'type' => 'file',
                    'is_fixed' => 0
                ]);
                $results[] = "Created footer_logo";
            }
        }
    }
    return response()->json($results);
});

require __DIR__.'/auth.php';

// Frontend Routes
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/circulars', [FrontendController::class, 'circulars'])->name('circulars.list');
Route::get('/achievements', [FrontendController::class, 'achievements'])->name('achievements.list');
Route::get('/archers', [FrontendController::class, 'archers'])->name('archers.list');
Route::post('/newsletter/subscribe', [FrontendController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::post('/contact-submit', [FrontendController::class, 'submitContact'])->name('contact.submit');
Route::get('/news', [FrontendController::class, 'news'])->name('news.index');
Route::get('/news/{slug}', [FrontendController::class, 'showNews'])->name('news.detail');
Route::get('/events', [FrontendController::class, 'events'])->name('events.index');
Route::get('/events/{slug}', [FrontendController::class, 'showEvent'])->name('events.detail');
Route::get('/calendar', [FrontendController::class, 'calendar'])->name('calendar');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/gallery/{slug}', [FrontendController::class, 'galleryDetail'])->name('gallery.detail');

Route::middleware('guest:archer')->group(function () {
    Route::get('/login', [ArcherAuthController::class, 'showLoginForm'])->name('archer.login');
    Route::post('/login', [ArcherAuthController::class, 'login'])->name('archer.login.submit');
    Route::get('/register', [ArcherAuthController::class, 'showRegisterForm'])->name('archer.register');
    Route::post('/register', [ArcherAuthController::class, 'register'])->name('archer.register.submit');
});

Route::prefix('archer')->name('archer.')->middleware('auth:archer')->group(function () {
    Route::get('/dashboard', [ArcherAuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [ArcherAuthController::class, 'logout'])->name('logout');
    Route::get('/applications', [ArcherAuthController::class, 'applications'])->name('applications.list');
});

// Catch-all CMS route (must be last)
Route::get('/{slug}', [FrontendController::class, 'cms'])->name('cms');

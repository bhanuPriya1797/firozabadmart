# Banner Management System

## Overview

A comprehensive banner management system for the IL Mission website with support for both image and video banners, featuring chunk upload functionality, progress bars, drag-and-drop ordering, and Vuexy theme integration.

## Features

### 🎨 **Banner Types**

-   **Image Banners**: Support for multiple images with titles, descriptions, and call-to-action buttons
-   **Video Banners**: Support for uploaded videos and embedded videos (YouTube, Vimeo, etc.)

### 📤 **Upload Features**

-   **Chunk Upload**: Large files uploaded in chunks with progress tracking
-   **Progress Bars**: Real-time upload progress for both images and videos
-   **Drag & Drop**: Intuitive drag-and-drop interface using Dropzone.js
-   **File Validation**: Automatic file type and size validation
-   **Thumbnail Generation**: Automatic thumbnail creation for images

### 🎯 **Management Features**

-   **DataTables Integration**: Server-side processing with search, sort, and pagination
-   **Media Management**: Separate page for managing individual media items
-   **Drag & Drop Ordering**: Reorder media items with drag-and-drop
-   **Bulk Operations**: Save all changes at once or update individual items
-   **Status Management**: Active/Inactive status control
-   **Sort Order**: Customizable display order
-   **Individual Media Deletion**: Delete specific media items

### 📝 **Content Management**

-   **Title & Description**: Edit titles and descriptions for each media item
-   **Button Configuration**: Set up to 2 call-to-action buttons per media item
-   **Link Management**: Configure button text and URLs
-   **Real-time Updates**: Save changes instantly with AJAX

## Installation & Setup

### 1. Required Packages

```bash
composer require pion/laravel-chunk-upload intervention/image
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider"
```

### 3. Storage Setup

```bash
php artisan storage:link
```

### 4. Database Migration

The banner tables should already exist. If not, run:

```bash
php artisan migrate
```

## File Structure

```
resources/views/admin/banners/
├── index.blade.php          # Banner listing with DataTables
├── form.blade.php           # Add/Edit banner form with upload
└── media.blade.php          # Media management page

app/Http/Controllers/Admin/
└── BannerController.php     # Main controller with all functionality

routes/web.php               # Banner management routes
```

## Usage

### Accessing Banner Management

1. **Login to Admin Panel**: `http://your-domain/administrator`
2. **Navigate to Banner Management**: Located in the sidebar under "Banner Management"
3. **Available Options**:
    - **All Banners**: View and manage existing banners
    - **Add Banner**: Create new banners

### Creating a Banner

#### Image Banner

1. Click "Add Banner"
2. Fill in basic information:
    - **Title**: Banner title (required)
    - **Type**: Select "Image Banner"
    - **Status**: Active/Inactive
    - **Sort Order**: Display order (optional)
3. Upload images using the drag-and-drop area
4. Save the banner
5. Click "Manage Media" to configure individual image details

#### Video Banner

1. Click "Add Banner"
2. Fill in basic information
3. Select "Video Banner" type
4. Choose video type:
    - **Upload Video**: Upload video file (max 100MB)
    - **Embed Video**: Provide embed URL (YouTube, Vimeo, etc.)
5. Upload video or enter embed URL
6. Save the banner

### Managing Banner Media

#### Accessing Media Management

1. From the banner listing, click the "Manage Media" button (photo icon)
2. This opens a dedicated page for managing all media items for that banner

#### Media Management Features

##### **Upload New Media**

-   **Image Upload**: Drag & drop or click to upload new images
-   **Video Upload**: Upload video files with chunk upload support
-   **Progress Tracking**: Real-time upload progress with progress bars

##### **Edit Media Details**

-   **Title**: Set the main title for each media item
-   **Description**: Add descriptive text for each media item
-   **Button 1**: Configure first call-to-action button
    -   Button Text: "Learn More", "Donate Now", etc.
    -   URL: Link to specific page or external URL
-   **Button 2**: Configure second call-to-action button
    -   Button Text: "Get Started", "Contact Us", etc.
    -   URL: Link to specific page or external URL

##### **Reorder Media Items**

-   **Drag & Drop**: Use the grip handle to drag items to new positions
-   **Visual Feedback**: See order numbers update in real-time
-   **Update Order**: Click "Update Order" to save the new sequence

##### **Save Changes**

-   **Individual Save**: Click the save button on each row to update that item
-   **Bulk Save**: Click "Save All Changes" to update all items at once
-   **Auto-save**: Changes are saved immediately with AJAX

##### **Delete Media Items**

-   **Individual Delete**: Click the delete button on any row
-   **Confirmation**: SweetAlert confirmation dialog
-   **File Cleanup**: Automatically removes files from storage

### Managing Banners

#### Banner Listing

-   **DataTables**: Search, sort, and paginate banners
-   **Status Badges**: Visual indicators for active/inactive banners
-   **Type Badges**: Distinguish between image and video banners
-   **Action Buttons**:
    -   **Edit**: Edit banner basic information
    -   **Manage Media**: Open media management page
    -   **Delete**: Delete banner and all media
-   **Formatted Dates**: Created dates shown in "M d, Y H:i" format

#### Editing Banners

1. Click the edit button on any banner
2. Modify banner information
3. Add/remove media files
4. Update banner settings
5. Save changes

#### Deleting Banners

1. Click the delete button on any banner
2. Confirm deletion in the popup dialog
3. Banner and all associated media will be permanently deleted

## Technical Details

### Database Schema

#### Banners Table

```sql
- id (Primary Key)
- title (VARCHAR)
- slug (VARCHAR)
- type (INT) - 1: Image, 2: Video
- video_type (INT) - 0: None, 1: Upload, 2: Embed
- video (VARCHAR) - Video filename
- video_embed (TEXT) - Embed URL
- status (INT) - 0: Inactive, 1: Active
- sort_order (INT)
- created_at, updated_at
```

#### Banner Images Table

```sql
- id (Primary Key)
- banner_id (Foreign Key)
- image_name (VARCHAR)
- title (VARCHAR)
- sub_title (TEXT)
- link_text_1 (VARCHAR)
- link_1 (VARCHAR)
- link_text_2 (VARCHAR)
- link_2 (VARCHAR)
- sort_order (INT)
- created_at, updated_at
```

### File Storage

#### Directory Structure

```
storage/app/public/banners/
├── [image-files].jpg/png/gif    # Original images
├── thumb/
│   └── [image-files].jpg/png/gif # Thumbnails (300x200)
└── [video-files].mp4/avi/mov    # Video files
```

#### File Naming Convention

-   **Images**: `timestamp_uniqueid.extension`
-   **Videos**: `timestamp_uniqueid.extension`
-   **Thumbnails**: Same as original with resized dimensions

### Upload Limits

#### Image Upload

-   **Max File Size**: 5MB per image
-   **Supported Formats**: JPG, PNG, GIF
-   **Thumbnail Size**: 300x200px (maintained aspect ratio)

#### Video Upload

-   **Max File Size**: 100MB per video
-   **Supported Formats**: MP4, AVI, MOV, WMV, FLV
-   **Chunk Size**: 1MB chunks for large files

### API Endpoints

#### Banner Management

```
GET    /administrator/banners              # List banners (DataTables)
GET    /administrator/banners/add          # Add banner form
GET    /administrator/banners/edit/{id}    # Edit banner form
POST   /administrator/banners/save/{id?}   # Save banner
POST   /administrator/banners/delete/{id}  # Delete banner
```

#### Media Management

```
GET    /administrator/banners/media/{id}           # Media management page
POST   /administrator/banners/upload-images        # Upload images
POST   /administrator/banners/upload-video         # Upload video
POST   /administrator/banners/delete-image         # Delete image
POST   /administrator/banners/update-media         # Update single media item
POST   /administrator/banners/update-all-media     # Update all media items
POST   /administrator/banners/update-media-order   # Update media order
```

## Frontend Integration

### Displaying Banners

The banners are automatically displayed on the frontend through the `FrontendController`:

```php
// In FrontendController::index()
$banners = Banner::where('status', 1)
    ->orderBy('sort_order', 'asc')
    ->take(5)
    ->get();
```

### Hero Component

The hero component (`resources/views/components/frontend/hero.blade.php`) displays banners dynamically:

```blade
@foreach($banners as $banner)
    @foreach($banner->images->sortBy('sort_order') as $image)
    <div class="hero-single" style="background: url('{{ asset('storage/banners/' . $image->image_name) }}')">
        <div class="hero-content">
            <h1>{{ $image->title }}</h1>
            <p>{{ $image->sub_title }}</p>
            @if($image->link_text_1)
                <a href="{{ $image->link_1 }}" class="btn btn-primary">{{ $image->link_text_1 }}</a>
            @endif
            @if($image->link_text_2)
                <a href="{{ $image->link_2 }}" class="btn btn-secondary">{{ $image->link_text_2 }}</a>
            @endif
        </div>
    </div>
    @endforeach
@endforeach
```

## Customization

### Adding New Banner Types

1. **Update Database**: Add new type values to the `type` field
2. **Update Controller**: Modify validation and processing logic
3. **Update Views**: Add UI elements for new banner type
4. **Update Frontend**: Modify display logic

### Modifying Upload Limits

1. **Controller Validation**: Update `uploadImages()` and `uploadVideo()` methods
2. **Frontend Validation**: Update Dropzone configuration
3. **Server Configuration**: Update PHP upload limits in `php.ini`

### Custom Styling

The banner management uses Vuexy theme classes. Customize by modifying:

-   **CSS Classes**: Update in `form.blade.php`, `index.blade.php`, and `media.blade.php`
-   **JavaScript**: Modify Dropzone configuration and form validation
-   **Icons**: Replace Tabler icons with custom icons

## Troubleshooting

### Common Issues

#### Upload Failures

1. **Check File Size**: Ensure files are within upload limits
2. **Check Permissions**: Verify storage directory permissions
3. **Check Disk Space**: Ensure sufficient server storage
4. **Check PHP Settings**: Verify `upload_max_filesize` and `post_max_size`

#### Image Processing Errors

1. **GD/Imagick**: Ensure PHP image processing extensions are installed
2. **Memory Limits**: Increase PHP memory limit for large images
3. **File Permissions**: Check write permissions for thumbnail directory

#### Video Upload Issues

1. **Chunk Upload**: Verify chunk upload configuration
2. **File Format**: Ensure video format is supported
3. **Server Timeout**: Increase timeout for large video uploads

#### Media Management Issues

1. **Sortable.js**: Ensure Sortable.js library is loaded
2. **AJAX Errors**: Check browser console for JavaScript errors
3. **CSRF Tokens**: Verify CSRF tokens are included in AJAX requests

### Debug Mode

Enable debug mode to see detailed error messages:

```php
// In .env file
APP_DEBUG=true
```

### Logs

Check Laravel logs for detailed error information:

```bash
tail -f storage/logs/laravel.log
```

## Security Considerations

### File Upload Security

-   **File Type Validation**: Only allow specific file types
-   **File Size Limits**: Prevent oversized uploads
-   **Virus Scanning**: Consider implementing virus scanning for uploads
-   **Secure Storage**: Store files outside web root when possible

### Access Control

-   **Authentication**: Ensure admin authentication is required
-   **Authorization**: Implement role-based access control
-   **CSRF Protection**: All forms include CSRF tokens
-   **Input Validation**: Validate all user inputs

## Performance Optimization

### Image Optimization

-   **Automatic Thumbnails**: Reduce bandwidth usage
-   **Image Compression**: Optimize image quality vs file size
-   **Lazy Loading**: Implement lazy loading for banner images

### Database Optimization

-   **Indexing**: Add indexes on frequently queried fields
-   **Eager Loading**: Use `with()` to prevent N+1 queries
-   **Caching**: Implement caching for banner data

### CDN Integration

-   **Cloud Storage**: Consider using AWS S3 or similar for file storage
-   **CDN**: Use CDN for faster file delivery
-   **Image Optimization**: Use services like Cloudinary for image processing

## Future Enhancements

### Planned Features

-   **Banner Scheduling**: Set start/end dates for banners
-   **A/B Testing**: Test different banner variations
-   **Analytics**: Track banner performance and clicks
-   **Multi-language**: Support for multiple languages
-   **Banner Templates**: Pre-designed banner templates
-   **Bulk Import**: Import multiple banners via CSV/Excel
-   **Media Library**: Centralized media management
-   **Responsive Images**: Generate multiple image sizes

### Technical Improvements

-   **WebP Support**: Add WebP image format support
-   **Video Thumbnails**: Generate video thumbnails automatically
-   **Progressive Upload**: Resume interrupted uploads
-   **Real-time Preview**: Live preview of banner changes
-   **Batch Operations**: Bulk edit multiple media items

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

---

**Version**: 2.0.0  
**Last Updated**: August 2025  
**Compatibility**: Laravel 10+, PHP 8.1+

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class UpdatePermissionGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing permissions with group names';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            'dashboard' => ['dashboard.view', 'dashboard.stats'],
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.export'],
            'roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'permissions.manage'],
            'profile' => ['profile.view', 'profile.edit', 'profile.change_password'],
            'settings' => ['settings.view', 'settings.edit', 'settings.manage'],
            'activities' => ['activities.view', 'activities.details', 'activities.export'],
            'cms' => ['cms.view', 'cms.create', 'cms.edit', 'cms.delete', 'cms.publish'],
            'custom_fields' => ['custom_fields.view', 'custom_fields.create', 'custom_fields.edit', 'custom_fields.delete'],
            'menus' => ['menus.view', 'menus.create', 'menus.edit', 'menus.delete', 'menu_items.manage'],
            'banners' => ['banners.view', 'banners.create', 'banners.edit', 'banners.delete', 'banners.media_manage', 'banners.publish'],
            'blog_categories' => ['blog_categories.view', 'blog_categories.create', 'blog_categories.edit', 'blog_categories.delete'],
            'blogs' => ['blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete', 'blogs.publish'],
            'news' => ['news.view', 'news.create', 'news.edit', 'news.delete', 'news.publish'],
            'events' => ['events.view', 'events.create', 'events.edit', 'events.delete', 'events.publish'],
            'enquiries' => ['enquiries.view', 'enquiries.delete', 'enquiries.export'],
            'volunteer_applications' => ['volunteer_applications.view', 'volunteer_applications.edit', 'volunteer_applications.delete', 'volunteer_applications.approve', 'volunteer_applications.reject'],
            'faq_categories' => ['faq_categories.view', 'faq_categories.create', 'faq_categories.edit', 'faq_categories.delete'],
            'faqs' => ['faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete', 'faqs.publish'],
            'newsletter' => ['newsletter.view', 'newsletter.export', 'newsletter.delete', 'newsletter.send'],
            'students' => ['students.view', 'students.create', 'students.edit', 'students.delete', 'students.approve', 'students.reject', 'students.finance_manage', 'students.documents_manage', 'students.export', 'students.activate', 'students.deactivate'],
            'student_applications' => ['student_applications.view', 'student_applications.edit', 'student_applications.delete', 'student_applications.approve', 'student_applications.reject', 'student_applications.finance_manage', 'student_applications.comment', 'student_applications.export'],
            'media' => ['media.view', 'media.upload', 'media.delete', 'media.organize'],
            'testimonials' => ['testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete', 'testimonials.publish'],
            'locations' => ['countries.view', 'countries.create', 'countries.edit', 'countries.delete', 'cities.view', 'cities.create', 'cities.edit', 'cities.delete']
        ];

        $updated = 0;
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                $result = Permission::where('name', $permission)->update(['group_name' => $module]);
                if ($result) {
                    $updated++;
                }
            }
        }

        $this->info("Updated {$updated} permissions with group names!");
        $this->info("Total permissions with groups: " . Permission::whereNotNull('group_name')->count());
    }
}
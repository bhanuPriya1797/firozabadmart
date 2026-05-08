<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomPermission as Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        // Clear existing permissions
        Permission::query()->delete();
        
        // Define all permissions by module
        $permissions = [
            // Dashboard Module
            'dashboard' => [
                'dashboard.view',
                'dashboard.stats',
            ],

            // User Management Module
            'users' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'users.export',
            ],

            // Role & Permission Management Module
            'roles' => [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'permissions.manage',
            ],

            // Profile Management Module
            'profile' => [
                'profile.view',
                'profile.edit',
                'profile.change_password',
            ],

            // Settings Module
            'settings' => [
                'settings.view',
                'settings.edit',
                'settings.manage',
            ],

            // Activity Log Module
            'activities' => [
                'activities.view',
                'activities.details',
                'activities.export',
            ],

            // CMS Module
            'cms' => [
                'cms.view',
                'cms.create',
                'cms.edit',
                'cms.delete',
                'cms.publish',
            ],

            // Custom Fields Module
            'custom_fields' => [
                'custom_fields.view',
                'custom_fields.create',
                'custom_fields.edit',
                'custom_fields.delete',
            ],

            // Menu Management Module
            'menus' => [
                'menus.view',
                'menus.create',
                'menus.edit',
                'menus.delete',
                'menu_items.manage',
            ],

            // Banner Management Module
            'banners' => [
                'banners.view',
                'banners.create',
                'banners.edit',
                'banners.delete',
                'banners.media_manage',
                'banners.publish',
            ],

            // Partner Management Module
            'partners' => [
                'partners.view',
                'partners.create',
                'partners.edit',
                'partners.delete',
            ],

            // Circulars Module
            'circulars' => [
                'circulars.view',
                'circulars.create',
                'circulars.edit',
                'circulars.delete',
            ],
            
            // Achievements Module
            'achievements' => [
                'achievements.view',
                'achievements.create',
                'achievements.edit',
                'achievements.delete',
            ],

            // Blog Categories Module
            'blog_categories' => [
                'blog_categories.view',
                'blog_categories.create',
                'blog_categories.edit',
                'blog_categories.delete',
            ],

            // Blogs Module
            'blogs' => [
                'blogs.view',
                'blogs.create',
                'blogs.edit',
                'blogs.delete',
                'blogs.publish',
            ],

            // News Module
            'news' => [
                'news.view',
                'news.create',
                'news.edit',
                'news.delete',
                'news.publish',
            ],

            // Events Module
            'events' => [
                'events.view',
                'events.create',
                'events.edit',
                'events.delete',
                'events.publish',
            ],

            // Success Stories Module
            'success_stories' => [
                'success_stories.view',
                'success_stories.create',
                'success_stories.edit',
                'success_stories.delete',
                'success_stories.publish',
            ],

            // Tour Packages Module
            'tour_packages' => [
                'tour_packages.view',
                'tour_packages.create',
                'tour_packages.edit',
                'tour_packages.delete',
            ],

            'destinations' => [
                'destinations.view',
                'destinations.create',
                'destinations.edit',
                'destinations.delete',
            ],

            // Enquiries Module
            'enquiries' => [
                'enquiries.view',
                'enquiries.delete',
                'enquiries.export',
            ],

            // Volunteer Applications Module
            'volunteer_applications' => [
                'volunteer_applications.view',
                'volunteer_applications.edit',
                'volunteer_applications.delete',
                'volunteer_applications.approve',
                'volunteer_applications.reject',
            ],

            // FAQ Categories Module
            'faq_categories' => [
                'faq_categories.view',
                'faq_categories.create',
                'faq_categories.edit',
                'faq_categories.delete',
            ],

            // FAQs Module
            'faqs' => [
                'faqs.view',
                'faqs.create',
                'faqs.edit',
                'faqs.delete',
                'faqs.publish',
            ],

            // Newsletter Module
            'newsletter' => [
                'newsletter.view',
                'newsletter.export',
                'newsletter.delete',
                'newsletter.send',
            ],
            
            // Tour Leads Module
            'tour_leads' => [
                'tour_leads.view',
                'tour_leads.delete',
            ],

            // Student Management Module
            'students' => [
                'students.view',
                'students.create',
                'students.edit',
                'students.delete',
                'students.approve',
                'students.reject',
                'students.finance_manage',
                'students.documents_manage',
                'students.export',
                'students.activate',
                'students.deactivate',
            ],

            // Archer Management Module
            'archers' => [
                'archers.view',
                'archers.create',
                'archers.edit',
                'archers.delete',
                'archers.export',
            ],

            // Student Applications Module
            'student_applications' => [
                'student_applications.view',
                'student_applications.edit',
                'student_applications.delete',
                'student_applications.approve',
                'student_applications.reject',
                'student_applications.finance_manage',
                'student_applications.comment',
                'student_applications.export',
            ],

            // Media Management Module
            'media' => [
                'media.view',
                'media.upload',
                'media.delete',
                'media.organize',
            ],

            // Testimonials Module
            'testimonials' => [
                'testimonials.view',
                'testimonials.create',
                'testimonials.edit',
                'testimonials.delete',
                'testimonials.publish',
            ],
            
            // Gallery Module
            'gallery' => [
                'gallery.view',
                'gallery.create',
                'gallery.edit',
                'gallery.delete',
            ],

            // Calendar Module
            'calendar' => [
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
            ],

            // Team Members Module
            'team_members' => [
                'team_members.view',
                'team_members.create',
                'team_members.edit',
                'team_members.delete',
            ],

            // Services Module
            'services' => [
                'services.view',
                'services.create',
                'services.edit',
                'services.delete',
            ],

            // Blog Comments Module
            'blog_comments' => [
                'blog_comments.view',
                'blog_comments.edit',
                'blog_comments.delete',
            ],

            // Country/City Management Module
            'locations' => [
                'countries.view',
                'countries.create',
                'countries.edit',
                'countries.delete',
                'cities.view',
                'cities.create',
                'cities.edit',
                'cities.delete',
            ],
        ];

        // Create permissions with group names and status
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Permission::updateOrCreate(
                    ['name' => $permission, 'guard_name' => 'web'],
                    ['group_name' => $module, 'status' => 1]
                );
            }
        }

        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $approverRole = Role::firstOrCreate(['name' => 'Approver', 'guard_name' => 'web']);
        $inspectorRole = Role::firstOrCreate(['name' => 'Inspector', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

        // Assign all permissions to SuperAdmin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to Approver role
        $approverPermissions = [
            'dashboard.view',
            'dashboard.stats',
            'profile.view',
            'profile.edit',
            'profile.change_password',
            'activities.view',
            'activities.details',
            'students.view',
            'students.edit',
            'students.approve',
            'students.reject',
            'students.finance_manage',
            'students.documents_manage',
            'archers.view',
            'archers.edit',
            'archers.export',
            'student_applications.view',
            'student_applications.edit',
            'student_applications.approve',
            'student_applications.reject',
            'student_applications.finance_manage',
            'student_applications.comment',
            'volunteer_applications.view',
            'volunteer_applications.edit',
            'volunteer_applications.approve',
            'volunteer_applications.reject',
            'enquiries.view',
            'newsletter.view',
            'destinations.view',
        ];
        $approverRole->givePermissionTo($approverPermissions);

        // Assign specific permissions to Inspector role
        $inspectorPermissions = [
            'dashboard.view',
            'profile.view',
            'profile.edit',
            'profile.change_password',
            'students.view',
            'students.documents_manage',
            'archers.view',
            'student_applications.view',
            'student_applications.comment',
            'volunteer_applications.view',
            'enquiries.view',
            'destinations.view',
            'tour_packages.view',
            'tour_leads.view',
            'gallery.view',
            'services.view',
        ];
        $inspectorRole->givePermissionTo($inspectorPermissions);

        // Assign specific permissions to Editor role
        $editorPermissions = [
            'dashboard.view',
            'profile.view',
            'profile.edit',
            'profile.change_password',
            'cms.view',
            'cms.create',
            'cms.edit',
            'cms.publish',
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.publish',
            'news.view',
            'news.create',
            'news.edit',
            'news.publish',
            'events.view',
            'events.create',
            'events.edit',
            'events.publish',
            'faqs.view',
            'faqs.create',
            'faqs.edit',
            'faqs.publish',
            'banners.view',
            'banners.create',
            'banners.edit',
            'banners.publish',
            'media.view',
            'media.upload',
            'media.organize',
            'destinations.view',
            'destinations.create',
            'destinations.edit',
            'destinations.delete',
            'tour_packages.view',
            'tour_packages.create',
            'tour_packages.edit',
            'tour_leads.view',
            'tour_leads.delete',
            'gallery.view',
            'gallery.create',
            'gallery.edit',
            'gallery.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'achievements.view',
            'achievements.create',
            'achievements.edit',
            'achievements.delete',
            'archers.view',
            'archers.create',
            'archers.edit',
            'archers.delete',
            'archers.export',
        ];
        $editorRole->givePermissionTo($editorPermissions);

        // Assign specific permissions to Viewer role
        $viewerPermissions = [
            'dashboard.view',
            'profile.view',
            'profile.edit',
            'profile.change_password',
            'students.view',
            'archers.view',
            'student_applications.view',
            'volunteer_applications.view',
            'enquiries.view',
            'newsletter.view',
            'blogs.view',
            'news.view',
            'events.view',
            'faqs.view',
            'banners.view',
            'media.view',
            'destinations.view',
            'tour_packages.view',
            'tour_leads.view',
            'gallery.view',
            'services.view',
            'achievements.view',
        ];
        $viewerRole->givePermissionTo($viewerPermissions);

        $this->command->info('Permissions and roles created successfully!');
        $this->command->info('Total permissions created: ' . Permission::count());
        $this->command->info('Total roles created: ' . Role::count());
    }
}

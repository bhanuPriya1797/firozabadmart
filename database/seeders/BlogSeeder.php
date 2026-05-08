<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample user if not exists
        $user = User::firstOrCreate(
            ['email' => 'admin@ilmission.org'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role_id' => 1, // Assuming role_id 1 is admin
            ]
        );

        // Create sample categories
        $categories = [
            'Education' => 'education',
            'Scholarships' => 'scholarships',
            'Community' => 'community',
            'Technology' => 'technology',
        ];

        foreach ($categories as $name => $slug) {
            BlogCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'sort_order' => 1,
                    'status' => 1,
                ]
            );
        }

        // Sample News Posts
        $newsPosts = [
            [
                'title' => 'IL Mission Launches New Scholarship Program for Kashmir Students',
                'brief' => 'We are excited to announce the launch of our comprehensive scholarship program designed specifically for deserving students in Kashmir.',
                'content' => '<p>IL Mission is proud to announce the launch of our new scholarship program aimed at supporting deserving students in Kashmir. This initiative represents our commitment to educational excellence and community development.</p>
                
                <h3>Program Highlights</h3>
                <ul>
                    <li>Full tuition coverage for selected students</li>
                    <li>Monthly stipend for educational expenses</li>
                    <li>Mentorship and career guidance</li>
                    <li>Access to educational resources and materials</li>
                </ul>
                
                <p>The program will benefit over 100 students in its first year, with plans to expand in the coming years. We believe that education is the key to breaking the cycle of poverty and building a brighter future for Kashmir.</p>
                
                <h3>Application Process</h3>
                <p>Applications are now open for the 2024-2025 academic year. Students can apply through our website or by visiting our local office in Srinagar.</p>',
                'content_type' => 'news',
                'featured' => 1,
                'blog_date' => now()->subDays(2),
            ],
            [
                'title' => 'Community Outreach Program Reaches 500+ Families',
                'brief' => 'Our community outreach team has successfully connected with over 500 families across Kashmir, providing essential support and resources.',
                'content' => '<p>Our dedicated community outreach team has completed a comprehensive survey and support program across Kashmir, reaching over 500 families in need.</p>
                
                <h3>Program Achievements</h3>
                <ul>
                    <li>500+ families surveyed and supported</li>
                    <li>Educational resources distributed</li>
                    <li>Health and nutrition guidance provided</li>
                    <li>Community workshops conducted</li>
                </ul>
                
                <p>The program focused on identifying families with school-going children who need additional support. Our team worked closely with local communities to understand their specific needs and challenges.</p>
                
                <h3>Impact</h3>
                <p>Through this initiative, we have been able to provide direct support to families and create lasting partnerships with local communities. The program has received overwhelming positive feedback from participants.</p>',
                'content_type' => 'news',
                'featured' => 1,
                'blog_date' => now()->subDays(5),
            ],
            [
                'title' => 'New Technology Lab Established in Srinagar',
                'brief' => 'A state-of-the-art technology lab has been established to provide students with access to modern computing and digital learning resources.',
                'content' => '<p>We are excited to announce the establishment of a new technology lab in Srinagar, equipped with the latest computers and digital learning tools.</p>
                
                <h3>Lab Features</h3>
                <ul>
                    <li>20 modern computers with latest software</li>
                    <li>High-speed internet connectivity</li>
                    <li>Digital learning platforms</li>
                    <li>Skilled instructors and support staff</li>
                </ul>
                
                <p>The lab will serve as a hub for digital literacy and technology education, helping students develop essential 21st-century skills. This initiative aligns with our mission to provide comprehensive educational support.</p>
                
                <h3>Access</h3>
                <p>The lab is open to all students enrolled in our programs and will be available for both individual study and group workshops.</p>',
                'content_type' => 'news',
                'featured' => 0,
                'blog_date' => now()->subDays(8),
            ],
        ];

        // Sample Events
        $eventPosts = [
            [
                'title' => 'Annual Scholarship Distribution Ceremony 2024',
                'brief' => 'Join us for our annual scholarship distribution ceremony where we will recognize and support deserving students.',
                'content' => '<p>We cordially invite you to attend our Annual Scholarship Distribution Ceremony 2024, a celebration of academic excellence and community support.</p>
                
                <h3>Event Details</h3>
                <ul>
                    <li><strong>Date:</strong> December 15, 2024</li>
                    <li><strong>Time:</strong> 2:00 PM - 5:00 PM</li>
                    <li><strong>Venue:</strong> Kashmir Convention Center, Srinagar</li>
                    <li><strong>Dress Code:</strong> Formal</li>
                </ul>
                
                <h3>Program Highlights</h3>
                <ul>
                    <li>Scholarship distribution to 50 deserving students</li>
                    <li>Recognition of academic achievements</li>
                    <li>Cultural performances</li>
                    <li>Networking opportunities</li>
                </ul>
                
                <p>This event brings together students, parents, educators, and community leaders to celebrate the power of education and community support.</p>',
                'content_type' => 'event',
                'featured' => 1,
                'blog_date' => now()->addDays(10),
            ],
            [
                'title' => 'Digital Literacy Workshop for Students',
                'brief' => 'A comprehensive workshop designed to enhance digital literacy skills among students.',
                'content' => '<p>Join us for an interactive digital literacy workshop designed to equip students with essential computer and internet skills.</p>
                
                <h3>Workshop Details</h3>
                <ul>
                    <li><strong>Date:</strong> December 20, 2024</li>
                    <li><strong>Time:</strong> 10:00 AM - 4:00 PM</li>
                    <li><strong>Venue:</strong> IL Mission Technology Lab, Srinagar</li>
                    <li><strong>Registration:</strong> Required (Limited seats)</li>
                </ul>
                
                <h3>Topics Covered</h3>
                <ul>
                    <li>Basic computer operations</li>
                    <li>Internet safety and security</li>
                    <li>Digital communication tools</li>
                    <li>Online learning platforms</li>
                </ul>
                
                <p>The workshop is free for all registered students and includes hands-on training and certification.</p>',
                'content_type' => 'event',
                'featured' => 0,
                'blog_date' => now()->addDays(15),
            ],
        ];

        // Sample Blog Posts
        $blogPosts = [
            [
                'title' => 'The Importance of Education in Community Development',
                'brief' => 'Exploring how education serves as the foundation for sustainable community development and social progress.',
                'content' => '<p>Education is widely recognized as one of the most powerful tools for community development and social progress. In this article, we explore the multifaceted role of education in building stronger, more resilient communities.</p>
                
                <h3>Education as a Catalyst for Change</h3>
                <p>Education serves as a catalyst for positive change in communities. It empowers individuals with knowledge, skills, and critical thinking abilities that are essential for personal and collective growth.</p>
                
                <h3>Economic Impact</h3>
                <p>Educated communities tend to have stronger economies. Education leads to better employment opportunities, higher incomes, and increased economic stability for families and communities.</p>
                
                <h3>Social Benefits</h3>
                <p>Beyond economic benefits, education promotes social cohesion, reduces inequality, and fosters a sense of community belonging. It helps individuals understand their rights and responsibilities as citizens.</p>
                
                <h3>Long-term Sustainability</h3>
                <p>Investing in education creates long-term benefits that extend beyond individual lifetimes. Educated communities are better equipped to address challenges and adapt to changing circumstances.</p>',
                'content_type' => 'blog',
                'featured' => 1,
                'blog_date' => now()->subDays(3),
                'category_id' => BlogCategory::where('slug', 'education')->first()->id,
            ],
            [
                'title' => 'Technology in Education: Bridging the Digital Divide',
                'brief' => 'How technology is transforming education and what it means for students in developing regions.',
                'content' => '<p>Technology has revolutionized the way we learn and teach, creating new opportunities for students worldwide. However, the digital divide remains a significant challenge in many regions.</p>
                
                <h3>The Digital Revolution in Education</h3>
                <p>Digital tools and platforms have transformed traditional learning methods, making education more accessible, interactive, and personalized. From online courses to educational apps, technology offers unprecedented learning opportunities.</p>
                
                <h3>Challenges of the Digital Divide</h3>
                <p>Despite the benefits, many students lack access to basic technology and internet connectivity. This digital divide creates educational inequalities that need to be addressed through targeted interventions.</p>
                
                <h3>Solutions and Initiatives</h3>
                <p>Various organizations and governments are working to bridge the digital divide through initiatives like community technology centers, affordable internet access, and digital literacy programs.</p>
                
                <h3>The Future of Education</h3>
                <p>As technology continues to evolve, it will play an increasingly important role in education. Ensuring equitable access to these tools is crucial for creating a more inclusive educational landscape.</p>',
                'content_type' => 'blog',
                'featured' => 0,
                'blog_date' => now()->subDays(7),
                'category_id' => BlogCategory::where('slug', 'technology')->first()->id,
            ],
        ];

        // Create news posts
        foreach ($newsPosts as $post) {
            Blog::firstOrCreate(
                ['title' => $post['title']],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'post_by' => $user->id,
                    'status' => 1,
                    'category_id' => null,
                ])
            );
        }

        // Create event posts
        foreach ($eventPosts as $post) {
            Blog::firstOrCreate(
                ['title' => $post['title']],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'post_by' => $user->id,
                    'status' => 1,
                    'category_id' => null,
                ])
            );
        }

        // Create blog posts
        foreach ($blogPosts as $post) {
            Blog::firstOrCreate(
                ['title' => $post['title']],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'post_by' => $user->id,
                    'status' => 1,
                ])
            );
        }

        $this->command->info('Sample blog posts created successfully!');
    }
}

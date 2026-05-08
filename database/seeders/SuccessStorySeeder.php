<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuccessStory;

class SuccessStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stories = [
            [
                'title' => 'From Struggling Student to Software Engineer',
                'brief' => 'Ahmed overcame financial hardships to become a successful software engineer at a leading tech company.',
                'description' => '<p>Ahmed\'s journey began in a small village where his family struggled to make ends meet. Despite the financial challenges, Ahmed never lost sight of his dream to become a software engineer. With the support of ILM\'s financial aid program, he was able to pursue his Computer Science degree at a prestigious university.</p>

<p><strong>Challenges Faced:</strong></p>
<ul>
<li>Family income below poverty line</li>
<li>Unable to afford tuition fees and books</li>
<li>Pressure to drop out and support family</li>
<li>Lack of access to modern technology</li>
</ul>

<p><strong>How ILM Helped:</strong></p>
<ul>
<li>Provided full tuition fee support</li>
<li>Supplied necessary books and study materials</li>
<li>Offered mentorship and career guidance</li>
<li>Connected with industry professionals</li>
</ul>

<p><strong>Current Achievement:</strong></p>
<p>Today, Ahmed works as a Senior Software Engineer at Microsoft, earning a six-figure salary. He has also started his own tech company and mentors other students from similar backgrounds. His success story has inspired over 50 students to pursue their dreams in technology.</p>

<p><em>"ILM didn\'t just provide financial support; they gave me hope and the confidence to believe in myself. Without their help, I would never have been able to achieve my dreams."</em> - Ahmed</p>',
                'featured' => true,
                'status' => true,
                'sort_order' => 1
            ],
            [
                'title' => 'Medical Student Becomes Renowned Doctor',
                'brief' => 'Dr. Fatima completed her medical degree with ILM\'s support and now serves her community as a respected physician.',
                'description' => '<p>Dr. Fatima\'s story is one of determination, compassion, and unwavering commitment to serving others. Coming from a family of modest means, she dreamed of becoming a doctor to help her community, but the high cost of medical education seemed insurmountable.</p>

<p><strong>Her Journey:</strong></p>
<p>Fatima excelled in her studies but faced the harsh reality that medical school was financially out of reach. Her parents, both teachers, could not afford the expensive tuition fees and living costs associated with medical education.</p>

<p><strong>ILM\'s Support:</strong></p>
<ul>
<li>Full scholarship for 5-year medical program</li>
<li>Monthly stipend for living expenses</li>
<li>Access to medical books and equipment</li>
<li>Internship placement assistance</li>
</ul>

<p><strong>Current Impact:</strong></p>
<p>Dr. Fatima now runs her own clinic in her hometown, providing affordable healthcare to underserved communities. She has treated over 10,000 patients and established a free medical camp that serves 500+ people monthly. Her dedication to community service has earned her recognition from the state government.</p>

<p><em>"ILM believed in me when I couldn\'t believe in myself. They showed me that financial constraints should never limit one\'s potential to serve humanity."</em> - Dr. Fatima</p>',
                'featured' => true,
                'status' => true,
                'sort_order' => 2
            ],
            [
                'title' => 'Engineering Graduate Builds Successful Startup',
                'brief' => 'Mohammed used his engineering degree to create innovative solutions and build a thriving technology startup.',
                'description' => '<p>Mohammed\'s entrepreneurial journey began during his engineering studies, where he developed innovative solutions to real-world problems. With ILM\'s support, he was able to focus on his studies and develop his ideas without financial stress.</p>

<p><strong>Early Struggles:</strong></p>
<ul>
<li>Family business failed, leaving them in debt</li>
<li>Could not afford engineering college fees</li>
<li>Had to work part-time jobs affecting studies</li>
<li>Lacked resources for project development</li>
</ul>

<p><strong>ILM\'s Intervention:</strong></p>
<ul>
<li>Complete tuition fee coverage</li>
<li>Project funding for innovative ideas</li>
<li>Mentorship from industry experts</li>
<li>Networking opportunities</li>
</ul>

<p><strong>Current Success:</strong></p>
<p>Mohammed\'s startup, TechSolutions, now employs 25 people and has clients across three countries. His innovative water purification technology has been adopted by 50+ villages, providing clean drinking water to over 100,000 people. The company has received multiple awards for social impact and innovation.</p>

<p><em>"ILM didn\'t just fund my education; they invested in my vision. Their support allowed me to focus on innovation and create solutions that benefit society."</em> - Mohammed</p>',
                'featured' => true,
                'status' => true,
                'sort_order' => 3
            ],
            [
                'title' => 'Teacher Inspires Next Generation',
                'brief' => 'Aisha became an exceptional teacher who has transformed the lives of hundreds of students in her community.',
                'description' => '<p>Aisha\'s passion for education and her desire to make a difference in her community led her to pursue a teaching degree. However, financial constraints threatened to derail her dreams of becoming an educator.</p>

<p><strong>Background:</strong></p>
<p>Growing up in a rural area with limited educational resources, Aisha witnessed firsthand how quality education could transform lives. She was determined to become a teacher who could inspire and empower young minds, but her family\'s financial situation made this dream seem impossible.</p>

<p><strong>ILM\'s Support:</strong></p>
<ul>
<li>Full scholarship for B.Ed program</li>
<li>Teaching materials and resources</li>
<li>Training workshops and seminars</li>
<li>Job placement assistance</li>
</ul>

<p><strong>Impact Created:</strong></p>
<p>Today, Aisha is the principal of a school in her hometown, where she has implemented innovative teaching methods and improved student performance by 40%. She has trained 30+ teachers and established a scholarship program for underprivileged students. Her school has become a model institution in the district.</p>

<p><em>"ILM gave me the opportunity to pursue my passion for teaching. Now I can give back to my community by providing quality education to children who need it most."</em> - Aisha</p>',
                'featured' => false,
                'status' => true,
                'sort_order' => 4
            ],
            [
                'title' => 'Business Graduate Creates Employment Opportunities',
                'brief' => 'Hassan used his business education to start a successful enterprise that employs 50+ people from his community.',
                'description' => '<p>Hassan\'s entrepreneurial spirit and business acumen were evident from a young age. However, without proper education and financial backing, his dreams of starting a business remained unfulfilled until ILM stepped in to support his education.</p>

<p><strong>Early Challenges:</strong></p>
<ul>
<li>Family relied on daily wage labor</li>
<li>No funds for higher education</li>
<li>Lacked business knowledge and skills</li>
<li>No access to mentors or guidance</li>
</ul>

<p><strong>Educational Journey:</strong></p>
<p>With ILM\'s support, Hassan pursued a Bachelor\'s in Business Administration, where he learned essential business skills, financial management, and marketing strategies. He also participated in various entrepreneurship programs and competitions.</p>

<p><strong>Current Achievement:</strong></p>
<p>Hassan now owns a successful manufacturing company that produces eco-friendly products. His business employs 50+ people from his community and has expanded to three cities. He has also established a micro-finance program to help other aspiring entrepreneurs in his area.</p>

<p><em>"ILM not only educated me but also taught me the importance of giving back. My success is measured not just by profit, but by the number of families I can support through employment."</em> - Hassan</p>',
                'featured' => false,
                'status' => true,
                'sort_order' => 5
            ],
            [
                'title' => 'Research Scholar Makes Scientific Breakthrough',
                'brief' => 'Dr. Sarah\'s research in renewable energy has led to groundbreaking discoveries that could benefit millions worldwide.',
                'description' => '<p>Dr. Sarah\'s journey from a struggling student to a renowned research scientist is a testament to the power of education and determination. Her research in renewable energy has the potential to revolutionize how we generate and store clean energy.</p>

<p><strong>Academic Journey:</strong></p>
<p>Sarah excelled in science throughout her school years but faced significant financial barriers to pursuing higher education. Her family could not afford the costs associated with advanced degrees in science and research.</p>

<p><strong>ILM\'s Comprehensive Support:</strong></p>
<ul>
<li>Full scholarship for Master\'s and Ph.D. programs</li>
<li>Research funding and equipment access</li>
<li>International conference participation</li>
<li>Mentorship from leading scientists</li>
</ul>

<p><strong>Scientific Contributions:</strong></p>
<p>Dr. Sarah has published 25+ research papers in international journals and holds 5 patents for renewable energy innovations. Her work on solar cell efficiency has been recognized by the International Energy Agency. She currently leads a research team of 15 scientists working on next-generation energy solutions.</p>

<p><em>"ILM\'s support allowed me to focus on research without financial worries. Their belief in my potential gave me the confidence to pursue groundbreaking discoveries."</em> - Dr. Sarah</p>',
                'featured' => true,
                'status' => true,
                'sort_order' => 6
            ]
        ];

        foreach ($stories as $storyData) {
            SuccessStory::create($storyData);
        }
    }
}

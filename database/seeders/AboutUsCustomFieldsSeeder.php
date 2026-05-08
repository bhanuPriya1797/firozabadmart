<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomField;
use App\Models\Cms;
use App\Models\CustomFieldValue;

class AboutUsCustomFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create About Us page first
        $aboutPage = Cms::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'brief' => 'Your trusted trip companion',
                'heading' => 'Looking for joy?',
                'template' => 'about',
                'description' => '<p>These popular destinations have a lot to offer. Discover new places, unique experiences, and seamless travel planning with us.</p>',
                'status' => 1,
                'featured' => 1,
                'sort_order' => 1,
            ]
        );

        // Define all custom fields for About Us page
        $customFields = [
            [
                'label' => 'Banner Heading',
                'key' => 'banner_heading',
                'type' => 'text',
                'group_name' => 'Banner',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Looking for joy?'
            ],
            [
                'label' => 'Banner Subtitle',
                'key' => 'banner_subtitle',
                'type' => 'text',
                'group_name' => 'Banner',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Your trusted trip companion'
            ],
            [
                'label' => 'Features JSON',
                'key' => 'features',
                'type' => 'textarea',
                'group_name' => 'Features',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '[{"icon":"frontend/img/featureIcons/1/1.svg","title":"Best Price Guarantee","desc":"Lorem ipsum dolor sit amet, consectetur adipiscing elit."},{"icon":"frontend/img/featureIcons/1/2.svg","title":"Easy & Quick Booking","desc":"Lorem ipsum dolor sit amet, consectetur adipiscing elit."},{"icon":"frontend/img/featureIcons/1/3.svg","title":"Customer Care 24/7","desc":"Lorem ipsum dolor sit amet, consectetur adipiscing elit."}]'
            ],
            [
                'label' => 'Counters JSON',
                'key' => 'counters',
                'type' => 'textarea',
                'group_name' => 'Stats',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '[{"value":"4,958","label":"Destinations"},{"value":"2,869","label":"Total Properties"},{"value":"2M","label":"Happy customers"},{"value":"574,974","label":"Our Volunteers"}]'
            ],
            [
                'label' => 'Happy People',
                'key' => 'happy_people',
                'type' => 'text',
                'group_name' => 'Stats',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '13m+'
            ],
            [
                'label' => 'Team Members JSON',
                'key' => 'team_members',
                'type' => 'textarea',
                'group_name' => 'Team',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '[{"image":"frontend/img/team/1.png","name":"Cody Fisher","role":"Medical Assistant"},{"image":"frontend/img/team/2.png","name":"Dianne Russell","role":"Web Designer"},{"image":"frontend/img/team/3.png","name":"Jerome Bell","role":"Marketing Coordinator"},{"image":"frontend/img/team/4.png","name":"Theresa Webb","role":"Nursing Assistant"},{"image":"frontend/img/team/5.png","name":"Cameron Williamson","role":"Dog Trainer"}]'
            ],
            
            // List Section
            [
                'label' => 'Show List Section',
                'key' => 'show_list',
                'type' => 'checkbox',
                'group_name' => 'List Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'List Item 1',
                'key' => 'list_item_1',
                'type' => 'text',
                'group_name' => 'List Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Support extended directly to students and through local NGOs at the grassroots level.'
            ],
            [
                'label' => 'List Item 2',
                'key' => 'list_item_2',
                'type' => 'text',
                'group_name' => 'List Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Focus on uplifting education standards by offering various forms of assistance.'
            ],
            [
                'label' => 'List Item 3',
                'key' => 'list_item_3',
                'type' => 'text',
                'group_name' => 'List Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Proper governance process to freely and fairly evaluate each candidate.'
            ],
            
            // CTA Button
            [
                'label' => 'Show CTA Button',
                'key' => 'show_cta_button',
                'type' => 'checkbox',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'CTA Button Text',
                'key' => 'cta_button_text',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Discover More'
            ],
            [
                'label' => 'CTA Button URL',
                'key' => 'cta_button_url',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '/contact'
            ],
            
            // Background Section
            [
                'label' => 'Show Background Section',
                'key' => 'show_background',
                'type' => 'checkbox',
                'group_name' => 'Background Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Background Title',
                'key' => 'background_title',
                'type' => 'text',
                'group_name' => 'Background Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Background'
            ],
            [
                'label' => 'Background Content',
                'key' => 'background_content',
                'type' => 'editor',
                'group_name' => 'Background Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '<p>Fifteen years ago, a small group of like-minded individuals joined together to voluntarily help poor meritorious students of the Kashmir valley, who in their view, possess the potential to excel in their educational careers. These individuals have been supporting various social activities with a special preference to the education causes, as individual and as a group.</p><p>The support was extended to the deserving students directly and through local NGOs operating at the grass root level. However, their preference was always to help the community to uplift their educational standards by providing various forms of assistance including the financial support.</p><p>In 2016, they contemplated to give this individual group, a proper shape and structure and to involve more people in this noble cause. To provide it a further impetus, they formed a group under the name <strong>Kashmir Educational Support</strong>, now operating as <strong>IL Mission</strong>.</p>'
            ],
            
            // Objective Section
            [
                'label' => 'Show Objective Section',
                'key' => 'show_objective',
                'type' => 'checkbox',
                'group_name' => 'Objective Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Objective Title',
                'key' => 'objective_title',
                'type' => 'text',
                'group_name' => 'Objective Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Objective'
            ],
            [
                'label' => 'Objective Content',
                'key' => 'objective_content',
                'type' => 'textarea',
                'group_name' => 'Objective Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'The core objective of <strong>IL Mission</strong> is to help the poor and needy meritorious students to continue their education and to enhance the educational standards of the community as a whole.'
            ],
            
            // Structure Section
            [
                'label' => 'Show Structure Section',
                'key' => 'show_structure',
                'type' => 'checkbox',
                'group_name' => 'Structure Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Structure Title',
                'key' => 'structure_title',
                'type' => 'text',
                'group_name' => 'Structure Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Structure'
            ],
            [
                'label' => 'Structure Content',
                'key' => 'structure_content',
                'type' => 'editor',
                'group_name' => 'Structure Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '<p><strong>IL Mission</strong> constitutes of two teams i.e., <strong>Ground Team (GT)</strong>, which is responsible for identifying students at the ground level and recommend the deserving candidates for financial support. The second team, <strong>Finance Team (FT)</strong>, approves the cases (students recommended by Ground Team) and arranges the required financial assistance for them.</p>'
            ],
            
            // How It Works Section
            [
                'label' => 'Show How It Works Section',
                'key' => 'show_how_it_works',
                'type' => 'checkbox',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'How It Works Title',
                'key' => 'how_it_works_title',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'How it works?'
            ],
            [
                'label' => 'How It Works Subtitle',
                'key' => 'how_it_works_subtitle',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'A clear four stage process helps IL Mission to extend support to deserving students.'
            ],
            [
                'label' => 'Step 1 Title',
                'key' => 'step_1_title',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Identification'
            ],
            [
                'label' => 'Step 1 Description',
                'key' => 'step_1_desc',
                'type' => 'textarea',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Ground Team identifies candidates that meet the eligibility criteria for getting any support from IL Mission. The credentials of the candidates are verified by the team at this stage.'
            ],
            [
                'label' => 'Step 2 Title',
                'key' => 'step_2_title',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Verification'
            ],
            [
                'label' => 'Step 2 Description',
                'key' => 'step_2_desc',
                'type' => 'textarea',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Ground Team will verify all details supplied by the candidate and undertake background checks before recommending eligible candidate to the Finance Team.'
            ],
            [
                'label' => 'Step 3 Title',
                'key' => 'step_3_title',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Approval'
            ],
            [
                'label' => 'Step 3 Description',
                'key' => 'step_3_desc',
                'type' => 'textarea',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Finance Team thoroughly evaluates candidates recommended by the Ground Team. The team conduct their own due diligence and may contact candidate and the institution directly.'
            ],
            [
                'label' => 'Step 4 Title',
                'key' => 'step_4_title',
                'type' => 'text',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Fundraising & Disbursement'
            ],
            [
                'label' => 'Step 4 Description',
                'key' => 'step_4_desc',
                'type' => 'textarea',
                'group_name' => 'How It Works Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Founding members arrange finances for the approved case either directly from their own resources and/or by raising funds through an appeal.'
            ],
            
            // Counter Section
            [
                'label' => 'Show Counters Section',
                'key' => 'show_counters',
                'type' => 'checkbox',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Counter 1 Number',
                'key' => 'counter_1_number',
                'type' => 'number',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '500'
            ],
            [
                'label' => 'Counter 1 Title',
                'key' => 'counter_1_title',
                'type' => 'text',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '+ Students Supported'
            ],
            [
                'label' => 'Counter 2 Number',
                'key' => 'counter_2_number',
                'type' => 'number',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '900'
            ],
            [
                'label' => 'Counter 2 Title',
                'key' => 'counter_2_title',
                'type' => 'text',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '+ Success Stories'
            ],
            [
                'label' => 'Counter 3 Number',
                'key' => 'counter_3_number',
                'type' => 'number',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1500'
            ],
            [
                'label' => 'Counter 3 Title',
                'key' => 'counter_3_title',
                'type' => 'text',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '+ Volunteers'
            ],
            [
                'label' => 'Counter 4 Number',
                'key' => 'counter_4_number',
                'type' => 'number',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '15'
            ],
            [
                'label' => 'Counter 4 Title',
                'key' => 'counter_4_title',
                'type' => 'text',
                'group_name' => 'Counter Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '+ Years of Service'
            ],
            
            // Additional Info Section
            [
                'label' => 'Show Additional Info Section',
                'key' => 'show_additional_info',
                'type' => 'checkbox',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Governance Title',
                'key' => 'governance_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Governance'
            ],
            [
                'label' => 'Governance Content',
                'key' => 'governance_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'The governance process in place is intended to freely and fairly evaluate each candidate at every stage of the process. Most importantly, at the Identification Stage, a candidate must be recommended by at least two people known to the candidate (one of them being a senior citizen) and at the Approval Stage, a case must be approved by two out of three authorised approvers in the Finance Team.'
            ],
            [
                'label' => 'Eligibility Title',
                'key' => 'eligibility_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Eligibility'
            ],
            [
                'label' => 'Eligibility Content',
                'key' => 'eligibility_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Any candidate who has a good track record of previous performance and is from economically disadvantageous background is eligible for the support. Also, those candidates who hail from the far flung or backward areas of Kashmir and have no access to other resources (such as scholarships, availability of local colleges or teachers etc.) are eligible.'
            ],
            [
                'label' => 'Donors Title',
                'key' => 'donors_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Donors'
            ],
            [
                'label' => 'Donors Content',
                'key' => 'donors_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Our donors mainly constitute individual members of the society. At times, some NGO groups come forward for the help of the students. Donors are based in Kashmir and outside Kashmir as well. They may directly sponsor a case published in the fund-raising appeal or pledge the amount through IL Mission (admins) discreetly so that their privacy is upheld.'
            ],
            [
                'label' => 'Confidentiality Title',
                'key' => 'confidentiality_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Confidentiality'
            ],
            [
                'label' => 'Confidentiality Content',
                'key' => 'confidentiality_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'We respect confidentiality of our candidates and donors and take utmost care in safeguarding their privacy. All personal details are kept anonymous and both candidates and donors are identified by a unique ID number. Donor details are not disclosed, not even to the Ground Team of IL Mission.'
            ],
            [
                'label' => 'Funding Title',
                'key' => 'funding_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Funding Source'
            ],
            [
                'label' => 'Funding Content',
                'key' => 'funding_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Our sources of funding are Donations, Zakat, Khumus, Sadqa and Qarz Hasna. Funds are raised from the public and they directly transfer the funds to the college/institute. No other source of funding is available except from the personal income of the donors.'
            ],
            [
                'label' => 'Volunteers Title',
                'key' => 'volunteers_title',
                'type' => 'text',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Volunteers'
            ],
            [
                'label' => 'Volunteers Content',
                'key' => 'volunteers_content',
                'type' => 'textarea',
                'group_name' => 'Additional Info Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'The member of the group both at Ground Team and Finance Team level are all volunteers. They all work only for the sake of Allah, for helping the poor student community of Kashmir valley. IL Mission currently operates with a minimal overhead expenditure, given the fact that all work is done on voluntary basis.'
            ],
            
            // Mission Vision Section
            [
                'label' => 'Show Mission Vision Section',
                'key' => 'show_mission_vision',
                'type' => 'checkbox',
                'group_name' => 'Mission Vision Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'Mission Title',
                'key' => 'mission_title',
                'type' => 'text',
                'group_name' => 'Mission Vision Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Our Mission'
            ],
            [
                'label' => 'Mission Content',
                'key' => 'mission_content',
                'type' => 'textarea',
                'group_name' => 'Mission Vision Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'To provide quality education and support to deserving students in Kashmir, enabling them to achieve their full potential and contribute positively to society.'
            ],
            [
                'label' => 'Vision Title',
                'key' => 'vision_title',
                'type' => 'text',
                'group_name' => 'Mission Vision Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Our Vision'
            ],
            [
                'label' => 'Vision Content',
                'key' => 'vision_content',
                'type' => 'textarea',
                'group_name' => 'Mission Vision Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'To create a world where every deserving student has access to quality education, regardless of their financial background, and can pursue their dreams with confidence.'
            ],
            
            // CTA Section
            [
                'label' => 'Show CTA Section',
                'key' => 'show_cta_section',
                'type' => 'checkbox',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '1'
            ],
            [
                'label' => 'CTA Title',
                'key' => 'cta_title',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Join Our Mission'
            ],
            [
                'label' => 'CTA Description',
                'key' => 'cta_description',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Together, we can make a difference in the lives of deserving students.'
            ],
            [
                'label' => 'CTA Primary Button Text',
                'key' => 'cta_primary_button_text',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Become a Volunteer'
            ],
            [
                'label' => 'CTA Primary Button URL',
                'key' => 'cta_primary_button_url',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '/volunteer'
            ],
            [
                'label' => 'CTA Secondary Button Text',
                'key' => 'cta_secondary_button_text',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => 'Contact Us'
            ],
            [
                'label' => 'CTA Secondary Button URL',
                'key' => 'cta_secondary_button_url',
                'type' => 'text',
                'group_name' => 'CTA Section',
                'module' => 'cms',
                'ref_id' => $aboutPage->id,
                'value' => '/contact'
            ],
        ];

        // Create custom fields and their values
        foreach ($customFields as $fieldData) {
            $value = $fieldData['value'];
            unset($fieldData['value']);
            
            // Create or update custom field
            $customField = CustomField::updateOrCreate(
                ['key' => $fieldData['key']],
                $fieldData
            );
            
            // Create or update custom field value
            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $customField->id,
                    'module_type' => 'cms',
                    'module_id' => $aboutPage->id,
                ],
                [
                    'value' => $value
                ]
            );
        }

        $this->command->info('About Us page and custom fields created successfully!');
    }
}

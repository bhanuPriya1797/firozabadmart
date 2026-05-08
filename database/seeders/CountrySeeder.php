<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'India', 'code' => 'IND', 'phone_code' => '+91', 'capital' => 'New Delhi', 'currency' => 'Indian Rupee', 'currency_code' => 'INR'],
            ['name' => 'Pakistan', 'code' => 'PAK', 'phone_code' => '+92', 'capital' => 'Islamabad', 'currency' => 'Pakistani Rupee', 'currency_code' => 'PKR'],
            ['name' => 'Bangladesh', 'code' => 'BGD', 'phone_code' => '+880', 'capital' => 'Dhaka', 'currency' => 'Bangladeshi Taka', 'currency_code' => 'BDT'],
            ['name' => 'Sri Lanka', 'code' => 'LKA', 'phone_code' => '+94', 'capital' => 'Colombo', 'currency' => 'Sri Lankan Rupee', 'currency_code' => 'LKR'],
            ['name' => 'Nepal', 'code' => 'NPL', 'phone_code' => '+977', 'capital' => 'Kathmandu', 'currency' => 'Nepalese Rupee', 'currency_code' => 'NPR'],
            ['name' => 'Bhutan', 'code' => 'BTN', 'phone_code' => '+975', 'capital' => 'Thimphu', 'currency' => 'Bhutanese Ngultrum', 'currency_code' => 'BTN'],
            ['name' => 'Maldives', 'code' => 'MDV', 'phone_code' => '+960', 'capital' => 'Male', 'currency' => 'Maldivian Rufiyaa', 'currency_code' => 'MVR'],
            ['name' => 'Afghanistan', 'code' => 'AFG', 'phone_code' => '+93', 'capital' => 'Kabul', 'currency' => 'Afghan Afghani', 'currency_code' => 'AFN'],
            ['name' => 'United States', 'code' => 'USA', 'phone_code' => '+1', 'capital' => 'Washington, D.C.', 'currency' => 'US Dollar', 'currency_code' => 'USD'],
            ['name' => 'United Kingdom', 'code' => 'GBR', 'phone_code' => '+44', 'capital' => 'London', 'currency' => 'British Pound', 'currency_code' => 'GBP'],
            ['name' => 'Canada', 'code' => 'CAN', 'phone_code' => '+1', 'capital' => 'Ottawa', 'currency' => 'Canadian Dollar', 'currency_code' => 'CAD'],
            ['name' => 'Australia', 'code' => 'AUS', 'phone_code' => '+61', 'capital' => 'Canberra', 'currency' => 'Australian Dollar', 'currency_code' => 'AUD'],
            ['name' => 'Germany', 'code' => 'DEU', 'phone_code' => '+49', 'capital' => 'Berlin', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'France', 'code' => 'FRA', 'phone_code' => '+33', 'capital' => 'Paris', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Japan', 'code' => 'JPN', 'phone_code' => '+81', 'capital' => 'Tokyo', 'currency' => 'Japanese Yen', 'currency_code' => 'JPY'],
            ['name' => 'China', 'code' => 'CHN', 'phone_code' => '+86', 'capital' => 'Beijing', 'currency' => 'Chinese Yuan', 'currency_code' => 'CNY'],
            ['name' => 'Brazil', 'code' => 'BRA', 'phone_code' => '+55', 'capital' => 'Brasília', 'currency' => 'Brazilian Real', 'currency_code' => 'BRL'],
            ['name' => 'Mexico', 'code' => 'MEX', 'phone_code' => '+52', 'capital' => 'Mexico City', 'currency' => 'Mexican Peso', 'currency_code' => 'MXN'],
            ['name' => 'South Africa', 'code' => 'ZAF', 'phone_code' => '+27', 'capital' => 'Pretoria', 'currency' => 'South African Rand', 'currency_code' => 'ZAR'],
            ['name' => 'Russia', 'code' => 'RUS', 'phone_code' => '+7', 'capital' => 'Moscow', 'currency' => 'Russian Ruble', 'currency_code' => 'RUB'],
            ['name' => 'Italy', 'code' => 'ITA', 'phone_code' => '+39', 'capital' => 'Rome', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Spain', 'code' => 'ESP', 'phone_code' => '+34', 'capital' => 'Madrid', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Netherlands', 'code' => 'NLD', 'phone_code' => '+31', 'capital' => 'Amsterdam', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Switzerland', 'code' => 'CHE', 'phone_code' => '+41', 'capital' => 'Bern', 'currency' => 'Swiss Franc', 'currency_code' => 'CHF'],
            ['name' => 'Sweden', 'code' => 'SWE', 'phone_code' => '+46', 'capital' => 'Stockholm', 'currency' => 'Swedish Krona', 'currency_code' => 'SEK'],
            ['name' => 'Norway', 'code' => 'NOR', 'phone_code' => '+47', 'capital' => 'Oslo', 'currency' => 'Norwegian Krone', 'currency_code' => 'NOK'],
            ['name' => 'Denmark', 'code' => 'DNK', 'phone_code' => '+45', 'capital' => 'Copenhagen', 'currency' => 'Danish Krone', 'currency_code' => 'DKK'],
            ['name' => 'Finland', 'code' => 'FIN', 'phone_code' => '+358', 'capital' => 'Helsinki', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Poland', 'code' => 'POL', 'phone_code' => '+48', 'capital' => 'Warsaw', 'currency' => 'Polish Złoty', 'currency_code' => 'PLN'],
            ['name' => 'Austria', 'code' => 'AUT', 'phone_code' => '+43', 'capital' => 'Vienna', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Belgium', 'code' => 'BEL', 'phone_code' => '+32', 'capital' => 'Brussels', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Ireland', 'code' => 'IRL', 'phone_code' => '+353', 'capital' => 'Dublin', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Portugal', 'code' => 'PRT', 'phone_code' => '+351', 'capital' => 'Lisbon', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Greece', 'code' => 'GRC', 'phone_code' => '+30', 'capital' => 'Athens', 'currency' => 'Euro', 'currency_code' => 'EUR'],
            ['name' => 'Turkey', 'code' => 'TUR', 'phone_code' => '+90', 'capital' => 'Ankara', 'currency' => 'Turkish Lira', 'currency_code' => 'TRY'],
            ['name' => 'Iran', 'code' => 'IRN', 'phone_code' => '+98', 'capital' => 'Tehran', 'currency' => 'Iranian Rial', 'currency_code' => 'IRR'],
            ['name' => 'Iraq', 'code' => 'IRQ', 'phone_code' => '+964', 'capital' => 'Baghdad', 'currency' => 'Iraqi Dinar', 'currency_code' => 'IQD'],
            ['name' => 'Saudi Arabia', 'code' => 'SAU', 'phone_code' => '+966', 'capital' => 'Riyadh', 'currency' => 'Saudi Riyal', 'currency_code' => 'SAR'],
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'phone_code' => '+971', 'capital' => 'Abu Dhabi', 'currency' => 'UAE Dirham', 'currency_code' => 'AED'],
            ['name' => 'Qatar', 'code' => 'QAT', 'phone_code' => '+974', 'capital' => 'Doha', 'currency' => 'Qatari Riyal', 'currency_code' => 'QAR'],
            ['name' => 'Kuwait', 'code' => 'KWT', 'phone_code' => '+965', 'capital' => 'Kuwait City', 'currency' => 'Kuwaiti Dinar', 'currency_code' => 'KWD'],
            ['name' => 'Bahrain', 'code' => 'BHR', 'phone_code' => '+973', 'capital' => 'Manama', 'currency' => 'Bahraini Dinar', 'currency_code' => 'BHD'],
            ['name' => 'Oman', 'code' => 'OMN', 'phone_code' => '+968', 'capital' => 'Muscat', 'currency' => 'Omani Rial', 'currency_code' => 'OMR'],
            ['name' => 'Yemen', 'code' => 'YEM', 'phone_code' => '+967', 'capital' => 'Sana\'a', 'currency' => 'Yemeni Rial', 'currency_code' => 'YER'],
            ['name' => 'Jordan', 'code' => 'JOR', 'phone_code' => '+962', 'capital' => 'Amman', 'currency' => 'Jordanian Dinar', 'currency_code' => 'JOD'],
            ['name' => 'Lebanon', 'code' => 'LBN', 'phone_code' => '+961', 'capital' => 'Beirut', 'currency' => 'Lebanese Pound', 'currency_code' => 'LBP'],
            ['name' => 'Syria', 'code' => 'SYR', 'phone_code' => '+963', 'capital' => 'Damascus', 'currency' => 'Syrian Pound', 'currency_code' => 'SYP'],
            ['name' => 'Israel', 'code' => 'ISR', 'phone_code' => '+972', 'capital' => 'Jerusalem', 'currency' => 'Israeli Shekel', 'currency_code' => 'ILS'],
            ['name' => 'Palestine', 'code' => 'PSE', 'phone_code' => '+970', 'capital' => 'East Jerusalem', 'currency' => 'Israeli Shekel', 'currency_code' => 'ILS'],
            ['name' => 'Egypt', 'code' => 'EGY', 'phone_code' => '+20', 'capital' => 'Cairo', 'currency' => 'Egyptian Pound', 'currency_code' => 'EGP'],
            ['name' => 'Libya', 'code' => 'LBY', 'phone_code' => '+218', 'capital' => 'Tripoli', 'currency' => 'Libyan Dinar', 'currency_code' => 'LYD'],
            ['name' => 'Tunisia', 'code' => 'TUN', 'phone_code' => '+216', 'capital' => 'Tunis', 'currency' => 'Tunisian Dinar', 'currency_code' => 'TND'],
            ['name' => 'Algeria', 'code' => 'DZA', 'phone_code' => '+213', 'capital' => 'Algiers', 'currency' => 'Algerian Dinar', 'currency_code' => 'DZD'],
            ['name' => 'Morocco', 'code' => 'MAR', 'phone_code' => '+212', 'capital' => 'Rabat', 'currency' => 'Moroccan Dirham', 'currency_code' => 'MAD'],
            ['name' => 'Sudan', 'code' => 'SDN', 'phone_code' => '+249', 'capital' => 'Khartoum', 'currency' => 'Sudanese Pound', 'currency_code' => 'SDG'],
            ['name' => 'Ethiopia', 'code' => 'ETH', 'phone_code' => '+251', 'capital' => 'Addis Ababa', 'currency' => 'Ethiopian Birr', 'currency_code' => 'ETB'],
            ['name' => 'Kenya', 'code' => 'KEN', 'phone_code' => '+254', 'capital' => 'Nairobi', 'currency' => 'Kenyan Shilling', 'currency_code' => 'KES'],
            ['name' => 'Uganda', 'code' => 'UGA', 'phone_code' => '+256', 'capital' => 'Kampala', 'currency' => 'Ugandan Shilling', 'currency_code' => 'UGX'],
            ['name' => 'Tanzania', 'code' => 'TZA', 'phone_code' => '+255', 'capital' => 'Dodoma', 'currency' => 'Tanzanian Shilling', 'currency_code' => 'TZS'],
            ['name' => 'Nigeria', 'code' => 'NGA', 'phone_code' => '+234', 'capital' => 'Abuja', 'currency' => 'Nigerian Naira', 'currency_code' => 'NGN'],
            ['name' => 'Ghana', 'code' => 'GHA', 'phone_code' => '+233', 'capital' => 'Accra', 'currency' => 'Ghanaian Cedi', 'currency_code' => 'GHS'],
            ['name' => 'Senegal', 'code' => 'SEN', 'phone_code' => '+221', 'capital' => 'Dakar', 'currency' => 'West African CFA Franc', 'currency_code' => 'XOF'],
            ['name' => 'Mali', 'code' => 'MLI', 'phone_code' => '+223', 'capital' => 'Bamako', 'currency' => 'West African CFA Franc', 'currency_code' => 'XOF'],
            ['name' => 'Burkina Faso', 'code' => 'BFA', 'phone_code' => '+226', 'capital' => 'Ouagadougou', 'currency' => 'West African CFA Franc', 'currency_code' => 'XOF'],
            ['name' => 'Niger', 'code' => 'NER', 'phone_code' => '+227', 'capital' => 'Niamey', 'currency' => 'West African CFA Franc', 'currency_code' => 'XOF'],
            ['name' => 'Chad', 'code' => 'TCD', 'phone_code' => '+235', 'capital' => 'N\'Djamena', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Cameroon', 'code' => 'CMR', 'phone_code' => '+237', 'capital' => 'Yaoundé', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Central African Republic', 'code' => 'CAF', 'phone_code' => '+236', 'capital' => 'Bangui', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Equatorial Guinea', 'code' => 'GNQ', 'phone_code' => '+240', 'capital' => 'Malabo', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Gabon', 'code' => 'GAB', 'phone_code' => '+241', 'capital' => 'Libreville', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Republic of the Congo', 'code' => 'COG', 'phone_code' => '+242', 'capital' => 'Brazzaville', 'currency' => 'Central African CFA Franc', 'currency_code' => 'XAF'],
            ['name' => 'Democratic Republic of the Congo', 'code' => 'COD', 'phone_code' => '+243', 'capital' => 'Kinshasa', 'currency' => 'Congolese Franc', 'currency_code' => 'CDF'],
            ['name' => 'Angola', 'code' => 'AGO', 'phone_code' => '+244', 'capital' => 'Luanda', 'currency' => 'Angolan Kwanza', 'currency_code' => 'AOA'],
            ['name' => 'Zambia', 'code' => 'ZMB', 'phone_code' => '+260', 'capital' => 'Lusaka', 'currency' => 'Zambian Kwacha', 'currency_code' => 'ZMW'],
            ['name' => 'Zimbabwe', 'code' => 'ZWE', 'phone_code' => '+263', 'capital' => 'Harare', 'currency' => 'Zimbabwean Dollar', 'currency_code' => 'ZWL'],
            ['name' => 'Botswana', 'code' => 'BWA', 'phone_code' => '+267', 'capital' => 'Gaborone', 'currency' => 'Botswana Pula', 'currency_code' => 'BWP'],
            ['name' => 'Namibia', 'code' => 'NAM', 'phone_code' => '+264', 'capital' => 'Windhoek', 'currency' => 'Namibian Dollar', 'currency_code' => 'NAD'],
            ['name' => 'Lesotho', 'code' => 'LSO', 'phone_code' => '+266', 'capital' => 'Maseru', 'currency' => 'Lesotho Loti', 'currency_code' => 'LSL'],
            ['name' => 'Eswatini', 'code' => 'SWZ', 'phone_code' => '+268', 'capital' => 'Mbabane', 'currency' => 'Swazi Lilangeni', 'currency_code' => 'SZL'],
            ['name' => 'Madagascar', 'code' => 'MDG', 'phone_code' => '+261', 'capital' => 'Antananarivo', 'currency' => 'Malagasy Ariary', 'currency_code' => 'MGA'],
            ['name' => 'Mauritius', 'code' => 'MUS', 'phone_code' => '+230', 'capital' => 'Port Louis', 'currency' => 'Mauritian Rupee', 'currency_code' => 'MUR'],
            ['name' => 'Seychelles', 'code' => 'SYC', 'phone_code' => '+248', 'capital' => 'Victoria', 'currency' => 'Seychellois Rupee', 'currency_code' => 'SCR'],
            ['name' => 'Comoros', 'code' => 'COM', 'phone_code' => '+269', 'capital' => 'Moroni', 'currency' => 'Comorian Franc', 'currency_code' => 'KMF'],
            ['name' => 'Djibouti', 'code' => 'DJI', 'phone_code' => '+253', 'capital' => 'Djibouti', 'currency' => 'Djiboutian Franc', 'currency_code' => 'DJF'],
            ['name' => 'Somalia', 'code' => 'SOM', 'phone_code' => '+252', 'capital' => 'Mogadishu', 'currency' => 'Somali Shilling', 'currency_code' => 'SOS'],
            ['name' => 'Eritrea', 'code' => 'ERI', 'phone_code' => '+291', 'capital' => 'Asmara', 'currency' => 'Eritrean Nakfa', 'currency_code' => 'ERN'],
            ['name' => 'Burundi', 'code' => 'BDI', 'phone_code' => '+257', 'capital' => 'Gitega', 'currency' => 'Burundian Franc', 'currency_code' => 'BIF'],
            ['name' => 'Rwanda', 'code' => 'RWA', 'phone_code' => '+250', 'capital' => 'Kigali', 'currency' => 'Rwandan Franc', 'currency_code' => 'RWF'],
            ['name' => 'South Sudan', 'code' => 'SSD', 'phone_code' => '+211', 'capital' => 'Juba', 'currency' => 'South Sudanese Pound', 'currency_code' => 'SSP'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'phone_code' => $country['phone_code'],
                    'capital' => $country['capital'],
                    'currency' => $country['currency'],
                    'currency_code' => $country['currency_code'],
                    'status' => true,
                ]
            );
        }
    }
}

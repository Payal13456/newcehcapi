<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\States;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $name = [
            "Andaman and Nicobar Islands",
	        "Andhra Pradesh",
	        "Arunachal Pradesh",
	        "Assam",
	        "Bihar",
	        "Chandigarh",
	        "Chhattisgarh",
	        "Dadra and Nagar Haveli",
	        "Daman and Diu",
	        "Delhi",
	        "Goa",
	        "Gujarat",
	        "Haryana",
	        "Himachal Pradesh",
	        "Jammu and Kashmir",
	        "Jharkhand",
	        "Karnataka",
	        "Kerala",
	        "Ladakh",
	        "Lakshadweep",
	        "Madhya Pradesh",
	        "Maharashtra",
	        "Manipur",
	        "Meghalaya",
	        "Mizoram",
	        "Nagaland",
	        "Odisha",
	        "Puducherry",
	        "Punjab",
	        "Rajasthan",
	        "Sikkim",
	        "Tamil Nadu",
	        "Telangana",
	        "Tripura",
	        "Uttar Pradesh",
	        "Uttarakhand",
	        "West Bengal"         
        ];

        foreach ($name as $n) {
             States::create(['state_name' => $n,'ip_address'=>'127.0.0.1']);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
class StateTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $indianStates = [
             'Andhra Pradesh',
             'Arunachal Pradesh',
             'Assam',
             'Bihar',
             'Chhattisgarh',
             'Goa',
             'Gujarat',
             'Haryana',
             'Himachal Pradesh',
             'Jammu and Kashmir',
             'Jharkhand',
             'Karnataka',
             'Kerala',
             'Madhya Pradesh',
             'Maharashtra',
             'Manipur',
             'Meghalaya',
             'Mizoram',
             'Nagaland',
             'Odisha',
             'Punjab',
             'Rajasthan',
             'Sikkim',
             'Tamil Nadu',
             'Telangana',
             'Tripura',
             'Uttar Pradesh',
             'Uttarakhand',
             'West Bengal',
             'Andaman and Nicobar Islands',
             'Chandigarh',
             'Dadra and Nagar Haveli',
             'Daman and Diu',
             'Lakshadweep',
             'National Capital Territory of Delhi',
             'Puducherry'
        ];

        foreach($indianStates as $state){
            State::create(['state_name'=>$state]);
        }
    }
}

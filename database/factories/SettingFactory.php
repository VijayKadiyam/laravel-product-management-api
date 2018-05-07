<?php

use App\Setting;
use Faker\Generator as Faker;

$factory->define(Setting::class, function (Faker $faker) {
    return [
      'company_id'  =>  '1',
      'bill_format' =>  'OSVL'
    ];
});

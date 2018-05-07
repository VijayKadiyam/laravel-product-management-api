<?php

use Faker\Generator as Faker;

$factory->define(\App\ProductCategory::class, function (Faker $faker) {
  return [
    'company_id'  =>  factory(\App\Company::class)->create()->id,
    'name'  =>  'Silico Plast',
    'hsn_code'  =>  '3824600',
    'quantity_left' =>  0
  ];
});

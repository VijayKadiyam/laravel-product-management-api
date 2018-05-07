<?php

use App\Billing;
use Faker\Generator as Faker;

$factory->define(Billing::class, function (Faker $faker) {
  return [
    'company_id'  =>  factory(\App\Company::class)->create()->id,
    'customer_id' =>  factory(\App\Customer::class)->create()->id,
    'bill_no'     =>  5
  ];
});

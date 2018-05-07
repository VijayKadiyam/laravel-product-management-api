<?php

use App\BillingDiscount;
use Faker\Generator as Faker;

$factory->define(BillingDiscount::class, function (Faker $faker) {
  return [
    'billing_id'  =>  '1',
    'discount_id' =>  '2',
    'amount' =>  '500' 
  ];
});

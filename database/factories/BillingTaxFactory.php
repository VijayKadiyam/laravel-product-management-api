<?php

use App\BillingTax;
use Faker\Generator as Faker;

$factory->define(BillingTax::class, function (Faker $faker) {
  return [
    'billing_id'  =>  '1',
    'tax_id' =>  '1',
    'amount' =>  '500'
  ];
});

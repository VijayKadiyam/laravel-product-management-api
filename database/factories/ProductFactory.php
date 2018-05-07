<?php

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
  return [
    'product_category_id' =>  factory(\App\ProductCategory::class)->create()->id,
    'qty' =>  '20'
  ];
});

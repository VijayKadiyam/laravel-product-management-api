<?php

use App\Module;
use Faker\Generator as Faker;

$factory->define(Module::class, function (Faker $faker) {
  return [
    'module'  =>  'inquiries'
  ];
});

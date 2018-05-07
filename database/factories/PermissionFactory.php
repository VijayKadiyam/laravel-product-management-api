<?php

use App\Module;
use App\Permission;
use Faker\Generator as Faker;

$factory->define(Permission::class, function (Faker $faker) {
  return [
    'module_id'   =>  factory(Module::class)->create()->id,
    'permission'  =>  'status',
    'label'       =>  'Add Status'
  ];
});

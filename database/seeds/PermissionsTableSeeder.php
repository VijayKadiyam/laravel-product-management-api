<?php

use App\Module;
use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Permission::truncate();
    Module::truncate();

    $module = factory(Module::class)->create([ 'module' => 'Product Sales' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'product_sales',
      'label'       =>  'Full Access on Product Sales'
    ]);

    $module = factory(Module::class)->create([ 'module' => 'Product Categories' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'product_categories',
      'label'       =>  'Full Access on Product Categories'
    ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'add_edit_products',
      'label'       =>  'Add/Edit Products'
    ]);

    $module = factory(Module::class)->create([ 'module' => 'Stock Categories' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'stock_categories',
      'label'       =>  'Full Access on Stock Categories'
    ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'add_edit_stocks',
      'label'       =>  'Add/Edit Stocks'
    ]);

    $module = factory(Module::class)->create([ 'module' => 'Suppliers' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'suppliers',
      'label'       =>  'Full Access on Suppliers'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Customers' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'customers',
      'label'       =>  'Full Access on Customers'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Settings' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'settings',
      'label'       =>  'Full Access on Settings'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Units' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'units',
      'label'       =>  'Full Access on Units'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Tax Percents' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'tax_percents',
      'label'       =>  'Full Access on Tax Percents'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Discount Percents' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'discount_percents',
      'label'       =>  'Full Access on Discount Percents'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Users' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'users',
      'label'       =>  'Full Access on Users'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Roles' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'roles',
      'label'       =>  'Full Access on Roles'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Permissions' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'permissions',
      'label'       =>  'Full Access on Permissions'
    ]); 
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'assign_permission_to_role',
      'label'       =>  'Assign Permission to Role'
    ]); 

    $module = factory(Module::class)->create([ 'module' => 'Modules' ]);
    Permission::create([
      'module_id'   =>  $module->id,
      'permission'  =>  'modules',
      'label'       =>  'Full Access on Modules'
    ]); 

  }
}

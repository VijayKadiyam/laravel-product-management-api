<?php

use App\Role;
use App\User;
use App\Company;
use App\Permission;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = User::where('email', '=', 'super@admin.com')->first();
    if(!$user) {
      $user = factory(User::class)->create([
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => bcrypt('123456')
      ]);

      $company = factory(Company::class)->create([
        'name'  =>  'AAIBUZZ'
      ]);
      $company->assignUser($user);

      $role = factory(Role::class)->create([
        'company_id'  =>  $company->id,
        'role'        =>  'SuperAdmin'
      ]);
      $user->assignRole($role); 
      $permissions = Permission::get()->pluck('id');
      $role->givePermission($permissions); 
    }
  }
}

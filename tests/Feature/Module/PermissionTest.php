<?php

namespace Tests\Feature\Module;

use App\Role;
use App\Module;
use App\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionTest extends TestCase
{
  use DatabaseTransactions;

  protected $role, $module, $permission;

  public function setUp()
  {
    parent::setUp();

    $this->role = factory(Role::class)->create([
      'company_id'  =>  $this->company->id,
      'role'        =>  'Admin'
    ]);

    $this->module = factory(Module::class)->create([
      'module'  =>  'inquiries'
    ]);
  }

  /** @test */
  function user_is_logged_in()
  {
    $this->json('post', '/api/permissions')
      ->assertStatus(401);
  }

  /** @test */
  function it_requires_moduleId_permission_and_label()
  {
    $this->json('post', '/api/permissions', [], $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function permissions_fetched_successfully()
  {
    factory(Permission::class)->create([
      'module_id'   =>  $this->module->id,
      'permission'  => 'inquiries',
      'label'       => 'Perform all operations on inquiries' 
    ]);

    $this->json('get', '/api/permissions', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          0 =>  [
            'module_id'   =>  $this->module->id,
            'permission'  => 'inquiries',
            'label'       => 'Perform all operations on inquiries',
            'module'      =>  [
              'id'  =>  $this->module->id
            ]
          ]
        ]
      ]); 
  }

  /** @test */
  function permission_saved_successfully()
  { 
    $this->disableEH();

    $payload = [
      'module_id'   =>  $this->module->id,
      'permission'  => 'inquiries',
      'label'       => 'Perform all operations on inquiries' 
    ];

    $this->json('post', '/api/permissions', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'module_id'   =>  $this->module->id,
          'permission'  => 'inquiries',
          'label'       => 'Perform all operations on inquiries' 
        ]
      ]);
  }

  /** @test */
  function single_permission_fetched_successfully()
  {
    $permission = factory(Permission::class)->create([
      'permission'  => 'inquiries',
      'label'       => 'Perform all operations on inquiries' 
    ]);

    $this->json('get', "/api/permissions/$permission->id", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'permission'  => 'inquiries',
          'label'       => 'Perform all operations on inquiries' 
        ]
      ]);
  }

  /** @test */
  function it_requires_permission_and_label_while_updating()
  {
    $permission = factory(Permission::class)->create([
      'permission'  => 'inquiries',
      'label'       => 'Perform all operations on inquiries' 
    ]);

    $this->json('patch', "/api/permissions/$permission->id", [], $this->headers)
      ->assertStatus(422);
  }

  /** @test */
  function permission_updated_successfully()
  {
    $permission = factory(Permission::class)->create([
      'permission'  => 'inquiries',
      'label'       => 'Perform all operations on inquiries' 
    ]);
    $permission->label = 'Perform all operations on the inquiries';

    $this->json('patch', "/api/permissions/$permission->id", $permission->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'permission'  => 'inquiries',
          'label'       => 'Perform all operations on the inquiries' 
        ]
      ]);
  } 

  /** @test */
  function assign_permission_to_a_role()
  {
    $this->permission = factory(Permission::class)->create();

    $this->role->givePermission([ $this->permission->id ]);

    $this->assertCount(1, $this->role->permissions);
  }

  /** @test */
  function it_requires_permissionId_and_roleId_while_assigning_permission()
  {
    $this->json('post', '/api/roles/assign-permissions', [], $this->headers)
      ->assertStatus(422);
  }

  /** @test */
  function assign_permission_to_a_role_url_test()
  {
    $this->disableEH();

    $permission = factory(Permission::class)->create();

    $payload = [
      'permissionIds' =>  [
        0 =>  $permission->id
      ],
      'role_id'       =>  $this->role->id
    ];

    $this->json('post', '/api/roles/assign-permissions', $payload, $this->headers)
      ->assertStatus(200);

    $this->role->refresh();

    $this->assertCount(1, $this->role->permissions);
  } 
}

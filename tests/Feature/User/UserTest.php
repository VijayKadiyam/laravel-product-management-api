<?php

namespace Tests\Feature\User;

use App\Role;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
  use DatabaseTransactions;

  protected $role;

  public function setUp()
  {
    parent::setUp();

    $this->role = factory(Role::class)->create([
      'role'  =>  'user'
    ]);
  }

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/users')
      ->assertStatus(401);
  }

  /** @test */
  function it_requires_name_email_password_password_confirmation_and_roleId()
  {
    $this->json('post', '/api/users', [], $this->headers)
      ->assertStatus(422);
  }

  public function users_are_fetched_successfully()
  {
    $user = factory(\App\User::class)->create([
      'name'  =>  'Vijay',
      'email' =>  'vjfrnd@gmail.com',
      'password'  =>  bcrypt('123456')
    ]); 
    $user->addAsEmployeeTo($this->user);
    $this->user->assignCompany($this->company);

    $this->json('get', '/api/users', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
        'data'  =>  [
          0 =>  [
            'name',
            'email'
          ]
        ] 
      ]);

    $this->assertCount(1, $this->user->employees);
  }

  /** @test */
  function user_saved_successfully()
  {
    $this->disableEH();

    $payload = [ 
      'name'  =>  'vijay',
      'email' =>  'vjfrnd@gmail.com',
      'password'  =>  '123456',
      'password_confirmation'  =>  '123456',
      'role_id'   =>  $this->role->id
    ];

    $this->assertCount(1, $this->company->users);

    $this->json('post', '/api/users', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'vijay',
          'email' =>  'vjfrnd@gmail.com' 
        ]
      ]);

    $payload['email'] =  'vjfrnd1@gmail.com';

    $this->json('post', '/api/users', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'vijay',
          'email' =>  'vjfrnd1@gmail.com' 
        ]
      ]); 

    $user = User::where('email', '=', 'vjfrnd@gmail.com')->first();

    $this->assertCount(2, $this->user->employees);

    $user->refresh();
    $this->assertCount(1, $user->roles);

  }

  /** @test */
  function user_is_assigned_a_company()
  {
    $user = factory(\App\User::class)->create([
      'name'  =>  'Vijay',
      'email' =>  'vjfrnd@gmail.com',
      'password'  =>  bcrypt('123456')
    ]);

    $user->assignCompany($this->company->id);

    $this->assertCount(1, $user->companies);
  }

  /** @test */
  function single_user_fetched_successfully()
  {
    $this->disableEH();
    
    $user = factory(\App\User::class)->create([
      'name'  =>  'Vijay',
      'email' =>  'vjfrnd@gmail.com',
      'password'  =>  bcrypt('123456')
    ]); 
    $this->user->addAsEmployeeTo($user);

    $this->json('get', "/api/users/$user->id", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'Vijay',
          'email' =>  'vjfrnd@gmail.com'
        ]
      ]);
  }

  /** @test */
  function user_updated_successfully()
  {
    $user = factory(\App\User::class)->create([
      'name'  =>  'Vijay',
      'email' =>  'vjfrnd@gmail.com',
      'password'  =>  bcrypt('123456') 
    ]); 
    $this->user->addAsEmployeeTo($user);
    $user->assignCompany($this->company->id);

    $user->name = "Ajay";
    $user->role_id = $this->role->id;

    $this->json('patch', "/api/users/$user->id", $user->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'name'  =>  'Ajay',
          'email' =>  'vjfrnd@gmail.com',
        ]
      ]);

    $this->assertCount(1, $user->roles);
  }

  /** @test */
  function assign_role_to_a_user()
  {
    $this->disableEH();

    $role = factory(Role::class)->create();

    $payload = [
      'user_id'       =>  $this->user->id,
      'roleIds' =>  [
        0 =>  $role->id
      ],
      
    ];

    $this->json('post', '/api/users/assign-roles', $payload, $this->headers)
      ->assertStatus(200);

    $this->user->refresh();

    $this->assertCount(1, $this->user->roles);
  } 
  
}

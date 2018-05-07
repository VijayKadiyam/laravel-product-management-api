<?php

namespace Tests\Feature\Module;

use App\Module;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModuleTest extends TestCase
{
  use DatabaseTransactions;

  protected $module;

  public function setUp()
  {
    parent::setUp();

    $this->module = factory(Module::class)->create([
      'module'  =>  'inquiries'
    ]);
  }

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/modules')
      ->assertStatus(401);  
  }

  /** @test */
  function it_requires_module()
  {
    $this->json('post', '/api/modules', [], $this->headers)
      ->assertStatus(422); 
  }

  /** @test */
  function modules_fetched_successfully()
  {
    $this->json('get', '/api/modules', [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          0 =>  [
            'module'  =>  'inquiries'
          ]
        ]
      ]);
  }

  /** @test */
  function module_saved_successfully()
  {
    $this->disableEH();

    $payload = [
      'module'  =>  'inquiries'
    ];

    $this->json('post', '/api/modules', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'module'  =>  'inquiries'
        ]
      ]); 
  }

  /** @test */
  function single_module_fetched_successfully()
  {
    $this->json('get', '/api/modules/'. $this->module->id, [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'module'  =>  'inquiries'
        ]
      ]); 
  }

  /** @test */
  function module_updated_successfully()
  {
    $this->module->module = "permissions";
    $this->json('patch', '/api/modules/'. $this->module->id, $this->module->toArray(), $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'module'  =>  'permissions'
        ]
      ]);
  }
}

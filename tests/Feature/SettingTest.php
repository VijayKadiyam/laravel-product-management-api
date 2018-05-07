<?php

namespace Tests\Feature;

use App\Setting;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingTest extends TestCase
{
  use DatabaseTransactions;

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/settings')
      ->assertStatus(401); 
  } 

  /** @test */
  function setting_saved_successfully()
  {
    $payload = [
      'bill_format' =>  'OSVL/501'
    ];

    $this->json('post', '/api/settings', $payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'bill_format' =>  'OSVL/501'
        ]
      ]); 
  }

  /** @test */
  function fetch_single_setting()
  {
    $this->disableEH();
    
    $setting = factory(Setting::class)->create([
      'company_id'  =>  $this->company->id,
      'bill_format' =>  'OSVL/501'
    ]);

    $this->json('get', "/api/settings", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
        'data'  =>  [
          'bill_format' =>  'OSVL/501'
        ]
      ]); 
  }

  /** @test */
  function setting_updated_successfully()
  {
    $setting = factory(Setting::class)->create([
      'company_id'  =>  $this->company->id,
      'bill_format' =>  'OSVL/501'
    ]);
    $setting->bill_format = 'OSVL/502';

    $this->json('post', "/api/settings", $setting->toArray(), $this->headers)
      ->assertStatus(201)
      ->assertJson([
        'data'  =>  [
          'bill_format' =>  'OSVL/502'
        ]
      ]); 
  }
}

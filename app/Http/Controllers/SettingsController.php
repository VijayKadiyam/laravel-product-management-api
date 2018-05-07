<?php

namespace App\Http\Controllers;

use App\Company;
use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To store the setting
   *
   *@
   */
  public function store(Request $request)
  {
    $setting = new Setting($request->all());
    $setting->store();

    return response()->json([
      'data'  =>  $setting->toArray()
    ], 201);
  }

  /*
   * To get a single setting
   *
   *@
   */
  public function index()
  {
    $company = Company::find(request()->header('company_id'));
    if($company) {
      $settings = $company->settings->count() ? $company->settings[0] : '' ;
    } 

    return response()->json([
      'data'  =>  $settings
    ], 200);
  }

  /*
   * To update a setting
   *
   *@
   */
  public function update(Request $request, Setting $setting)
  {
    $setting->update($request->all());

    return response()->json([
      'data'  =>  $setting->toArray()
    ], 200);
  }
}

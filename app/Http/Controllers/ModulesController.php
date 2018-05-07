<?php

namespace App\Http\Controllers;

use App\Module;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To get all the modules
   *
   *@
   */
  public function index()
  {
    $modules = Module::with('permissions')->latest()->get();

    return response()->json([
      'data'  =>  $modules
    ]);
  }

  /*
   * To store a module
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'module'   => 'required'
    ]);

    $module = new Module($request->all());
    $module->save();

    return response()->json([
      'data'  =>  $module->toArray()
    ], 201);  
  }

  /*
   * To show a module
   *
   *@
   */
  public function show(Module $module)
  {
    return response()->json([
      'data'  =>  $module->toArray()
    ]); 
  }

  /*
   * TO update a module
   *
   *@
   */
  public function update(Request $request, Module $module)
  {
    $module->update($request->all());

    return response()->json([
      'data'  =>  $module->toArray()
    ]);
  }
}

<?php

namespace App\Http\Controllers;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To fetch all the permissions
   *
   *@
   */
  public function index()
  {
    $permissions = Permission::with('module')->latest()->get();

    return response()->json([
      'data'  =>  $permissions
    ], 200);
  }

  /*
   * To store a permission
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'module_id'   =>  'required',
      'permission'  =>  'required',
      'label'       =>  'required'
    ]);

    $permission = new Permission($request->all());
    $permission->save();

    return response()->json([
      'data'  =>  $permission->toArray()
    ], 201);
  }

  /*
   * To get a single permission
   *
   *@
   */
  public function show(Permission $permission)
  {
    return response()->json([
      'data'  =>  $permission->toArray()
    ], 200);
  }

  /*
   * To update a permission
   *
   *@
   */
  public function update(Request $request, Permission $permission)
  {
    $request->validate([
      'module_id'   =>  'required',
      'permission'  =>  'required',
      'label'       =>  'required'
    ]);

    $permission->update($request->all());

    return response()->json([
      'data'  =>  $permission->toArray()
    ], 200);
  }
}

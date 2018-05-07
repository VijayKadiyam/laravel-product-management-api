<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api');
  }

  /*
   * To get all the employees
   *
   *@
   */
  public function index()
  {
    $employees = \Auth::guard('api')->user()->employees;

    return response()->json([
      'data'  =>  $employees
    ], 200);
  }

  /*
   * To store a new user
   *
   *@
   */
  public function store(Request $request)
  {
    $request->validate([
      'name'        => 'required|string|max:255',
      'email'       => 'required|string|email|max:255|unique:users',
      'password'    => 'required|string|min:6|confirmed',
      'role_id'     =>  'required'
    ]);

    $user = User::store(); 

    return response()->json([
      'data'  =>  $user->toArray()
    ], 201);
  }

  /*
   * To get a single user
   *
   *@
   */
  public function show(User $user)
  {
    $user = \Auth::guard('api')->user()->employees()->find($user->id) ? \Auth::guard('api')->user()->employees()->find($user->id) : $user; 

    return response()->json([
      'data'  =>  $user->toArray()
    ], 200);
  }

  /*
   * To update user details
   *
   *@
   */
  public function update(Request $request, User $user)
  {
    $request->validate([
      'name'        => 'required|string|max:255',
      'role_id'     =>  'required'
    ]);

    $user->update($request->only(['name']));
    
    $role = Role::where('id', '=', request()->role_id)->first();
    $user->assignRole($role); 

    return response()->json([
      'data'  =>  $user->toArray()
    ], 200);
  }

  /*
   * To assign a role to a user
   *
   *@
   */
  public function assignroles(Request $request)
  {
    $request->validate([
      'user_id' =>  'required',
      'roleIds' =>  'required'
    ]);

    $user = User::find($request->user_id);
    $user->assignRole($request->roleIds); 
  }
}

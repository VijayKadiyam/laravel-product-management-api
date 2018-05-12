<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $fillable = [
    'role'
  ];

  /*
   * A role belongs to a company
   *
   *@
   */
  public function company()
  {
    $this->belongsTo(Company::class);
  }

  /*
   * To store a new role
   *
   *@
   */
  public function store()
  {
    if(request()->header('company-id')) {
      $company = Company::find(request()->header('company-id'));
      if($company)
        $company ? $company->roles()->save($this) : '';
    } 

    return $this;
  }

  /*
   * A role belongs to many permissions
   *
   *@
   */
  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }

  /*
   * To give permission to a role
   *
   *@
   */
  public function givePermission($permissions)
  {
    return $this->permissions()->sync($permissions);
  }
}

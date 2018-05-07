<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $fillable = [
    'module_id', 'permission', 'label'
  ];

  /*
   * A permission belongs to many roles
   *
   *@
   */
  public function roles()
  {
    return $this->belongsToMany(Role::class);
  }

  /*
   * A permission belongs to module
   *
   *@
   */
  public function module()
  {
    return $this->belongsTo(Module::class);
  }
}

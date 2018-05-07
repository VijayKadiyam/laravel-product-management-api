<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
  protected $fillable = [
    'module'
  ]; 

  /*
   * A module has many permissions
   *
   *@
   */
  public function permissions()
  {
    return $this->hasMany(Permission::class);
  }
}

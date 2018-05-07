<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  protected $fillable = [
    'bill_format'
  ];

  /*
   * A setting belongs to a company
   *
   *@
   */
  public function company()
  {
    $this->belongsTo(Company::class);
  }

  /*
   * To store a new setting
   *
   *@
   */
  public function store()
  {
    if(request()->header('company_id')) {
      $company = Company::find(request()->header('company_id'));
      if($company) {
        $setting = Setting::where('company_id', '=', $company->id)->first();
        if($setting)
          $setting->update($this->toArray());
        else
          $company ? $company->settings()->save($this) : '';
      }
    } 

    return $this;
  } 
}

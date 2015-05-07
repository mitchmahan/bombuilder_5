<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model {

  protected $primaryKey = 'site_id';
  protected $fillable = array('site_name', 'site_desc', 'image', 'panel_regex', 'department', 'url', 'network');

  public function boms() {
    return $this->belongsToMany('App\Models\Bom', 'sites_boms');
  }

}

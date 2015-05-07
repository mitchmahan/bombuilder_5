<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model {

  protected $primaryKey = 'part_id';
  protected $fillable = array(
    'part_name', 
    'vendor_id', 
    'descr', 
    'rmu', 
    'power', 
    'level3_pn', 
    'price'
  );
  public $timestamps = false;

  public function vendor() {
    return $this->hasOne('App\Models\Vendor', 'vendor_id', 'vendor_id');
  } 

  public function urls() {
    return $this->belongsToMany('App\Models\Url', 'part_url_map', 'part_id', 'url_id');
  }

  public function children() {
    return $this->belongsToMany('App\Models\Part', 'parts_containers', 'parent_id', 'child_id')
      ->withPivot('count', 'optional');
  }

  public function parents() {
    return $this->belongsToMany('App\Models\Part', 'parts_containers', 'child_id', 'parent_id')
      ->withPivot('count', 'optional');
  }

  public function boms() {
    return $this->belongsToMany('App\Models\Bom', 'boms_parts_map');
  }

};

?>

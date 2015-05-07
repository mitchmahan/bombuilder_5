<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cablerun extends Model { 

  protected $primaryKey = "cablerun_id";
  protected $fillable = array(
    'cable_id', 
    'run_name', 
    'ne_id', 
    'ne_port', 
    'ne_type',
    'remote_ne_id',
    'remote_ne_type',
    'remote_ne_port',
    'notes'
  );
  public $timestamps = false;

  public function cable() {
    return $this->hasOne('App\Models\Cable', 'cable_id', 'cable_id');
  }

  public function boms(){
    return $this->belongsToMany('App\Models\Bom', 'boms_cableruns_map');
  }
};

?>

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cable extends Model {
  protected $primaryKey = 'cable_id';
  protected $fillable = array('type', 'maxlength', 'bandwidth');
  public $timestamps = false;

  public function cableruns(){
    return $this->hasMany('App\Models\Cablerun');
  }
};

?>

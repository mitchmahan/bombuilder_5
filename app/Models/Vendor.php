<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model {
  protected $primaryKey = 'vendor_id';
  public $timestamps = false;

  protected $fillable = array('vendor_name', 'comment');

  public function parts(){
    return $this->hasMany('App\Models\Part');
  }
};

?>

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model {
  protected $primaryKey = 'url_id';
  protected $fillable = array('url');

  public $timestamps = false;
}

?>

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model {

  protected $primaryKey = 'bom_id';
  protected $fillable = array('bom_name', 'author', 'bom_desc');

  public function author() {
    return $this->belongsTo('App\Models\User');
  }

  public function parts(){
    return $this->belongsToMany('App\Models\Part', 'boms_parts_map')->withPivot('optional', 'count');
  }

  public function cableruns(){
    return $this->belongsToMany('App\Models\Cablerun', 'boms_cableruns_map');
  }

  public function urls(){
    return $this->belongsToMany('App\Models\Url', 'boms_urls_map')->withPivot('mode');
  }

  public function createdAt() {
    if ( $this->created_at->diffInDays() > 30 ){
      return "30+ Days Ago";
    }else{
      return $this->created_at->diffForHumans();
    }
  }

};

?>

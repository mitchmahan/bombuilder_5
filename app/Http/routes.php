<?php namespace App\Http;

/**
 * Index Route
 *
 */
use Route;
use View;
use Auth;
use Redirect;
use Input;

use App\Models\Bom;
use App\Models\Site;
use App\Models\Part;


/**
 * Include default authentication routes
 */
Route::controllers([
  'auth' => 'Auth\AuthController',
  'password' => 'Auth\PasswordController',
]);


/**
 * Home page!
 */
Route::get('/', function()
  {
    $boms = Bom::orderBy('bom_id', 'DESC')->take(5)->get();
    $parts = Part::all()->take(5)->sortBy('part_id')->reverse();
    $sites = Site::all();
    $site_list = array();

    foreach($sites as $site){
      if( ! isset( $site_list[$site['network']] ) ){
        // Create new array for our network type
        $site_list[$site['network']] = array();
      }
      // Add this site to its network type
      array_push($site_list[$site['network']], $site);
    }

    return View::make('index')->with(array(
      'boms' => $boms,
      'parts' => $parts,
      'site_list' => $site_list
    ));
  });


/**
 * Main /bom/{id} route for unauthenticated users
 */
Route::get('/bom/{id}', 'BomController@show');
Route::get('/bom', 'BomController@index');


/**
 * User logout
 */
Route::get('user/logout', function()
      {
        Auth::logout();
        return Redirect::to('/');
      });


/**
 * Routes for Gridster Panels and Panel functions
 */
Route::get('/panels', function()
      {

        if( Input::exists('network') )
        {
          $sites = Site::where('network', '=', Input::get('network') )->with('boms')->get();
        }else{
          $sites = Site::all();
          $sites->load('boms');
        }

        $panels = array();
        foreach($sites as $site){
          $panels[$site->site_name] = array();
          $panels[$site->site_name]['name'] = $site->site_name;
          $panels[$site->site_name]['id'] = $site->site_id;
          $panels[$site->site_name]['panels'] = array();

          if($site->panel_regex)
          {
            $model = Bom::where('bom_name', 'like', "%$site->panel_regex%")->take(50)->get();

            $panels[$site->site_name]['panels'] = array_map( function($array){
              return array('value' => $array['bom_name'], 'id' => $array['bom_id']);
            }, $model->toArray());
          }

          $panels[$site->site_name]['panels'] = array_merge(
            $panels[$site->site_name]['panels'], 
            array_map( function($array){
              return array('value' => $array['bom_name'], 'id' => $array['bom_id']);
            }, $site->boms->toArray()) 
            );

        }
        return $panels;
      });

/**
 * Get information for a single panel
 */
Route::get('/panel/{id}', function($id)
          {
            $site = Site::find($id);
            $site->load('boms');
            $panels = array();
            $panels['name'] = $site->site_name;
            $panels['id'] = $site->site_id;
            $panels['panels'] = array();

            if($site->panel_regex)
            {
              $model = Bom::where('bom_name', 'like', "%$site->panel_regex%")->take(50)->get();

              $panels['panels'] = array_map( function($array){
                return array('value' => $array['bom_name'], 'id' => $array['bom_id']);
              }, $model->toArray());
            }

            $panels['panels'] = array_merge(
              $panels['panels'], 
              array_map( function($array){
                return array('value' => $array['bom_name'], 'id' => $array['bom_id']);
              }, $site->boms->toArray()) 
              );

            return $panels;

          });


/**
 * Authorized routes for logged in users
 */
Route::group(['middleware' => 'auth'], function()
            {
              /* BOM Actions */
              Route::post('bom', 'BomController@store');
              Route::post('bom/delete', 'BomController@destroy');
              Route::post('bom/{id}', 'BomController@update');
              Route::post('bom/{id}/addPart', 'BomController@addPart');
              Route::post('bom/{id}/addCablerun', 'BomController@addCablerun');
              Route::post('bom/{id}/addUrl', 'BomController@addUrl');
              Route::post('bom/{id}/addImage', 'BomController@addImage');
              Route::post('bom/{id}/addVisio', 'BomController@addVisio');
              Route::post('bom/{id}/delete', 'BomController@delete');

              /* Site Actions */
              Route::post('site/delete', 'SiteController@destroy');
              Route::post('site/{id}', 'SiteController@update');
              Route::post('site/{id}/addBom', 'SiteController@addBom');
              Route::post('site/{id}/delete', 'SiteController@delete');

              /* Part Actions */
              Route::post('part/delete', 'PartController@destroy');
              Route::post('part/{id}', 'PartController@update');
              Route::post('part/{id}/addChild', 'PartController@addChild');
              Route::post('part/{id}/addImage', 'PartController@addImage');
              Route::post('part/{id}/delete', 'PartController@delete');

              /* Cablerun Actions */
              Route::post('cablerun/delete', 'CablerunController@delete');
              Route::post('cablerun/{id}', 'CablerunController@update');

              /* Cablerun Actions */
              Route::post('cable/delete', 'CableController@delete');
              Route::post('cable/{id}', 'CableController@update');

              /* Vendor Actions */
              Route::post('vendor/delete', 'VendorController@delete');
              Route::post('vendor/{id}', 'VendorController@update');

              /* User Actions */
              Route::post('user/delete', 'UserController@delete');
              Route::post('user/{id}','UserController@postUser');

              /* Default Actions */
              Route::resource('site','SiteController');
              Route::resource('part','PartController');
              Route::resource('cable','CableController');
              Route::resource('cablerun','CablerunController');
              Route::resource('vendor','VendorController');
              Route::resource('user','UserController');

            });
?>

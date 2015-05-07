<?php namespace App\Http\Controllers;

use Auth;
use Input;
use Request;
use Response;
use Validator;
use Session;
use Redirect;
use View;
use Exception;

use App\Models\Site;
use App\Models\Bom;

class SiteController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    // Return a subset of the parts database (used in searches)
    if( Input::get('query') ){
      $search = Input::get('query');
      $model = Site::where('site_name', 'like', "%$search%")->take(10)->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => $array['site_name'], 'data' => $array['site_id']);
      }, $model->toArray());

      return $results;
    }

    $page = array('name' => 'Sites');
    $data = Site::all();
    $data->load('boms');

    if(Request::ajax()) return $data;

    return View::make('sites')->with( array('page' => $page, 'data' => $data));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
      $rules = array(
        'SiteName' => 'required|min:4|unique:sites,site_name',
        'SiteDesc' => 'max:254',
        'Network'  => 'min:4|max:5',
        'PanelRegex' => 'max:45',
      );

      $validator = Validator::make(Input::all(), $rules);
      if($validator->fails())
      {
        return Response::json(['success' => false, 
          'errors' => $validator->getMessageBag()->toArray()], 
          400);
      }

      $site = new Site([
        'site_name' => Input::get('SiteName'),
        'site_desc' => Input::get('Desc'),
        'network'   => strtolower(Input::get('Network')),
        'panel_regex' => Input::get('PanelRegex')
      ]);

      try{
        // Save our new site
        $site->save();
      }catch(Exception $e){
        Log::warning("An attempt to save site $site->site_name failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to add site. Is this a duplicate?'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'New site created successfully.',
        'id' => $site->site_id,
        'name' => $site->site_name
      );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to('/site');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $site = Site::find($id);

    return View::make('site')->with('site', $site);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
      $rules = array(
        'value' => 'min:1|max:254',
      );

      $validator = Validator::make(Input::all(), $rules);
      if($validator->fails())
      {
        return Response::json(['success' => false, 
          'errors' => $validator->getMessageBag()->toArray()], 
          400);
      }

      $site = Site::find($id);
      $site->{Input::get('name')} = Input::get('value');

      try{
        // Save our new site
        $site->save();
      }catch(Exception $e){
        Log::warning("An attempt to save to $site->site_name failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to add site. Is this a duplicate?'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Site updated successfully.',
        'id' => $site->site_id,
        'name' => $site->site_name
      );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to('/site');
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function delete($id)
  {
    $rules = array(
      'id' => 'required|integer',
      'type'  => 'required'
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $item_id = Input::get('id');
    $type = Input::get('type');

    $response = array(
      'success' => true,
      'msg' => 'Removed item from site.',
      'data' => array('data' => Input::All())
    );

    if( $type == 'Bom' )
    {
      try{
        $site = Site::find($id);
        $site->boms()->detach($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from site $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove bom.'),
          'data' => Input::all()],
          400);
      }
      if(Request::ajax()) return Response::json( $response );
      return Redirect::to("/site");
    }

    return Response::json(['success' => false,
      'errors' => array('No action was performed.'),
      'data' => Input::all()],
      400);
  }

  /**
   * Attach a BOM to a site.
   *
   * @param int $id
   * @param int $BomId
   * @return Response
   */
  public function addBom($id)
  {
      $rules = array(
        'BomId' => 'required|integer',
      );

      $validator = Validator::make(Input::all(), $rules);
      if($validator->fails())
      {
        return Response::json(['success' => false, 
          'errors' => $validator->getMessageBag()->toArray()], 
          400);
      }

      $site = Site::find($id);
      $bom = Bom::find(Input::get('BomId'));

      try{
        // Save our new site
        $site->boms()->attach($bom);
        $site->save();
      }catch(Exception $e){
        Log::warning("An attempt to attach a BOM to site $site->site_name failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to add BOM to site. Is this a duplicate?'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Added BOM successfully.',
        'id' => $site->site_id,
        'name' => $site->site_name
      );

      if(Request::ajax()) return Response::json( $response );
      return Redirect::to("/site/$id");
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy()
  {
    //
    $rules = array(
      'id' => 'required|integer',
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $id = Input::get('id');
    try{
      Site::destroy($id);
    }
    catch(Exception $e){
      Log::warning("An attempt to delete site $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to delete site. Make sure it is empty before deleting it.'),
        'data' => Input::all()],
        400);
    }

    $response = array(
      'success' => true,
      'msg' => 'Deleted Site.',
      'data' => array('Site' => $id, 'input' => Input::All())
    );
    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom");
  }
}

<?php namespace App\Http\Controllers;

use Validator;
use Response;
use Request;
use Input;
use View;
use Exception;

use App\Models\Cablerun;

class CablerunController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    if( Input::exists('query') ){
      $search = Input::get('query');
      $model = Cablerun::where('run_name', 'like', "%$search%")->take(10)->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => $array['run_name'], 'data' => $array['cablerun_id']);
      }, $model->toArray());

      return $results;
    }
    
    $page = array('name' => 'Cableruns');
    $data = Cablerun::all();

    if(Request::ajax()) return $data;
    return View::make('cableruns')->with( array('page' => $page, 'data' => $data) );
  }


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $cablerun = Cablerun::find($id);

    return View::make('cablerun')->with('cablerun', $cablerun);
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

      $cablerun = Cablerun::find($id);
      $cablerun->{Input::get('name')} = Input::get('value');

      try{
        // Save our new site
        $cablerun->save();
      }catch(Exception $e){
        Log::warning("An attempt to save to $cablerun->cablerun_name failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to update item.'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Item updated successfully.'
      );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::back();
  }


  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    $rules = array(
      'RunName'   => 'required|unique:cableruns,run_name',
      'NeId'      => 'required',
      'NeType'    => 'required',
      'NePort'    => 'required',
      'CableId'   => 'required|integer',
      'R_NeId'    => 'required',
      'R_NeType'  => 'required',
      'R_NePort'  => 'required'
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $cablerun = new Cablerun( array(
      'run_name'        => Input::get('RunName'),
      'ne_id'           => Input::get('NeId'),
      'ne_type'         => Input::get('NeType'),
      'ne_port'         => Input::get('NePort'),
      'cable_id'        => Input::get('CableId'),
      'remote_ne_id'    => Input::get('R_NeId'),
      'remote_ne_type'  => Input::get('R_NeType'),
      'remote_ne_port'  => Input::get('R_NePort')
    ));

    if(Input::get('Notes')){
      $cablerun->notes = Input::get('Notes');
    }

    // Try adding a URL to the BOM
    try{
      $cablerun->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a Cablerun failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add Cablerun. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }
    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New Cablerun added to the BOM.',
    );

    return Response::json( $response );
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function delete()
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
      Cablerun::destroy($id);
    }
    catch(Exception $e){
      Log::warning("An attempt to delete cablerun $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to delete cablerun. Is it still in use by a BOM?'),
        'data' => Input::all()],
        400);
    }

    $response = array(
      'success' => true,
      'msg' => 'Deleted cablerun.',
      'data' => array('cablerun' => $id, 'input' => Input::All())
    );
    return Response::json( $response );
  }


}

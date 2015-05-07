<?php namespace App\Http\Controllers;

use Validator;
use Response;
use Request;
use Input;
use View;
use Exception;

use App\Models\Cable;

class CableController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    if( Input::get('query') ){
      $search = Input::get('query');
      $model  = Cable::where('type', 'like', "%$search%")->take(10)->orderBy('type')->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => $array['type'], 'data' => $array['cable_id']);
      }, $model->toArray());

      return $results;
    }

    $page = array('name' => 'Cables');
    $data = Cable::orderBy('type')->get();

    if(Request::ajax()) return $data;
    return View::make('cables')->with( array('page' => $page, 'data' => $data) );
	}


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $cable = Cable::find($id);

    return View::make('cable')->with('cable', $cable);
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

      $cable = Cable::find($id);
      $cable->{Input::get('name')} = Input::get('value');

      try{
        // Save our new site
        $cable->save();
      }catch(Exception $e){
        Log::warning("An attempt to save to $cable->cable_name failed. $e");
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
      'CableType' => 'required',
      'Bandwidth' => 'required',
      'MaxLength' => 'required'
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $cable = new Cable( array(
      'type' => Input::get('CableType'),
      'bandwidth' => Input::get('Bandwidth'),
      'maxlength' => Input::get('MaxLength')
    ));

    try{
      $cable->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a cable failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add cable. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }
    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New cable added to the BOM.',
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
      Cable::destroy($id);
    }
    catch(Exception $e){
      Log::warning("An attempt to delete cable $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to delete cable. Is it still in use by a cablerun?'),
        'data' => Input::all()],
        400);
    }

    $response = array(
      'success' => true,
      'msg' => 'Deleted cable.',
      'data' => array('cable' => $id, 'input' => Input::All())
    );
    return Response::json( $response );
  }


}

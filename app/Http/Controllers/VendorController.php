<?php namespace App\Http\Controllers;

use Validator;
use Response;
use Request;
use Input;
use View;
use Exception;

use App\Models\Vendor;

class VendorController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    if( Input::get('query') ){
      $search = Input::get('query');
      $model = Vendor::where('vendor_name', 'like', "%$search%")->take(3)->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => ucfirst($array['vendor_name']), 'data' => $array['vendor_id']);
      }, $model->toArray());

      return $results;
    }

    $page = array('name' => 'Vendors');
    $data = Vendor::all();

    if(Request::ajax()) return $data;

    return View::make('vendors')->with( array('page' => $page, 'data' => $data));
  }


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $vendor = Vendor::find($id);

    return View::make('vendor')->with('vendor', $vendor);
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

      $vendor = Vendor::find($id);
      $vendor->{Input::get('name')} = Input::get('value');

      try{
        // Save our new site
        $vendor->save();
      }catch(Exception $e){
        Log::warning("An attempt to save to $vendor->vendor failed. $e");
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
      'VendorName' => 'required',
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $vendor = new Vendor( array(
      'vendor_name' => Input::get('VendorName'),
    ));

    if(Input::get('Comment')){
      $vendor->comment = Input::get('Comment');
    }

    // Try adding a URL to the BOM
    try{
      $vendor->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a Vendor failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add Vendor. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }
    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New Vendor added to the BOM.',
    );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/vendor");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function delete()
  {
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
      Vendor::destroy($id);
    }
    catch(Exception $e){
      Log::warning("An attempt to delete vendor $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to delete vendor. Is it still in use by a part?'),
        'data' => Input::all()],
        400);
    }
    $response = array(
      'success' => true,
      'msg' => 'Deleted vendor.',
      'data' => array('vendor' => $id, 'input' => Input::All())
    );
    return Response::json( $response );
  }


}

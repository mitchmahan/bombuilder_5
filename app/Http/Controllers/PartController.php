<?php namespace App\Http\Controllers;

use Auth;
use Input;
use Request;
use Response;
use Validator;
use Session;
use Redirect;
use View;
use Log;
use File;
use Exception;

use App\Models\Part;
use App\Models\Url;

class PartController extends Controller {

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
      $model = Part::where('part_name', 'like', "%$search%")->take(10)->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => $array['part_name'], 'data' => $array['part_id']);
      }, $model->toArray());

      return $results;
    }

    $page = array('name' => 'Parts');
    $data = Part::all();
    $data->load('vendor', 'children', 'children.vendor');

    if(Request::ajax()) return $data;

    return View::make('parts')->with( array('page' => $page, 'data' => $data));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    $rules = array(
      'PartName'    => 'required|unique:parts,part_name',
      'VendorId'    => 'required|integer',
      'ItemMaster'  => 'integer|unique:parts,level3_pn',
      'RMU'         => 'integer'
    );

    $validator = Validator::make(Input::all(), $rules);
    if($validator->fails())
    {
      return Response::json(['success' => false,
        'errors' => $validator->getMessageBag()->toArray()],
        400);
    }

    $part = new Part;

    $part->part_name = Input::get('PartName');
    $part->descr     = Input::get('Descr');
    $part->level3_pn = Input::get('ItemMaster');
    $part->price     = floatval(Input::get('Price'));
    $part->rmu       = Input::get('RMU');
    $part->power     = Input::get('Power');
    $part->vendor_id = Input::get('VendorId');

    $part->save();

    $response = array(
      'success' => true,
      'message' => 'New part '. $part->part_name .'added.',
      'id' => $part->part_id,
      'name' => $part->part_name
    );

    return Response::json( $response );
  }


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $part = Part::find($id);

    return View::make('part')->with('part', $part);
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

      $part = Part::find($id);
      $part->{Input::get('name')} = Input::get('value');

      try{
        // Save our new site
        $part->save();
      }catch(Exception $e){
        Log::warning("An attempt to update item failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to update item.'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Updated successfully.',
      );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to('/site');
  }


  /**
   * Add an image to the BOM
   *
   * @param  int  $id
   * @return Response
   */
  public function addImage($id)
  {
    $rules = array(
      'Image' => 'required|image',
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $file_name = Input::file('Image')->getClientOriginalName();

    try {
      // Just in case... (since we use $id in our move call)
      $id = basename($id);
      (int)$id;
      Input::file('Image')->move(public_path("/part_images/$id"), $file_name);
      Log::info("Image uploaded to /part_images/$id/$file_name");
    }
    catch(Exception $e){
      Log::warning("Unable to move image into storage. Check permissions on upload folder. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add image. Please contact the site admin.')
      ],
      400);

    }

    $file_url = "/part_images/$id/$file_name";
    $url = new Url( array('url' => $file_url ) );
    $part = Part::find($id);

    try {
      $url->save();
      $part->urls()->attach( $url->url_id );
      $part->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add an image to the part $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add link to image. Is this a duplicate?')
      ],
      400);
    }

    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New image added to the part.',
    );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/part/$id");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy()
  {
      $rules = array(
        'id' => 'required|integer',
      );

      $validator = Validator::make(Input::all(), $rules);
      if($validator->fails())
      {
        return Response::json(['success' => false, 
          'errors' => $validator->getMessageBag()->toArray()], 
          400);
      }

      try{
        // Save our new site
        Part::destroy(Input::get('id'));
      }catch(Exception $e){
        Log::warning("An attempt to delete a part failed. $e");
        return Response::json(['success' => false,
          'errors' => array('You must remove all associations before deleting a part.'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Deleted part.'
      );

    return Response::json( $response );
  }


  /**
   * Attach a BOM to a site.
   *
   * @param int $id
   * @param int $BomId
   * @return Response
   */
  public function addChild($id)
  {
      $rules = array(
        'ChildPartId' => 'required|integer',
        'PartCount'   => 'required|integer'
      );

      $validator = Validator::make(Input::all(), $rules);
      if($validator->fails())
      {
        return Response::json(['success' => false, 
          'errors' => $validator->getMessageBag()->toArray()], 
          400);
      }

      $part = Part::find($id);
      $child = Part::find(Input::get('ChildPartId'));

      try{
        // Save our new site
        $part->children()->attach($child, array('count' => Input::get('PartCount')));
        $part->save();
      }catch(Exception $e){
        Log::warning("An attempt to attach a child to part $part->part_name failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to add child to part.'),
          'data' => Input::all()],
          400);
      };

      $response = array(
        'success' => true,
        'message' => 'Added child successfully.',
        'id' => $id,
        'part' => Input::get('ChildPartId')
      );

    return Response::json( $response );
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
      'msg' => 'Removed item from part.',
      'data' => array('part' => $id, 'item' => Input::All())
    );

    if( $type == 'Part' )
    {
      try{
        $part = Part::find($id);
        $part->children()->detach($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from part $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove part.'),
          'data' => Input::all()],
          400);
      }
      return Response::json( $response );
    }

    if( $type == 'Url' )
    {
      $url = Url::find($item_id);

      // Delete any images for a specific BOM
      if( File::exists(public_path($url->url)) ){
        File::delete(public_path($url->url));
      }

      // Delete thumbnails of visio's
      if( File::exists(public_path($url->url.".png")) ){
        File::delete(public_path($url->url.".png"));
      }

      try{
        $part = Part::find($id);
        $part->urls()->detach($item_id);
        Url::destroy($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from part $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove image.'),
          'data' => Input::all()],
          400);
      }
      return Response::json( $response );
    }

    return Response::json(['success' => false,
      'errors' => array('No action was performed.'),
      'data' => Input::all()],
      400);
  }


}

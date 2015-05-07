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

use \App\Models\Bom;
use \App\Models\Part;
use \App\Models\Url;

class BomController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    if( Input::exists('query') ){
      $search = Input::get('query');
      $model = Bom::where('bom_name', 'like', "%$search%")->take(10)->get();

      $results['suggestions'] = array_map( function($array){
        return array('value' => $array['bom_name'], 'data' => $array['bom_id']);
      }, $model->toArray());

      return $results;
    }else{
      $page = array('name' => 'Boms');
      $data = Bom::all();
  
      if(Request::ajax()) return $data;
      return View::make('boms')->with( array('page' => $page, 'data' => $data) );
    }
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

    $bom = Bom::find($id);
    $bom->{Input::get('name')} = Input::get('value');

    try{
      // Save our new site
      $bom->save();
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
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    $rules = array(
      'Bom' => 'required|min:8|unique:boms,bom_name',
      'Desc' => 'max:254'
    );

    $validator = Validator::make(Input::all(), $rules);
    if($validator->fails())
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $bom = new Bom;

    $bom->bom_name = Input::get('Bom');
    $bom->bom_desc = Input::get('Desc');

    $bom->save();

    $response = array(
      'success' => true,
      'message' => 'New bom '. link_to("/bom/$bom->bom_id",$bom->bom_name) . ' created successfully.',
      'id' => $bom->bom_id,
      'name' => $bom->bom_name
    );

    if(Request::ajax()) return Response::json( $response );
  }


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $bom = Bom::find($id);
    // Eagerly load up to 6 layers of "child parts"
    // This reduces our MySQL query count from 40+ to about 11
    $bom->load( 'cableruns', 
      'cableruns.cable',
      'urls',
      'parts',
      'parts.urls',
      'parts.vendor',
      'parts.children',
      'parts.children.children',
      'parts.children.children.children',
      'parts.children.vendor',
      'parts.children.children.vendor',
      'parts.children.children.children.vendor',
      'parts.children.children.children.children',
      'parts.children.children.children.children.vendor'
    );

    return View::make('bom')->with('bom', $bom);
  }


  /**
   * Add a part to the BOM
   *
   * @param  int  $id
   * @input int $PartId
   * @input int $Count
   * @return Response
   */
  public function addPart($id)
  {
    $rules = array(
      'PartId' => 'required|integer',
      'Count' => 'required|integer'
    );

    // Validate input or return an error. Javascript will display the errors in the modal.
    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $partId = Input::get('PartId');
    $count  = array('count' => Input::get('Count') );
    $bom    = Bom::find($id);

    // Add a part to the BOM
    try{
      $bom->parts()->attach( $partId, $count );
      $bom->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a part to bom $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('We were unable to add your part to this BOM. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }

    // Return our success message. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New part added to the BOM.',
    );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom/$id");
  }


  /**
   * Add a URL to the BOM
   *
   * @param  int  $id
   * @return Response
   */
  public function addUrl($id)
  {
    $rules = array(
      'Url' => 'required|url',
      'Transuntrust' => 'integer'
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $url = new Url( array('url' => Input::get('Url')) );
    $bom = Bom::find($id);

    // Try adding a URL to the BOM
    try{
      $url->save();
      $bom->urls()->attach( $url->url_id, array('mode' => e(Input::get('Transuntrust'))));
      $bom->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a URL to bom $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add URL. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }

    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New URL added to the BOM.',
    );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom/$id");
  }


  /**
   * Add a Visio to the BOM
   *
   * @param  int  $id
   * @return Response
   */
  public function addVisio($id)
  {
    $rules = array(
      'Visio' => 'required',
      'Image' => 'required|image|mimes:png'
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    /**
     *
     * Save the Visio File
     *
     */
    $visio_name = Input::file('Visio')->getClientOriginalName();
    try {
      // Just in case... (since we use $id in our move call)
      $id = basename($id);
      (int)$id;
      Input::file('Visio')->move(public_path("/bom_images/$id"), $visio_name);
      Log::info("Visio uploaded.");
    }
    catch(Exception $e){
      Log::warning("Unable to move visio into storage. Check permissions on upload folder. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add visio. Please contact the site admin.')
      ],
      400);
    }

    /**
     *
     * Save the Image file
     *
     */
    $image_name = $visio_name . ".png";
    try {
      // Just in case... (since we use $id in our move call)
      $id = basename($id);
      (int)$id;
      Input::file('Image')->move(public_path("/bom_images/$id"), $image_name);
      Log::info("Image uploaded.");
    }
    catch(Exception $e){
      Log::warning("Unable to move image into storage. Check permissions on upload folder. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add image. Please contact the site admin.')
      ],
      400);
    }

    /**
     *
     * We only save the URL to the visio as the image file is optional.
     * In the "URLs" macro we then determine if the image for the visio exists.
     *
     */
    $visio_url = "/bom_images/$id/$visio_name";
    $url = new Url( array('url' => $visio_url ) );
    $bom = Bom::find($id);

    /**
     *
     * Attach the visio to the BOM
     *
     */
    try{
      $url->save();
      $bom->urls()->attach( $url->url_id );
      $bom->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a URL to bom $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add URL. Is this a duplicate?'),
        'data' => Input::all()],
        400);
    }

    /**
     *
     *  Return success. Javascript will close the modal and refresh the page.
     *
     */
    $response = array(
      'success' => true,
      'msg' => 'New URL added to the BOM.',
    );
    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom/$id");
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
      Input::file('Image')->move(public_path("/bom_images/$id"), $file_name);
      Log::info("Image uploaded.");
    }
    catch(Exception $e){
      Log::warning("Unable to move image into storage. Check permissions on upload folder. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add image. Please contact the site admin.')
      ],
      400);

    }

    $file_url = "/bom_images/$id/$file_name";
    $url = new Url( array('url' => $file_url ) );
    $bom = Bom::find($id);

    try {
      $url->save();
      $bom->urls()->attach( $url->url_id );
      $bom->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add an image to the bom $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to add link to image. Is this a duplicate?')
      ],
      400);
    }

    // Return success. Javascript will close the modal and refresh the page.
    $response = array(
      'success' => true,
      'msg' => 'New URL added to the BOM.',
    );

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom/$id");
  }


  /**
   * Add a URL to the BOM
   *
   * @param  int  $id
   * @return Response
   */
  public function addCablerun($id)
  {
    $rules = array(
      'CablerunId'   => 'required|integer',
    );

    $validator = Validator::make(Input::all(), $rules);
    if( $validator->fails() )
    {
      return Response::json(['success' => false, 
        'errors' => $validator->getMessageBag()->toArray()], 
        400);
    }

    $bom = Bom::find($id);

    // Try adding a URL to the BOM
    try{
      $bom->cableruns()->attach( Input::get('CablerunId') );
      $bom->save();
    }
    catch(Exception $e){
      Log::warning("An attempt to add a Cablerun to bom $id failed. $e");
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

    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom/$id");
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
      'msg' => 'Removed item from BOM.',
      'data' => array('bom' => $id, 'item' => Input::All())
    );

    if( $type == 'Part' )
    {
      try{
        $bom = Bom::find($id);
        $bom->parts()->detach($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from bom $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove part.'),
          'data' => Input::all()],
          400);
      }
      if(Request::ajax()) return Response::json( $response );
      return Redirect::to("/bom/$id");
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
        $bom = Bom::find($id);
        $bom->urls()->detach($item_id);
        Url::destroy($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from bom $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove part.'),
          'data' => Input::all()],
          400);
      }
      if(Request::ajax()) return Response::json( $response );
      return Redirect::to("/bom/$id");
    }

    if( $type == 'Cablerun' )
    {
      try{
        $bom = Bom::find($id);
        $bom->cableruns()->detach($item_id);
      }
      catch(Exception $e){
        Log::warning("An attempt to remove an item from bom $id failed. $e");
        return Response::json(['success' => false,
          'errors' => array('Unable to remove part.'),
          'data' => Input::all()],
          400);
      }
      if(Request::ajax()) return Response::json( $response );
      return Redirect::to("/bom/$id");
    }

    return Response::json(['success' => false,
      'errors' => array('No action was performed.'),
      'data' => Input::all()],
      400);
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
      Bom::destroy($id);
    }
    catch(Exception $e){
      Log::warning("An attempt to delete BOM $id failed. $e");
      return Response::json(['success' => false,
        'errors' => array('Unable to delete BOM. Make sure it is empty before deleting it.'),
        'data' => Input::all()],
        400);
    }

    $response = array(
      'success' => true,
      'msg' => 'Deleted BOM.',
      'data' => array('BOM' => $id, 'input' => Input::All())
    );
    if(Request::ajax()) return Response::json( $response );
    return Redirect::to("/bom");
  }


}

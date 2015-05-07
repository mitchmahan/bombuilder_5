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

use App\Models\User;

class UserController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    $page = array('name' => 'Users');
    $data = User::all();
    return View::make('users')->with( array('page' => $page, 'data' => $data) );
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $user = User::find($id);
    return View::make('user')->with('user', $user);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store()
  {
    $rules = array(
      'email'     => 'required|email|unique:users,email',
    );

    $validation = Validator::make(Input::all(), $rules);
    if( $validation->fails() ){
      return Response::json(['success' => false, 'errors' => $validation->getMessageBag()->toArray()], 400);
    }
      // Get the currently logged in user
      $user = Auth::User();
//      if( $user->hasAccess('user.create') )
      if( $user )
      {
        // Create the new user
        $new_user = User::create([
          'email'     => strtolower(Input::get('email')),
          'activated' => true,
          'permissions' => array(
            'user.create' => -1,
            'user.delete' => -1,
            'user.view'   => 1,
            'user.update' => -1,
          ),
        ]);
      }
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function postUser($id)
  {
    $rules = array(
      'value'     => 'max:254',
    );

    $validation = Validator::make(Input::all(), $rules);
    if( $validation->fails() ){
      return Response::json(['success' => false, 'errors' => $validation->getMessageBag()->toArray()], 400);
    }
    
      // Get the currently logged in user
      $user = Auth::User();
      //if($user->hasAccess('user.update') || $user->id == $id )
      if($user){
        // Find the user using the user id
        $updated_user = User::find($id);
        $updated_user->{Input::get('name')} = Input::get('value');
        $updated_user->save();
        return Response::json(['success' => true]);
      }
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function delete()
  {
      // Get the currently logged in user
      $user = Auth::user();
//      if($user->hasAccess('user.delete') )
      if( $user ){
        $delete_user = User::find(Input::get('id'));
        $delete_user->delete();
      }else{
        return Response::json(['success' => false, 'errors' => 'You do not have permission.'], 400);
      }
      return Response::json(['success' => true]);
  }
}

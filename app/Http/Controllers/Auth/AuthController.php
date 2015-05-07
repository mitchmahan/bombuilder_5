<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;

use Redirect;
use Response;

class AuthController extends Controller {
  /*
  |--------------------------------------------------------------------------
  | Registration & Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users, as well as the
  | authentication of existing users. By default, this controller uses
  | a simple trait to add these behaviors. Why don't you explore it?
  |
   */

  use AuthenticatesAndRegistersUsers;

  /**
   * Create a new authentication controller instance.
   *
   * @param  \Illuminate\Contracts\Auth\Guard  $auth
   * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
   * @return void
   */
  public function __construct(Guard $auth, Registrar $registrar)
  {
    $this->auth = $auth;
    $this->registrar = $registrar;
  }

  /**
   * Handle a login request to the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function postLogin(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email', 'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

    if ($this->auth->attempt($credentials, $request->has('remember')))
    {
      $response = array(
        'success' => true,
      );
      if(\Illuminate\Support\Facades\Request::ajax()) return Response::json( $response );
      //return redirect()->intended($this->redirectPath());
    }

    $response = array(
      'success' => false,
      'errors'     => [$this->getFailedLoginMessage()]
    );
    if (\Illuminate\Support\Facades\Request::ajax()) return Response::json( $response , 400);
    return redirect($this->loginPath())
      ->withInput($request->only('email', 'remember'))
      ->withErrors([
        'email' => $this->getFailedLoginMessage(),
        ]);

  }

}

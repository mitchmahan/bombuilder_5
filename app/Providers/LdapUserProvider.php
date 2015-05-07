<?php namespace App\Providers; 

use Illuminate\Contracts\Auth\User as UserContract;
use Illuminate\Contracts\Auth\UserProvider;

use App\Models\User;

class LdapUserProvider implements UserProvider{

  protected $ldap;
  protected $domain;

  /**
   * Build and store our connection to LDAP Service
   */
  public function __construct()
  {
    $this->ldap = ldap_connect('10.1.9.41');
    $this->domain = "DC=corp,Dc=global,DC=level3,DC=com";
  }

  /**
   * @param  string $identifier - ldap username
   * @return User
   */
  public function retrieveByID($identifier)
  {
    return new LdapUser(array('username' => $identifier));
  }

  public function isDeferred()
  {
    return false;
  }

  /**
   * @param email $email
   * @return LDAP DN for $email
   */
  public function retrieveDNByEmail($email)
  {
    $filter = "(mail=$email)";
    $search = ldap_search($this->ldap,"DC=corp,Dc=global,DC=level3,DC=com",$filter);
    $search_result = ldap_get_entries($this->ldap, $search);
    if( array_key_exists(0, $search_result) ){
      return $search_result[0]["dn"];
    }else{
      return false;
    }
  }

  /**
   * [connectLdap description]
   * @param  array  $credentials - passes in username / password
   * @return boolean
   */
  public function connectLdap(array $credentials)
  {
    $userDN = $this->retrieveDNByEmail($credentials['email']);

    /**
     * If $userDn was not found or password is not @ least 3 chars ...
     * Prevents ldap_connect with null password (anonymous connection)
     */
    if( ! $userDN || strlen($credentials['password']) < 3){
      return false;
    }

    // ldap_bind fails hard with invalid credentials so let's silence it with @
    try { 
      $ldap_bind = @ldap_bind($this->ldap, $userDN, $credentials['password']);

      if (!$ldap_bind) {
        return false;
      }

      return true;

    } catch (Exception $e) {

      // otherwise invalid
      return false;
    }
  }

  /**
   * @param  array  $credentials - passes in username / password
   * @return mixed
   */
  public function retrieveByCredentials(array $credentials)
  {
    /**
     *  Verify the supplied e-mail is in our database
     *
     *  This prevents any valid LDAP user from logging in
     */
    $query = User::where('email', '=', $credentials['email'])->get();
    if( ! $query->isEmpty() ){
      /**
       * Test the users LDAP credentials by doing an LDAP bind
       */
      if($this->connectLdap($credentials)){
        return $user = new LdapUser($credentials);
      }
    }
  }

  /**
   * [validateCredentials description]
   * @param  UserInterface $user        [description]
   * @param  array         $credentials [description]
   * @return boolean
   */
  public function validateCredentials(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials)
  {
    return $this->connectLdap($credentials);
  }

  public function retrieveByToken($identifier, $token){}
    public function updateRememberToken(\Illuminate\Contracts\Auth\Authenticatable $user, $token){}
}

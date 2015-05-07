<?php namespace App\Providers;
use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;
use Auth;

class LdapServiceProvider extends ServiceProvider {
  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;
  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    Auth::extend('ldap', function($app) {
      $provider = new LdapUserProvider();
      return new Guard($provider, $app['session.store']);
    });
  }
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
  }
  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('auth');
  }
}

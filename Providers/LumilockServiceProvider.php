<?php

namespace lumilock\lumilock\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class LumilockServiceProvider extends ServiceProvider
{

   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot()
   {
      $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
   }

   /**
    * Register the application services.
    *
    * @return void
    */
   public function register()
   {

      // Register the service the package provides.
      // $this->app->singleton('lumilock', function ($app) {
      //    return new lumilock;
      // });

      $configPath = __DIR__ . '/../config/auth.php';
      $this->mergeConfigFrom($configPath, 'auth');
      //Register Our Package routes
      include __DIR__ . '/../Routes/web.php';

      $baseConfigPath = base_path() . '/config';
      if (!file_exists($baseConfigPath)) {
         mkdir($baseConfigPath, 0777, true);
      }
      if (!file_exists($baseConfigPath . '/auth.php')) {
         // Will copy package/auth.php to project/auth.php
         // overwritting it if necessary
         copy($configPath, $baseConfigPath . '/auth.php');
      }

      // Loading custom factories
      $this->registerEloquentFactoriesFrom(__DIR__.'/../database/factories');

   }

   /**
    * Register factories.
    *
    * @param  string  $path
    * @return void
    */
   protected function registerEloquentFactoriesFrom($path)
   {
      $this->app->make(EloquentFactory::class)->load($path);
   }

   /**
    * Get the services provided by the provider.
    *
    * @return array
    */
   // public function provides()
   // {
   //    return ['lumilock'];
   // }
}

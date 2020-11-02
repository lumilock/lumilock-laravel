# Lumilock

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Dev Version on Packagist][ico-version-dev]][link-packagist]

## 📚 Installation
Create a .env file, copy all contents in .env.example into the .env file and add your database configurations.

In boostrap/app.php uncomment the facades and eloquent method.

```php
//before

// $app->withFacades();

// $app->withEloquent();

//after

$app->withFacades();

$app->withEloquent();
```

Migrate your database.

```shell
php artisan migrate
```
This package use the package `tymon/jwt-auth` so you need to generate an API secret key.

```shell
Generate your API secret
```
> ⚠️ If you got the error : `There are no commands defined in the "jwt" namespace.` you will need to add manually the secret key. So add JWT_SECRET=<Str::random(64). If you have python you can get secret in terminal like that :   
> ```python
> python
> >>> import random 
> >>> import string  
> >>> ''.join(random.choices(string.ascii_uppercase + string.digits, k=64))
> ```
> _

Make some changes to `bootstrap/app.php`.
```php
//before
// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

//After
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);
```

```php
//before
 // $app->register(App\Providers\AppServiceProvider::class);
 // $app->register(App\Providers\AuthServiceProvider::class);
 // $app->register(App\Providers\EventServiceProvider::class);

//After
 // $app->register(App\Providers\AppServiceProvider::class);
 $app->register(App\Providers\AuthServiceProvider::class);
 // $app->register(App\Providers\EventServiceProvider::class);

 // Add these lines
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(lumilock\lumilock\Providers\LumilockServiceProvider::class);
```

Add CORS Middleware to `bootstrap/app.php`.
```php
  $app->middleware([
      \lumilock\lumilock\App\Http\Middleware\CorsMiddleware::class
  ]);
```

## ⛕ Routes
- Post `/api/register` : Create a new user.
  - `Auth` : False
  - `@params` [name/email/password/password_confirmation]
- Post `/api/login` : Generate a new jwt.
  - `Auth` : False
  - `@params` [email/password]
- Post `/api/logout` : Blackliste the current jwt.
  - `Auth` : True
- Get `/api/profile` : Get all data from the current user.
  - `Auth` : True
- Get `/api/users/` : Get all data from all users.
  - `Auth` : True
- Get `/api/users/{id}` : Get all data from a specific user.
  - `Auth` : True

## 🧪 Tests
In order to test the package use this command line at the root of your lumen project : 
```shell
vendor/bin/phpunit <path/to/lumilock/test/dir>

# or

vendor/bin/phpunit .\vendor\lumilock\lumilock\tests\
```
## 🏗️ Create a package
https://blog.cloudoki.com/creating-a-lumen-package/

## 📰 Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## 👨‍👩‍👧‍👦 Credits

- [lumilock (Thibaud PERRIN)][link-author]


## 📝 License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/perrinthibaud/laravlock.svg
[ico-version-dev]: https://img.shields.io/packagist/vpre/perrinthibaud/laravlock.svg

[link-packagist]: https://packagist.org/packages/perrinthibaud/laravlock
[link-author]: https://github.com/lumilock
[link-contributors]: ../../contributors]
Laravel Themify
=======

Themify is a Laravel package that provides basic theme functionality in a non-obtrusive way. The purpose of Themify is to allow the developer to group views inside themes, having each theme its own folder. If you have experience with Yii framework theming, you will find this package usage very familiar.

A sample structure folder could be like this:

```
app/
├── Http
├── ...
├── themes
│   ├── admin
│   │   ├── category
│   │   ├── dashboard
│   │   ├── ...
│   └── default
│       ├── index.blade.php
│       ├── layouts
│       ├── post
│       └── ...
```

Themify expects you to store your themes in a given folder, which is `app/themes` by default. Then, each theme should have its own views inside its folder, just like it was a `views` folder.

Installation
-------
 - Use composer to install the package:

```
composer require nwidart/themify=*
```

 - Add the ServiceProvider to your service provider list inside `app/config/app.php`:

```php
'providers' => array(
    ...
    'Illuminate\View\ViewServiceProvider',
    'Illuminate\Workbench\WorkbenchServiceProvider',

    'Nwidart\Themify\ThemifyServiceProvider',
),
```

 - Add the Facade to your aliases array inside `app/config/app.php`:

```php
'aliases' => array(
    ...
    'URL'             => 'Illuminate\Support\Facades\URL',
    'Validator'       => 'Illuminate\Support\Facades\Validator',
    'View'            => 'Illuminate\Support\Facades\View',

    'Themify'         => 'Nwidart\Themify\Facades\Themify',
),
```

 - Create your `themes` directory inside your application. By default, **Themify** expects an `app/themes` directory, but this can be modified in the package configuration.

 - Publish package configuration with artisan: 
 
 ```
 php artisan vendor:publish
 ``` 
 
 Then, modify settings as needed by editing `config/themify.php`.

Usage
-------

First, you have to tell the package which theme you want to use. You have three different ways to do this, ordered by priority:

1. Calling `Themify::set($theme)`. Being `theme` the name of the folder of the theme you want to use.
2. Defining a `public $theme` property in your controller.
3. Using `Themify::defaults($theme)`, which is a shortcut to changing the `themify::default_theme` property in package settings.

Once you have defined your theme, you can render your views using the `View` class in the traditional way. **Themify** will try to find the specified view inside the defined theme folder. If it doesn't find it, it will fallback to the default `views` folder (or whatever you have defined in your `app/config/view.php`).

`View::render('foo', compact($bar));`
 
### Priorities

Each of the mentioned methods has an internal priority assigned:

- If the theme is explicitly set using `Themify::set($theme)`, the only way to override it is to use `set()` again.
- If no calls to `set()` are found, **Themify** will check for a `$theme` property in the current controller (if any). Note that this property should be `public`. This check is made through a simple `before` filter that the ServiceProvider of the package adds to all routes.

```php
<?php

class MyAwesomeController extends BaseController {
    
    public $theme = 'bootstrap';

    public function index()
    {
        return View::make('index');
    }

}
```

- If no `$theme` property is found on the controller, or there is no controller for the current route, **Themify** will get the value inside it's configuration file. This value can be set either at runtime using `Themify::defaults($theme)`, or modifying the `themify::default_theme` property inside the `config.php` that you published with artisan.

### Theme assets

**Themify** expects you to have a folder inside your `public` directory (or the one that you have defined in your Laravel configuration) to store theme assets. By default, this folder is `public/assets/themes`, but it can be modified in the package configuration file.

Thus, this assets folder should contain one folder per theme. For example, if you are using a `bootstrap` theme, you should create `public/assets/themes/bootstrap`, and then create your stylesheets, javascripts and other assets there.

### Helpers

**Themify** provides two convenient helpers for your views: `theme_url()` and `theme_secure_url()`, which will return the path to your current theme assets folder.

```html
<link rel="stylesheet" type="text/css" href="{{ theme_url() }}/css/styles.css">
<script src="{{ theme_url() }}/js/main.min.js"></script>
```

## Examples

### Setting a theme for a group of routes

```php
<?php

Route::filter('admin.theme', function()
{
    // We use default() so we can 
    // override later if we want
    Themify::default('admin')
});

Route::group(['prefix' => 'admin', 'before' => 'admin.theme'], function()
{
    // All of these routes will use
    // 'admin' theme

    // Override this route with a
    // different theme
    Route::get('login', function() {
        Themify::set('basic');
    });
});
```

### Setting a theme for all controller actions

You can define your theme in a per controller basis, using a `public $theme` property inside your controllers:

```php
<?php

class FooController extends BaseController {

    public $theme = 'footheme';

    public function someAction()
    {
        // For this one, use a different theme
        Themify::set('bartheme');
    }

}
```

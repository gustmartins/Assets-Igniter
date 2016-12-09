# Assets-Igniter
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)]()

Assets-Igniter is the best approach for you to handle external **CSS** and **JS** files. Assets-Igniter is lightweight, simple, and clean, and you can customize it at your will!
It comes with a helper file to assist you and simplify the process of loading external files! For more information about how to use and customize the library, keep on reading this instructions.

## Features

+ Fully customizable!
+ Minify **CSS** and **JS** files (if you want to).
+ Merge multiple files into one group to load less files in browser.
+ Set versions to each file or to each file type.
+ Keep track of errors with a debug function.
+ Translate the error messages to the language you want.
+ Comes with an helper to make much easier to use the library!

## Requirements

- PHP version 5.6 or newer is recommended.
- CodeIgniter version 2.2.1+

## Instalation

To use the Assets-Igniter Library you must fist copy the file located in `application/config/assets.php` to your own `application/config/` folder. Then, edit this file according to your configurations. Four variables you **MUST** set to work fine with the Assets-Igniter:

1. *css_path*: The path to your CSS files.
2. *js_path*: The path to your JS files.
3. *min_css_path*: The path to your minified CSS files.
4. *min_js_path*: The path to your minified JS files.

It is important that all this folders have a `0664` permission because the Assets-Igniter will sometimes try to write some files in them.

After the configuration of the Assets-Igniter, copy the files `application/libraries/Assets.php`, `application/language/english/assets_lang.php` and if you want to use our helper, `application/helpers/assets_helper.php` to your own `application/libraries/`, `application/language/english/` and `application/helpers/` folders.

That's all! Have fun!

## Library Usage

> Below are some examples of what the Assets-Igniter does!

### Loading Library

Your can load the Assets-Igniter as you load any other library:

```php

$this->load->library('assets');

```

### Setting custom configurations

If you want to load different configurations you can pass a variable while loading the library with the configurations you want to customize, like this:

```php

$config = array(
	'css_path' => 'custom/path/to/css/',
	'js_path' => 'custom/path/to/js/'
);
$this->load->library('assets', $config);

```

But if you need to run the library and after that to change the configurations, you can do that using the initialize function:

```php

$this->load->library('assets', $config);

$new_config = array(
	'css_path' => 'another/path/to/css/',
	'js_path' => 'another/path/to/js/'
);
$this->assets->initialize($new_config);

```

### Clearing loaded data

To clear whatever file you have already loaded, and reset the configurations to those in the `application/config/assets.php` file, you can use the clear function:

```php

$this->assets->clear();

```

For debugging reasons error messages are kept on clearing. If you want to clear the errors that was saved, set the first parameter of the clear function to `TRUE` and the errors will also be cleared.

**Remember**: If you previously loaded any custom configurations *they will also* be cleared and the library will be restored to the configurations saved on `application/config/assets.php`.

### Loading files

Loading files is easy! You can use the following code as many times you need:

```php

$this->assets->load('filename.css', 'groupname', 123);
$this->assets->load('path/to/filename.css', 'groupname', 456);

```

Note that three parameters are given to the function. The only *required* parameter is the first one! The first parameter is the name of the file. The file given must be inside the path described in `application/config/assets.php` for its type of file.

The second parameter is a name for the group, if you set **auto_merge**, or any **merge type** to `TRUE` (in the configurations), all files in the group will be combined into one file with the group name. In the example above, both files would be merged into one file named `groupname.css` or `groupname.min.css`, if you also set **minify type** to `TRUE`.

The third parameter is optional! If you set _a number_ on it this number will be used as the version of the file loaded. If not set, the **type version** in `application/config/assets.php` will be used.

**Remember**: You can **ONLY** load one file on each call to load function. If you want to call multiple files at once, consider to use the `load_assets` function in our helper.

### Generating HTML tags

You can create as many groups you want loading the files with the load function. To generate the output HTML tags for the files, you can assign a variable to send to your view like this:

```php

$links = $this->assets->generate('groupname');

```

Now, all you have to do is to send the variable **$links** to your view and echo it! That's all!

**Remember**: The first and only parameter of this function **MUST** be an loaded group. It can only be empty if you load a file with no group!

### Error Messages

By default Assets-Igniter Library keeps an record of errors that may occur during the process. You can assign this errors to a variable to show in somewhere or to save a log (whatever).

```php

$errors = $this->assets->print_debugger();

```

## Helper Usage

> You can use our helper instead of the library itself!

### Configuring the Library

The `config_assets()` function accepts an array of data to configure the Assets-Igniter Library. This is useful if you want to change the configurations set in `application/config/assets.php`.

```php

<?php
   config_assets(array(
      'css_path' => 'path/to/css/',
      'js_path' => 'path/to/js/'
   ));
?>

```

### Loading Files

You can load files direct on your views using the function `load_assets()`. You can pass a `string` in the first parameter with the the file path or your can pass an `array` with multiple paths. The `array` passed can have a prototype of `filename => version`.

The second parameter is the name of the group to use. Additionally, you can pass an `array` of configurations in the third parameter.

```php

<?php
   echo load_assets('filename.js', 'scripts');

   echo load_assets(array(
      'main.css',
      'filename.css' => 123
   ), 'styles');

   echo load_assets('last.js', 'lasts', array(
      'js_path' => 'new/path/to/js/'
   ));
?>

```

### Clearing Assets

To clear the files and configurations in the Assets-Igniter Library use the function `clear_assets()`. The `clear_assets()` function accepts only one parameter telling it to clear the error messages. If you want to clear the errors for debug porpuses, set the parameter to `TRUE`.

```php

<?php
   clear_assets(TRUE);
?>

```

### Debug Errors

To show the debug error messages echo the function `debug_assets()`. It doesn't need any parameter.

```php

<?php echo debug_assets(); ?>

```

## Contributions

This package was created by [Gustavo Martins][GustMartins], but your help is welcome! Things you are welcome to do:

+ Report any bug you may encounter
+ Suggest a feature for the project

For more information about contributing to the project please, read the [Contributing Requirements][contrib].

## Change Log

Currently, the Assets-Igniter Library is in the version **1.0.1**. See the full [Changelog][changelog] for more details.

[GustMartins]: https://github.com/GustMartins
[contrib]: https://github.com/GustMartins/Assets-Igniter/blob/master/contributing.md
[changelog]: https://github.com/GustMartins/Assets-Igniter/blob/master/changelog.md
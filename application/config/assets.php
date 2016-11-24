<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package		Assets
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Assets-Igniter
 * @since		Version 1.0.0
 */

/*
|--------------------------------------------------------------------------
| CSS Folder Path
|--------------------------------------------------------------------------
|
| Set the path where you put your .css files.
|
*/
$config['css_path'] = '';

/*
|--------------------------------------------------------------------------
| JS Folder Path
|--------------------------------------------------------------------------
|
| Set the path where you put your .js files.
|
*/
$config['js_path'] = '';

/*
|--------------------------------------------------------------------------
| Minified CSS Folder Path
|--------------------------------------------------------------------------
|
| Set the path where you want Assets Library to write the minified .css
| files it generates.
|
*/
$config['min_css_path'] = '';

/*
|--------------------------------------------------------------------------
| Minified JS Folder Path
|--------------------------------------------------------------------------
|
| Set the path where you want Assets Library to write the minified .js files
| it generates.
|
*/
$config['min_js_path'] = '';

/*
|--------------------------------------------------------------------------
| Automatic Merge Files
|--------------------------------------------------------------------------
|
| If TRUE the Assets Library will merge all files in a group into one
| single file.
|
*/
$config['auto_merge'] = TRUE;

/*
|--------------------------------------------------------------------------
| Merge File Type
|--------------------------------------------------------------------------
|
| Even if you set 'auto_merge' to TRUE you can choose what file types will
| be merged with this variables.
|
| WARNING: If you set 'auto_merge' to TRUE and you want all types to be
| merged, set this two variables to TRUE also.
|
*/
$config['merge_css'] = TRUE;
$config['merge_js'] = TRUE;

/*
|--------------------------------------------------------------------------
| File Type Version
|--------------------------------------------------------------------------
|
| The default file type version.
|
*/
$config['css_version'] = 1;
$config['js_version'] = 1;

/*
|--------------------------------------------------------------------------
| Minify File Types
|--------------------------------------------------------------------------
|
| Do you want the Assets Library to minify your files?
|
*/
$config['minify_css'] = TRUE;
$config['minify_js'] = TRUE;

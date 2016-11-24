<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Assets Helper for Assets Class
 *
 * This class helps the Assets Class to manage assets files like css and js files
 *
 * @package		Assets
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Assets-Igniter
 * @since		Version 1.0.0
 */


// ------------------------------------------------------------------------

if ( ! function_exists('config_assets'))
{
	/**
	 *  Load custom configurations for Assets
	 *
	 *  @see      Assets::initialize()
	 *  @param    array   $config
	 *  @return   void
	 */
	function config_assets(array $config)
	{
		$CI =& get_instance();

		if ( ! isset($CI->assets))
		{
			$CI->load->library('assets', $config);
		}
		else
		{
			$CI->assets->initialize($config);
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('load_assets'))
{
	/**
	 *  Load files and generate the HTML tags for them
	 *
	 *  @see      Assets::load()
	 *  @see      Assets::generate()
	 *  @param    array|string   $files
	 *  @param    string         $group
	 *  @param    array          $config    If you want you can load different configurations
	 *  @return   string
	 */
	function load_assets($files, $group = 'main', $config = array())
	{
		$CI =& get_instance();

		if ( ! isset($CI->assets))
		{
			$CI->load->library('assets', $config);
		}
		else
		{
			$CI->assets->initialize($config);
		}

		if (is_array($files))
		{
			foreach ($files as $file => $value)
			{
				if ( ! is_numeric($file))
				{
					$CI->assets->load($file, $group, $value);
				}
				else
				{
					$CI->assets->load($value, $group);
				}
			}
		}
		else
		{
			$CI->assets->load($files, $group);
		}

		return $CI->assets->generate($group);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('clear_assets'))
{
	/**
	 *  Clear the Assets Data
	 *
	 *  @see      Assets::clear()
	 *  @param    boolean   $clear_debugger   Clear error messages?
	 *  @return   void
	 */
	function clear_assets($clear_debugger = FALSE)
	{
		$CI =& get_instance();

		if ( ! isset($CI->assets))
		{
			$CI->load->library('assets');
		}

		$CI->assets->clear($clear_debugger);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('debug_assets'))
{
	/**
	 *  Get Debug Message
	 *
	 *  @see      Assets::print_debugger()
	 *  @return   string
	 */
	function debug_assets()
	{
		$CI =& get_instance();

		if ( ! isset($CI->assets))
		{
			$CI->load->library('assets');
		}

		return $CI->assets->print_debugger();
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Assets Class for Codeigniter
 *
 * This class enables the management of assets files like css and js files
 *
 * @package		Assets
 * @subpackage	Library
 * @category	Library
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Assets-Igniter
 * @version 	1.0.0
 */
class Assets {

	/**
	 * Path to your CSS files folder. It can NOT be empty, you MUST
	 * set this value in assets config file.
	 *
	 * @var string
	 */
	public $css_path		= '';

	/**
	 * Path to your minified CSS files folder. It can NOT be empty,
	 * you MUST set this value in assets config file.
	 *
	 * @var string
	 */
	public $min_css_path	= '';

	/**
	 * Path to your JS files folder. It can NOT be empty, you MUST
	 * set this value in assets config file.
	 *
	 * @var string
	 */
	public $js_path			= '';

	/**
	 * Path to your minified JS files folder. It can NOT be empty,
	 * you MUST set this value in assets config file.
	 *
	 * @var string
	 */
	public $min_js_path		= '';

	/**
	 * Automatically merge all files into one single file?
	 *
	 * @var bool
	 */
	public $auto_merge	= FALSE;

	/**
	 * Automatically merge all CSS files?
	 *
	 * @var bool
	 */
	public $merge_css	= TRUE;

	/**
	 * Automatically merge all JS files?
	 *
	 * @var bool
	 */
	public $merge_js	= FALSE;

	/**
	 * The version of the CSS files
	 *
	 * @var int
	 */
	public $css_version	= 1;

	/**
	 * The version of the JS files
	 *
	 * @var int
	 */
	public $js_version	= 1;

	/**
	 * Minify all CSS files
	 *
	 * @var bool
	 */
	public $minify_css	= TRUE;

	/**
	 * Minify all JS files
	 *
	 * @var bool
	 */
	public $minify_js	= FALSE;
	
	// --------------------------------------------------------------------------

	/**
	 * Reference to CodeIgniter instance
	 *
	 * @var object
	 */
	protected $CI;

	/**
	 * Files loaded to assets class
	 *
	 * @var array
	 */
	protected $_files			= array();

	/**
	 * Modified Files Content
	 *
	 * @var array
	 */
	protected $_modified_files	= array();

	/**
	 * Files to show
	 *
	 * @var array
	 */
	protected $_output_files	= array();

	/**
	 * Array of custom error messages
	 *
	 * @var array
	 */
	protected $_error_messages	= array();
	
	// --------------------------------------------------------------------------

	/**
	 * Allowed File Extensions to use
	 *
	 * @var array
	 */
	private $_allowed_ext		= array('css', 'js');

	/**
	 * Allowed File Extensions to use
	 *
	 * @var array
	 */
	private $_allowed_regex		= '/(css|js)$/i';
	
	// --------------------------------------------------------------------------

	/**
	 *  Assets Class Constructor
	 *
	 *  @param   array   $params = array()
	 *  @return	 void
	 */
	public function __construct(array $params = array())
	{
		// Set the super object to a local variable for use later
		$this->CI =& get_instance();
		$this->initialize($params);
		$this->CI->load->helper(array('file', 'url'));

		log_message('info', 'Assets Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @param	array	$params
	 * @return	Assets
	 */
	public function initialize(array $params = array())
	{
		$this->clear();

		foreach ($params as $key => $val)
		{
			if (isset($this->$key))
			{
				$method = 'set_'.$key;

				if (method_exists($this, $method))
				{
					$this->$method($val);
				}
				else
				{
					$this->$key = $val;
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the Assets Data
	 *
	 * @param	bool
	 * @return	Assets
	 */
	public function clear($clear_debugger = FALSE)
	{
		$this->CI->config->load('assets');

		$this->css_path		= config_item('css_path');
		$this->min_css_path	= config_item('min_css_path');
		$this->js_path		= config_item('js_path');
		$this->min_js_path	= config_item('min_js_path');
		$this->auto_merge	= config_item('auto_merge');
		$this->merge_css	= config_item('merge_css');
		$this->merge_js		= config_item('merge_js');
		$this->css_version	= config_item('css_version');
		$this->js_version	= config_item('js_version');
		$this->minify_css	= config_item('minify_css');
		$this->minify_js	= config_item('minify_js');
		$this->_files			= array();
		$this->_modified_files	= array();
		$this->_output_files	= array();

		if ($clear_debugger !== FALSE)
		{
			$this->_error_messages = array();
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Load a file to a given group
	 *
	 *  @param    string   $filename   The name of the file
	 *  @param    string   $group
	 *  @param    mixed    $version    The version you want for this file
	 *  @return   Assets
	 */
	public function load($filename, $group = 'main', $version = NULL)
	{
		$ext = substr(strrchr($filename, "."), 1);

		if ( ! in_array($ext, $this->_allowed_ext))
		{
			$this->_set_error_message('lang:invalid_extension', $filename);
		}
		else
		{
			if ( ! is_file($this->{$ext.'_path'}.$filename) OR ! file_get_contents($this->{$ext.'_path'}.$filename))
			{
				$this->_set_error_message('lang:empty_content', $filename);
			}
			else
			{
				$this->_files[$group][] = array(
					'ext' => $ext,
					'file' => $filename,
					'version' => $this->_set_file_version($version, $ext)
				);

				if ($this->auto_merge !== TRUE)
				{
					if ($this->{'merge_'.$ext} !== FALSE)
					{
						$this->_merge_files($group, $ext, $filename);
					}
					else
					{
						if ($this->{'minify_'.$ext} !== FALSE)
						{
							$min_filename = str_replace('.'.$ext, '.min.'.$ext, $filename);

							if ( ! is_file($this->{'min_'.$ext.'_path'}.$min_filename) OR ! file_get_contents($this->{'min_'.$ext.'_path'}.$min_filename))
							{
								if ($file = $this->_write_file($filename, $ext, $this->{'_minify_'.$ext.'_file'}(file_get_contents($this->{$ext.'_path'}.$filename))))
								{
									$this->_output_files[$group][] = array(
										'file' => $file,
										'ext' => $ext,
										'version' => $this->_set_file_version($version, $ext)
									);
								}
							}
							else
							{
								$this->_output_files[$group][] = array(
									'file' => $min_filename,
									'ext' => $ext,
									'version' => $this->_set_file_version($version, $ext)
								);
							}
						}
						else
						{
							$this->_output_files[$group][] = array(
								'file' => $filename,
								'ext' => $ext,
								'version' => $this->_set_file_version($version, $ext)
							);
						}
					}
				}
				else
				{
					$this->_merge_group($group, TRUE);
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 *  Generates the output content for the Assets
	 *
	 *  @param    string   $group
	 *  @return   string
	 */
	public function generate($group = 'main')
	{
		$assets = '';

		if (isset($this->_output_files[$group]) && is_array($this->_output_files[$group]))
		{
			foreach ($this->_output_files[$group] as $file)
			{
				if (is_file($this->{'min_'.$file['ext'].'_path'}.$file['file']))
				{
					$assets .= $this->_set_file_tag($file['ext'], $file['file'], $file['version'], TRUE);
				}
				elseif (is_file($this->{$file['ext'].'_path'}.$file['file']))
				{
					$assets .= $this->_set_file_tag($file['ext'], $file['file'], $file['version']);
				}
				else
				{
					$this->_set_error_message('lang:file_not_found', $file['file']);
				}
			}
		}

		if (isset($this->_modified_files[$group]) && is_array($this->_modified_files[$group]))
		{
			foreach ($this->_modified_files[$group] as $ext => $content)
			{
				$file = $this->_write_file($group.'.'.$ext, $ext, $content);

				if ($file)
				{
					if ($this->{'minify_'.$ext} !== FALSE)
					{
						$assets .= $this->_set_file_tag($ext, $file, $this->{$ext.'_version'}, TRUE);
					}
					else
					{
						$assets .= $this->_set_file_tag($ext, $file, $this->{$ext.'_version'});
					}
				}
			}
		}

		return $assets;
	}

	// --------------------------------------------------------------------

	/**
	 *  Get Debug Message
	 *
	 *  @return   string
	 */
	public function print_debugger()
	{
		$msg = '';

		if (count($this->_error_messages) > 0)
		{
			foreach ($this->_error_messages as $val)
			{
				$msg .= $val;
			}
		}

		return $msg;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the version for CSS files
	 *
	 *  @param   integer   $int
	 *  @return  Assets
	 */
	protected function set_css_version($int)
	{
		$this->css_version = is_numeric($int) ? (integer) $int : time();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the version for JS files
	 *
	 *  @param   integer   $int
	 *  @return  Assets
	 */
	protected function set_js_version($int)
	{
		$this->js_version = is_numeric($int) ? (integer) $int : time();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the path to CSS folder
	 *
	 *  @param   string   $path
	 *  @return  Assets
	 */
	protected function set_css_path($path)
	{
		if (realpath($path) !== FALSE)
		{
			$realpath = realpath($path);
		}
		elseif ( ! is_dir($realpath))
		{
			$this->_set_error_message('lang:invalid_css_path', $realpath);
		}

		$this->css_path = is_dir($realpath) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the path to JS folder
	 *
	 *  @param   string   $path
	 *  @return  Assets
	 */
	protected function set_js_path($path)
	{
		if (realpath($path) !== FALSE)
		{
			$realpath = realpath($path);
		}
		elseif ( ! is_dir($realpath))
		{
			$this->_set_error_message('lang:invalid_js_path', $realpath);
		}

		$this->js_path = is_dir($realpath) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the path to minified CSS folder
	 *
	 *  @param   string   $path
	 *  @return  Assets
	 */
	protected function set_min_css_path($path)
	{
		if (realpath($path) !== FALSE)
		{
			$realpath = realpath($path);
		}
		elseif ( ! is_dir($realpath))
		{
			$this->_set_error_message('lang:invalid_min_css_path', $realpath);
		}

		$this->min_css_path = is_dir($realpath) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Sets the path to minified JS folder
	 *
	 *  @param   string   $path
	 *  @return  Assets
	 */
	protected function set_min_js_path($path)
	{
		if (realpath($path) !== FALSE)
		{
			$realpath = realpath($path);
		}
		elseif ( ! is_dir($realpath))
		{
			$this->_set_error_message('lang:invalid_min_js_path', $realpath);
		}

		$this->min_js_path = is_dir($realpath) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Set file version, custom or default based on extension
	 *
	 *  @param   string   $version
	 *  @param   string   $ext
	 */
	protected function _set_file_version($version = '', $ext = '')
	{
		return ($version !== NULL) ? $version : $this->{$ext.'_version'};
	}

	// --------------------------------------------------------------------

	/**
	 *  Prepare a group to be merged into one single file
	 *
	 *  @param    string    $group
	 *  @param    boolean   $force   Force to merge the files
	 *  @return   Assets
	 */
	protected function _merge_group($group = 'main', $force = FALSE)
	{
		if ( ! empty($this->_files[$group]))
		{
			foreach ($this->_files[$group] as $name => $file)
			{
				$group_file = $this->{$file['ext'].'_path'}.$group.'.'.$file['ext'];

				if ($force !== FALSE OR ! is_file($group_file) OR ! file_get_contents($group_file))
				{
					$this->_merge_files($group, $file['ext'], $file['file']);
				}
			}
		}
		else
		{
			$this->_set_error_message('lang:empty_group', $group);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Merge files into one single file
	 *
	 *  @param    string   $group
	 *  @param    string   $ext
	 *  @param    string   $filename
	 *  @return   Assets
	 */
	protected function _merge_files($group, $ext, $filename)
	{
		if ( ! in_array($ext, $this->_allowed_ext))
		{
			$this->_set_error_message('lang:invalid_extension', $filename);
		}
		else
		{
			$content = (isset($this->_modified_files[$group][$ext])) ? $this->_modified_files[$group][$ext] : '';

			if ($this->auto_merge !== FALSE OR ($this->auto_merge === FALSE && $this->{'merge_'.$ext} !== FALSE))
			{
				$content .= file_get_contents($this->{$ext.'_path'}.$filename);

				if ($this->{'minify_'.$ext} !== FALSE)
				{
					$this->_modified_files[$group][$ext] = $this->{'_minify_'.$ext.'_file'}($content);
				}
				else
				{
					$this->_modified_files[$group][$ext] = $content;
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 *  Generates the HTML Tags for each file loaded
	 *
	 *  @param   string    $ext
	 *  @param   string    $filename
	 *  @param   integer   $version
	 *  @param   boolean   $minified   Is it a minified file?
	 */
	protected function _set_file_tag($ext, $filename, $version, $minified = FALSE)
	{
		if ($minified !== FALSE)
		{
			$file_url = $this->{'min_'.$ext.'_path'}.$filename.'?v='.$version;
		}
		else
		{
			$file_url = $this->{$ext.'_path'}.$filename.'?v='.$version;
		}

		switch ($ext)
		{
			case 'css':
				return '<link href="'.base_url($file_url).'" type="text/css" rel="stylesheet" />'."\n";
			
			case 'js':
				return '<script src="'.base_url($file_url).'" type="text/javascript" charset="utf-8" async defer></script>'."\n";
		}
	}

	// --------------------------------------------------------------------

	/**
	 *  Set Errors Message
	 *
	 *  @param   string   $msg
	 *  @param   string   $val
	 *  @return  void
	 */
	protected function _set_error_message($msg, $val = '')
	{
		$this->CI->lang->load('assets');

		if (sscanf($msg, 'lang:%s', $line) !== 1 OR FALSE === ($line = $this->CI->lang->line($line)))
		{
			$this->_error_messages[] = str_replace('%s', $val, $msg).'<br />';
		}
		else
		{
			$this->_error_messages[] = str_replace('%s', $val, $line).'<br />';
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Minifies CSS file
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	private function _minify_css_file($content)
	{
		//	Remove all comments from the content
		// $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
		//	Remove space after colons
		$content = str_replace(': ', ':', $content);
		//	Remove last semicolon before closing bracket
		$content = str_replace(';}', '}', $content);

		//	Remove new lines, tabs
		$content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
		// preserve empty comment after '>'
		// http://www.webdevout.net/css-hacks#in_css-selectors
		$content = preg_replace('@>/\\*\\s*\\*/@', '>/**/', $content);
		// preserve empty comment between property and value
		// http://css-discuss.incutio.com/?page=BoxModelHack
		$content = preg_replace('@/\\*\\s*\\*/\\s*:@', '/**/:', $content);
		$content = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/**/', $content);
		// remove ws around { } and last semicolon in declaration block
		$content = preg_replace('/\\s*{\\s*/', '{', $content);
		$content = preg_replace('/;?\\s*}\\s*/', '}', $content);
		// remove ws surrounding semicolons
		$content = preg_replace('/\\s*;\\s*/', ';', $content);
		// remove ws around urls
		$content = preg_replace('/
			url\\(		# url(
			\\s*
			([^\\)]+?)	# 1 = the URL (really just a bunch of non right parenthesis)
			\\s*
			\\)			# )
		/x', 'url($1)', $content);
		// remove ws between rules and colons
		$content = preg_replace('/
				\\s*
				([{;])				# 1 = beginning of block or rule separator 
				\\s*
				([\\*_]?[\\w\\-]+)	# 2 = property (and maybe IE filter)
				\\s*
				:
				\\s*
				(\\b|[#\'"])		# 3 = first character of a value
			/x', '$1$2:$3', $content);
		// minimize hex colors
		$content = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i', '$1#$2$3$4$5', $content);
		$content = preg_replace('/@import\\s+url/', '@import url', $content);
		// remove any ws involving newlines
		$content = preg_replace('/[ \\t]*\\n+\\s*/', "", $content);

		return $content;
	}

	// --------------------------------------------------------------------

	/**
	 *  Minifies JS file
	 *
	 *  @param    string   $content
	 *  @return   string
	 */
	private function _minify_js_file($content)
	{
		// Remove comments
		$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
		$content = preg_replace('!\/\/.*!','',$content);
		// Remove space after colons
		$content = str_replace(': ', ':', $content);
		// Remove new lines, tabs
		$content = str_replace(array("\r\n", "\r", "\n", "\t"), '', $content);

		return $content;
	}

	// --------------------------------------------------------------------

	/**
	 *  Writes a file
	 *
	 *  @param    string   $filename
	 *  @param    string   $ext
	 *  @param    string   $content
	 *  @return   mixed
	 */
	private function _write_file($filename, $ext, $content)
	{
		$affix = ($this->{'minify_'.$ext} !== FALSE) ? 'min.'.$ext : $ext;
		$preffix = ($this->{'minify_'.$ext} !== FALSE) ? 'min_' : '';

		// Verify if the file is inside any folder...
		preg_match('/^(.*[\\|\/])/', $filename, $file_dir);
		// ... and create the folder before writting the file
		if (isset($file_dir[0]) &&  ! is_dir($this->{$preffix.$ext.'_path'}.$file_dir[0]))
		{
			mkdir($this->{$preffix.$ext.'_path'}.$file_dir[0], 0644, TRUE);
		}

		if ( ! write_file($this->{$preffix.$ext.'_path'}.preg_replace($this->_allowed_regex, $affix, $filename), $content))
		{
			$this->_set_error_message('lang:write_file_failure', $this->{$preffix.$ext.'_path'}.preg_replace($this->_allowed_regex, $affix, $filename));
			return FALSE;
		}
		else
		{
			return preg_replace($this->_allowed_regex, $affix, $filename);
		}
	}
}

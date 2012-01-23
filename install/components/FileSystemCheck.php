<?php
/**
 * BackendPro
 *
 * An open source development control panel written in PHP
 *
 * @package		BackendPro
 * @author		Adam Price
 * @copyright	Copyright (c) 2008, Adam Price
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link		http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ------------------------------------------------------------------------

include_once("common/CommonFunctions.php");

/**
 * Log folder is writable
 *
 * Check the log folder is writable
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class LogFolderWritable extends Component
{
	var $name = "Log Folder writable";

	function install()
	{
		if( is_really_writable(LOGS))
		{
			$this->status = TRUE;
		}
		else
		{
			$this->error = LOGS . " folder isn't writable";
		}

		return $this->status;
	}
}

/**
 * Asset folders are writable
 *
 * Check all the asset folders are writable
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class AssetFoldersWritable extends Component
{
	var $name = "Asset folders writable";
	var $path_array = array(
			'assets/cache/');

	function install()
	{
		foreach($this->path_array as $path)
		{
			if ( ! is_really_writable(BASEPATH . $path))
			{
				$this->error = BASEPATH . $path . " folder isn't writable";
				return $this->status;
			}
		}
		$this->status = TRUE;
		return $this->status;
	}
}

/**
 * Cache folder is writable
 *
 * Check the CodeIgniter cache folder is writable
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class CacheFolderWritable extends Component
{
	var $name = "Cache Folder writable";

	function install()
	{
		if( is_really_writable(CACHE))
		{
			$this->status = TRUE;
		}
		else
		{
			$this->error = CACHE . " folder isn't writable";
		}
		return $this->status;
	}
}

/**
 * Config files are writable
 *
 * Check all config files we need to write to later
 * are writable
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class ConfigFilesWritable extends Component
{
	var $name = "Config files writable";
	var $file_array = array();

	function ConfigFilesWritable()
	{
		$this->file_array[] = APPLICATION . 'config/config.php';
		$this->file_array[] = APPLICATION . 'config/database.php';
		$this->file_array[] = MODULES . 'recaptcha/config/recaptcha.php';
	}

	function install()
	{
		foreach($this->file_array as $file)
		{
			if ( !is_really_writable($file))
			{
				$this->error = $file . " file isn't writable";
				return $this->status;
			}
		}
		$this->status = TRUE;
		return $this->status;
	}
}



/* End of file FileSystemCheck.php */
/* Location: ./install/components/FileSystemCheck.php */
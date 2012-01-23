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

/**
 * Perform File Replacement
 *
 * Replace the files in the basic CodeIgniter install with the
 * custom created files using the Users entered data
 *
 * @package 	BackendPro
 * @subpackage 	Install
 * @param 		string $fromFile Source filename
 * @param 		string $toFile Destination filename
 * @param 		array  $replacementArray Array of replacement strings
 * @return 		bool
 */
function PerformOverWrite($fromFile, $toFile, $replacementArray)
{
	global $logger;

	$file = 'files/' . $fromFile;
	$contents = file_get_contents($file);

	// Lets run our replacement
	foreach($replacementArray as $key => $value)
	{
		$contents = preg_replace('/{' . $key . '}/', $value, $contents);
	}

	// Lets copy the files to there correct locations
	if( !$fp = @fopen($toFile, 'wb'))
	{
		return "Could not open " . $toFile . " to write new content to";
	}

	flock($fp, LOCK_EX);
	fwrite($fp, $contents);
	flock($fp, LOCK_UN);
	fclose($fp);
	return TRUE;
}

/**
 * Overwrite System Config
 *
 * Overwrite the default system config file with the new
 * custom file created using the Users input
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class OverWriteSystemConfig extends Component
{
	var $name 		= "Create new system config file";
	var $copyFrom 	= "config.txt";
	var $copyTo 	= "config/config.php";

	function Install()
	{
		// First if the user hasn't provided an encryption key
		// lets make one
		if($_POST['encryption_key'] == "")
		{
			// Base chars
			$base = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$max = strlen($base)-1;

			$encrypt_key = '';
			mt_srand((double)microtime()*1000000);
			while (strlen($encrypt_key)<32)
			{
				$encrypt_key .= $base{mt_rand(0,$max)};
			}

			// Save key back to POST variable
			$_POST['encryption_key'] = $encrypt_key;
		}

		// Create the base_url
		$url_protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']== "on") ? "https" : "http");
		$url_host = $_SERVER['HTTP_HOST'];		

		// Define what variables need replacing in this file
		$replace = array(
				'base_url'			=> $url_protocol . "://" . $url_host . BASEDIR,
		    	'encryption_key' 	=> $_POST['encryption_key']);

		if ($result = PerformOverWrite($this->copyFrom, APPLICATION . $this->copyTo, $replace) !== TRUE)
		{
			$this->error = $result;
		}
		else
		{
			$this->status = TRUE;
		}

		return $this->status;
	}
}

/**
 * Overwrite Database Config
 *
 * Overwrite the default database config file with the new
 * custom file created using the Users input
 *
 * @package  	BackendPro
 * @subpackage 	Install
 */
class OverWriteDatabaseConfig extends Component
{
	var $name 		= "Create new database config file";
	var $copyFrom 	= "database.txt";
	var $copyTo 	= "config/database.php";

	function Install()
	{
		// Define what variables need replacing in this file
		$replace = array(
				'database_host'		=> $_POST['database_host'],
                'database_user'		=> $_POST['database_user'],
                'database_password'	=> $_POST['database_password'],
                'database_name'		=> $_POST['database_name']);

		if ($result = PerformOverWrite($this->copyFrom, APPLICATION . $this->copyTo, $replace) !== TRUE)
		{
			$this->error = $result;
		}
		else
		{
			$this->status = TRUE;
		}

		return $this->status;
	}
}

/**
 * Overwrite ReCAPTCHA Config
 *
 * Overwrite the default recaptcha config file with the new
 * custom file created using the Users input
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class OverWriteRecaptchaConfig extends Component
{
	var $name 		= "Create new ReCAPTCHA config file";
	var $copyFrom 	= "recaptcha.txt";
	var $copyTo 	= "recaptcha/config/recaptcha.php";

	function Install()
	{
		// Define what variables need replacing in this file
		$replace = array(
				'public_key'	=> $_POST['public_key'],
                'private_key'	=> $_POST['private_key']);

		if ($result = PerformOverWrite($this->copyFrom, MODULES . $this->copyTo, $replace) !== TRUE)
		{
			$this->error = $result;
		}
		else
		{
			$this->status = TRUE;
		}

		return $this->status;
	}
}



/* End of file SetupFileSystem.php */
/* Location: ./install/components/SetupFileSystem.php */
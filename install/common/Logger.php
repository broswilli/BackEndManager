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
 * Logger
 *
 * Allows simple log files to be created and lines written to them
 *
 * @package 	BackendPro
 * @subpackage	Install
 */
class Logger
{
	var $file_name  = 'install.log';
	var	$date_fmt   = 'Y-m-d H:i:s';

	/**
	 * Write Log Message
	 *
	 * Write a line to the log file with the given type
	 *
	 * @param string 	Type of log to write
	 * @param string 	Message to log
	 * @return bool 	Returns TRUE on success, FALSE otherwise
	 */
	function write($type = 'INFO', $msg = NULL)
	{
		if ($msg == NULL)
		{
			return FALSE;
		}

		$type = strtoupper($type);

		// Open the log file
		if ( ! $fp = fopen($this->file_name, 'ab'))
		{
			return FALSE;
		}

		$message = $type . " " . (($type == 'INFO') ? ' - ' : '- ') . date($this->date_fmt,time()) . " --> " . $msg . "\r\n";

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($this->file_name, 0777);
		return TRUE;
	}
}



/* End of file Logger.php */
/* Location: ./install/common/Logger.php */
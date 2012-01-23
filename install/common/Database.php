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
 * Database Class
 *
 * Used to provide very basic database connectivity
 *
 * @package 	BackendPro
 * @subpackage	Install
 */
class Database
{
	/**
	 * Stores a MySQL database connection instance
	 *
	 * @var unknown_type
	 */
	var $connection;

	/**
	 * Connect to Database
	 *
	 * @param 	string Database host machine
	 * @param 	string Database name
	 * @param 	string Database username
	 * @param 	string Database password
	 * @return 	bool TRUE if connection made, FALSE otherwise
	 */
	function Connect($host = NULL, $database = NULL, $user = NULL, $password = NULL)
	{
		global $logger;

		$this->connection = @mysql_connect($host, $user, $password);
		if ( ! $this->connection)
		{
			$logger->write('error', mysql_error());
			return FALSE;
		}

		if ( ! @mysql_select_db($database, $this->connection))
		{
			$logger->write('error', mysql_error());
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Query
	 *
	 * Run the given query on the current connection
	 *
	 * @param 	string SQL query to execute
	 * @return 	bool TRUE if query executed, FALSE otherwise
	 */
	function Query($sql = NULL)
	{
		global $logger;

		if($sql == NULL)
		{
			return FALSE;
		}

		if( ! @mysql_query($sql, $this->connection))
		{
			$logger->write('error', mysql_error());
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Run SQL Schema File
	 *
	 * Given a SQL Schema file process each query inside it
	 *
	 * @param 	string Filename of schema file in files/ dir
	 * @return 	bool TRUE if full schema executed, FALSE otherwise
	 */
	function RunSQLFile($file = NULL)
	{
		global $logger;

		$path = 'files/' . $file;

		if($file == NULL)
		{
			return FALSE;
		}

		if( ! $fp = @fopen($path, 'r'))
		{
			$logger->write('error', "Couldn't open " . $path);
			return FALSE;
		}

		$contents = fread($fp, filesize($path));
		fclose($fp);

		// Lets get rid of comment lines
		$contents = preg_replace('/--(.)*/', '', $contents);

		// Get rid of newlines
		$contents = preg_replace('/\n/', '', $contents);

		// Turn each statement into an array item
		$contents = explode(';', $contents);

		foreach($contents as $sql)
		{
			if( $sql == '')
			{
				continue;
			}

			if($this->Query($sql) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}



/* End of file Database.php */
/* Location: ./install/common/Database.php */
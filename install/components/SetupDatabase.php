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
 * Connect to Database
 *
 * Try to connect to the database provided
 * if not throw an error
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class ConnectToDatabase extends Component
{
	var $name = "Connect to database";

	function Install()
	{
		global $database;

		$this->status = $database->connect($_POST['database_host'],$_POST['database_name'],$_POST['database_user'],$_POST['database_password']);
		return $this->status;
	}
}

/**
 * Update Schema
 *
 * Update the database table schema using
 * the file provided
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class UpdateSchema extends Component
{
	var $name = "Update table schema";

	function Install()
	{
		global $database;

		$this->status = $database->RunSQLFile('database_schema.sql');
		return $this->status;
	}
}

/**
 * Create Administrator Account
 *
 * Creates the initial administrator account
 * in the database provided
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class CreateAdministrator extends Component
{
	var $name = "Create administrator user account";

	function Install()
	{
		global $database;

		// Encrypt the password
		$password = $_POST['password'] . $_POST['encryption_key'];
		$password = sha1($password);

		$queries[] = sprintf("INSERT INTO `be_users` (`id` ,`username` ,`password` ,`email` ,`active` ,`group` ,`activation_key` ,`last_visit` ,`created` ,`modified`)VALUES ('1', '%s', '%s', '%s', '1', '2', NULL , NULL , NOW( ) , NULL);",
			$_POST['username'],
			$password,
			$_POST['email']);
		$queries[] = "INSERT INTO `be_user_profiles` (`user_id`) VALUES ('1')";

		foreach($queries as $query)
		{
			if( ! $database->Query($query))
			{
				return $this->status;
			}
		}
		$this->status = TRUE;
		return $this->status;
	}
}



/* End of file SetupDatabase.php */
/* Location: ./install/components/SetupDatabase.php */
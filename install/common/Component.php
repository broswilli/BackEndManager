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
 * Component Class
 *
 * Allows a feature component to be modeled as an object
 *
 * @package 	BackendPro
 * @subpackage	Install
 */
class Component
{
	var $status = FALSE;
	var $name 	= "My Component";
	var $error 	= NULL;				// Error message if thrown

	/**
	 * Install Component
	 *
	 * @return bool	Status of install
	 */
	function install()
	{
		return $this->status;
	}
}
?>
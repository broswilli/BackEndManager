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
 * Feature Class
 *
 * Allows installation features to be defined as code objects.
 *
 * @package 	BackendPro
 * @subpackage 	Install
 */
class Feature
{
	var $name;								// Name of this feature
	var $components 			= array();	// Emptey list of components
	var $status 			 	= FALSE;	// Status of component installation
	var $prerequisiteFeature 	= NULL;		// Pre-requisite feature link

	function Feature($name="My Feature")
	{
		global $logger;

		$logger->write("info", "New feature '" . $name . "' created");
		$this->name = $name;
	}

	/**
	 * Attach Component
	 *
	 * Attach the specified component to the feature for install
	 *
	 * @param 	Component Component object you want to attach
	 * @return 	bool TRUE if component is attached, FALSE otherwise
	 */
	function attach_component($component=NULL)
	{
		global $logger;

		if($component == NULL OR !is_object($component) OR strtolower(get_parent_class($component)) != "component")
		{
			return FALSE;
		}

		$this->components[] = &$component;
		$logger->write("info","Component '" . $component->name . "' attached to feature '" . $this->name . "'");
		return TRUE;
	}

	/**
	 * Set Prerequisite Feature
	 *
	 * Specify a prerequsite feature which must have been installed
	 * correctly for this feature to procede with installation
	 *
	 * @param 	Feature Prerequisite feature needed
	 * @return 	bool TRUE on successful link, FALSE otherwise
	 */
	function set_prerequisite_feature($feature = NULL)
	{
		global $logger;

		if($feature == NULL OR !is_object($feature) OR strtolower(get_class($feature)) != "feature")
		{
			return FALSE;
		}

		$this->prerequisiteFeature = &$feature;
		$logger->write("info","Feature '" . $this->name . "' now has prerequisite '" . $feature->name . "'");
		return TRUE;
	}

	/**
	 * Install Feature
	 *
	 * Install the feature onto the users system
	 *
	 * @return bool TRUE on successful install, FALSE otherwise
	 */
	function install()
	{
		global $logger;

		// First check to see if a prerequisite feature
		// failed to install
		if( $this->prerequisiteFeature != NULL && $this->prerequisiteFeature->status===FALSE)
		{
			// Can't continue so fail also
			$logger->write("info",$this->name . " Feature installation haulted since its prerequisite feature '" . $this->prerequisiteFeature->name . "' failed to install");
			return $this->status;
		}

		// Lets procede with the install of this feature
		// So for each component try and install it
		$i=0;
		while($i < count($this->components))
		{
			if( $this->components[$i]->install()===FALSE )
			{
				// Component install failed
				$logger->write("error",$this->components[$i]->name . " Component install failed: " . $this->components[$i]->error);
				break;
			}
			else
			{
				// Procede with next component
				$logger->write("info",$this->components[$i]->name . " Component installed");
				$i++;
			}
		}

		if($i != count($this->components))
		{
			$logger->write("info","Attempting to roll back Components for '" . $this->name . "' Feature");
			// Not all the components where installed
			// so run uninstall scripts for those which ran
			for($j=0;$j<=$i;$j++)
			{
				if (method_exists($this->components[$j],'Uninstall'))
				{
					if($this->components[$j]->uninstall())
					{
						$logger->write("info",$this->components[$j]->name . " Component uninstalled");
					}
					else
					{
						$logger->write("error",$this->components[$j]->name . " Component uninstall failed: " . $this->components[$j]->error);
					}
				}
			}
			return $this->status;
		}
		$this->status = TRUE;
		$logger->write('info',$this->name . " Feature installed");
		return $this->status;
	}
}



/* End of file Feature.php */
/* Location: ./install/common/Feature.php */
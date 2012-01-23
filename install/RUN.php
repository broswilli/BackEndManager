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

include_once('common/Logger.php');
include_once('common/Database.php');
include_once('common/Feature.php');
include_once('common/Component.php');

// Setup a logger
$logger = new Logger();

// Setup the database connector
$database = new Database();

// Remove any trailing slashes from the paths
foreach($_POST as $key => $value)
{
	if(substr($key,0,3) == 'ci_')
	{
		// Found a path form input
		if(substr($value,-1) == '/')
		{
			// Trailing slash found, remove it
			$_POST[$key] = substr($value,0,-1);
		}
	}
}

// Define constants for the paths and folders	


// This is the extra sub folders needed for the website url to get from the domain name
// to the index.php file, so if you are on domain.com and your index.php file is located
// at domain.com/site, this value should be /site
$basedir = dirname(dirname($_SERVER['SCRIPT_NAME']));
$basedir.= (substr($basedir,-1)=='/') ? '' : '/';	// Add a trailing slash if needed
define('BASEDIR',$basedir);

// This is the full server-side path to the folder which contains the BackendPro
// files and the CI system folder
$basepath = $_SERVER['DOCUMENT_ROOT'] . BASEDIR;
define('BASEPATH',$basepath);

// These values should all be relative to the BASEPATH
$systempath = $_POST['ci_system'];
$systempath = (substr($systempath,0,1)=='/') ? substr($systempath,1) : $systempath; // Get rid of first slash
$systempath.= (substr($systempath,-1)=='/') ? '' : '/';	// Add a trailing slash if needed 
define('SYSTEM',BASEPATH . $systempath);

$apppath = $_POST['ci_application'];
$apppath = (substr($apppath,0,1)=='/') ? substr($apppath,1) : $apppath; // Get rid of first slash
$apppath.= (substr($apppath,-1)=='/') ? '' : '/';	// Add a trailing slash if needed 
define('APPLICATION',BASEPATH . $apppath);

$modulespath = $_POST['ci_modules'];
$modulespath = (substr($modulespath,0,1)=='/') ? substr($modulespath,1) : $modulespath; // Get rid of first slash
$modulespath.= (substr($modulespath,-1)=='/') ? '' : '/';	// Add a trailing slash if needed 
define('MODULES',BASEPATH . $modulespath);

$logspath = $_POST['ci_logs'];
$logspath = (substr($logspath,0,1)=='/') ? substr($logspath,1) : $logspath; // Get rid of first slash
$logspath.= (substr($logspath,-1)=='/') ? '' : '/';	// Add a trailing slash if needed 
define('LOGS',BASEPATH . $logspath);

$cachepath = $_POST['ci_cache'];
$cachepath = (substr($cachepath,0,1)=='/') ? substr($cachepath,1) : $cachepath; // Get rid of first slash
$cachepath.= (substr($cachepath,-1)=='/') ? '' : '/';	// Add a trailing slash if needed 
define('CACHE',BASEPATH . $cachepath);

$logger->write('info','Base path set to: ' . BASEPATH);
$logger->write('info','Base dir set to: ' . BASEDIR);
$logger->write('info','System path set to: ' . SYSTEM);
$logger->write('info','Application path set to: ' . APPLICATION);
$logger->write('info','Modules path set to: ' . MODULES);
$logger->write('info','Logs path set to: ' . LOGS);
$logger->write('info','Cache path set to: ' . CACHE);

// Define Install components
$features['writable_check'] = new Feature("FileSystem Check");
$features['copy_files'] = new Feature("Setup FileSystem");
$features['database_setup'] = new Feature("Setup Database");

// Setup any prerequisites
$features['copy_files']->set_prerequisite_feature($features['writable_check']);
$features['database_setup']->set_prerequisite_feature($features['copy_files']);

// Load component libraies
include_once("components/FileSystemCheck.php");
include_once("components/SetupFileSystem.php");
include_once("components/SetupDatabase.php");

// Associate components to filesystem check feature
$features['writable_check']->attach_component(new LogFolderWritable());
$features['writable_check']->attach_component(new AssetFoldersWritable());
$features['writable_check']->attach_component(new CacheFolderWritable());
$features['writable_check']->attach_component(new ConfigFilesWritable());

// Associate components to Setup Filesystem feature
$features['copy_files']->attach_component(new OverWriteSystemConfig());
$features['copy_files']->attach_component(new OverWriteDatabaseConfig());
$features['copy_files']->attach_component(new OverWriteRecaptchaConfig());

// Associate components to Setup Database Feature
$features['database_setup']->attach_component(new ConnectToDatabase());
$features['database_setup']->attach_component(new UpdateSchema());
$features['database_setup']->attach_component(new CreateAdministrator());

// Perform the install
$install_status = TRUE;
foreach($features as $key => $feature)
{
	// We need to do this since php4 dosn't support reference in forloops
	$block =& $features[$key];
	if ($block->install() === FALSE)
	{
		$install_status = FALSE;
	}
}

/* End of file RUN.php */
/* Location: ./install/RUN.php */
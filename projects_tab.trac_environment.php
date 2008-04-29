<?php
/**
 * This tab extends the projects module with options to work with the tracIntegration module
 * It checks whether there is a trac environment for the currently selected project
 * @author David Raison <david@ion.lu>
 * @version 0.3-rc2
 * @since 0.3-rc2
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

// save tab id
$AppUI->setState('tabid',dPgetParam($_REQUEST,'tab'));

// load our class
require_once $AppUI->getModuleClass('trac');
$tracProj = new CTracIntegrator();
$project_id = intval( dPgetParam( $_REQUEST, 'project_id', 0 ) );

$myhost = $tracProj->fetchHosts($project_id);
$environment = $tracProj->fetchEnvironments($project_id);

// check if a host has been defined for this module
if (!empty($myhost) && !empty($environment)) { 
 	// a) offer a link
	print('<p>');
	print($tracProj->showLink($project_id));
	print('</p>');
 	// b) see if we can extract some information (curl?!)
	// c) add a button to reconfigure
} elseif (empty($myhost) || empty($environment) || dPgetParam($_REQUEST, 'trac_configure', 0)) // if no host found or configuration request, offer a form:
	print($tracProj->displayConfigForm($project_id));
?>

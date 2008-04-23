<?php
/**
 * Document!
 */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

print('hello world');
/** firstly, do we have permission to access the trac module? (access is granted if the tab is shown)
if ($perms->checkModule('trac', 'view'))
 * check if there is an environment for this project
 *  1) if not, ask if the user wants to create one
 *  2) if there is an environment
 *   a) offer a link
 *   b) see if we can extract some information (curl?!)
 */

/* this file is required in the projects module, so we need to load our class
require_once $AppUI->getModuleClass('resources');
$resource =& new CResource;
 * and maybe some js?
$AppUI->getModuleJS('resources', 'tabs');

*/


?>

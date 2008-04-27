<?php /* TRAC $Id: index.php,v 1.4 2008/04/24 23:53:33 david_iondev Exp $ */
/**
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.3-rc2
 * @since 0.1
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @todo 
 *	Add tasks module integration
 * 		i.e. allow to link to specific trac tickets from within tasks	( in 0.3-rc3)
 * 	Preserve states (urls) between tab switches	(in 0.4)
 */

if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

/** Some notes for the dev, please don't mind this for the time being 
var_dump($AppUI->user_id);	
var_dump($perms->getPermittedUsers("trac"));	
$canEdit = $perms->checkModuleItem( $m, 'edit', $moditem );
*/

// Checking permissions
$perms =& $AppUI->acl();
$canRead = $perms->checkModule( $m, 'view');
$canEdit = $perms->checkModule( $m, 'edit');
$canAdd = $perms->checkModule( $m, 'add');
$canDelete = $perms->checkModule( $m, 'delete');
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Trac', 'trac_logo.png', $m, "$m.$a" );
if ($canAdd || $canDelete) $titleBlock->addCrumb('?m=trac&a=addEnv',$AppUI->_('Manage Trac Environments'));
$titleBlock->show();

// all trac environments and display them as tabs (they are here, now, we could also add them into the db [later])
// in the same go, we could add the url into the DB, so as to make this module publicly available.
$tracProj = new CTracIntegrator();
$tracenvs = $tracProj->fetchEnvironments();
$AppUI->setState('tracenvs',$tracenvs);

// what trac environment to load
if(($env = dPgetParam($_REQUEST,'env','')) != ''){	// set
	$AppUI->setState('environment',$env);
	//$tabs = array_keys($tracenvs,$env);
	// multi-dimensional array!!
	// is array_search R?
	//$tab = $tabs[0];
} else {
	$tab = dPgetParam($_REQUEST,'tab',0);
	$tab = ($tab != '') ? $tab : 0;
}

// check if a project exists?
$hosts = $tracProj->fetchHosts();
if (empty($hosts))
	$AppUI->setMsg("You need to configure a trac host for one of your projects in the projects module first.",UI_MSG_WARNING);
elseif (empty($tracenvs))
	$AppUI->setMsg("You need to add at least one environment.",UI_MSG_WARNING);
else {
	// generate tabs
	$tabBox = new CTabBox('?m=trac',dPgetConfig('root_dir').'/modules/trac/',$tab);
	foreach($tracenvs as $env)
		$tabBox->add('embed',$env['dtenvironment']);
	$tabBox->show();
}
?>

<?php /* TRAC $Id: index.php,v 1.8 2008/04/30 03:35:17 david_iondev Exp $ */
/**
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.3
 * @since 0.1
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @todo 
 *    v0.3: 
 *				a) Allow to reconfigure Environments in the Projects tab
 *				b) Add tasks module integration - allow to link to specific trac tickets from within tasks.
 *    v0.4: Preserve states (urls) between tab switches.
 *    v0.5: Add support for trac xmlrpc calls (http://trac-hacks.org/wiki/XmlRpcPlugin).
 *		undef: Use CPdObject as Parent Class
 *
 */

if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

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

$tracProj = new CTracIntegrator();
$tracenvs = $tracProj->fetchEnvironments();
$envids = array_keys($tracenvs);
$AppUI->setState('tracenvs',$tracenvs);	// reusing it in embed.php, this is saving us a query

// what trac environment to load
if(($envId = dPgetParam($_REQUEST,'envId','')) != '')
	$tab = $envId;
else
	$tab = dPgetParam($_REQUEST,'tab',$tracenvs[$envids[0]]['idenvironment']);	// first env as default

// check if a project exists?
$hosts = $tracProj->fetchHosts();
if (empty($hosts))
	$AppUI->setMsg("You need to configure a trac host for one of your projects in the projects module first.",UI_MSG_WARNING);
elseif (empty($tracenvs))
	$AppUI->setMsg("You need to add at least one environment.",UI_MSG_WARNING);
else { 	// generate tabs
	$tabBox = new CTabBox('?m=trac',dPgetConfig('root_dir').'/modules/trac/',$tab);	// $tab is selected by default
	foreach($tracenvs as $env)
		$tabBox->add('embed',$env['dtenvironment'],false,$env['idenvironment']); // set environment id as tab key
	$tabBox->show();
}
?>

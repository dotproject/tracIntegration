<?php
/**
 * $Id: index.php,v 1.12 2008/05/02 14:10:37 david_iondev Exp $
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package TracIntegration
 * @version 0.5
 * @since 0.1
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @todo 
 *	v0.5:
 *				- Add support for trac xmlrpc calls (http://trac-hacks.org/wiki/XmlRpcPlugin)
 *				- Document trac.class
 *	v0.6: 
 * 				- Base TracIntegrator Class on CDpObject
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

<?php
/**
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.2
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Trac', 'trac_logo.png', $m, "$m.$a" );
$titleBlock->addCrumb('?m=trac&a=setUrl',$AppUI->_('Set Trac URL'));
$titleBlock->addCrumb('?m=trac&a=addEnv',$AppUI->_('Manage Trac Environments'));
$titleBlock->show();

// all trac environments and display them as tabs (they are here, now, we could also add them into the db [later])
// in the same go, we could add the url into the DB, so as to make this module publicly available.
$tracProj = new CTracProj();
$tracenvs = $tracProj->fetchEnvironments();
$AppUI->setState('tracenvs',$tracenvs);

// what trac environment to load
if(($env = dPgetParam($_REQUEST,'env','')) != ''){	// set
	//$env = dPgetParam($_REQUEST,'env','');	// third arg = default
	$AppUI->setState('environment',$env);
	$tabs = array_keys($tracenvs,$env);
	$tab = $tabs[0];
} else {
	$tab = dPgetParam($_REQUEST,'tab',0);
	$tab = ($tab != '') ? $tab : 0;
}

if ($tracProj->getURL() == '')
	$AppUI->setMsg("You need to set an URL.",UI_MSG_WARNING);
elseif (empty($tracenvs))
	$AppUI->setMsg("You need to add at least one environment.",UI_MSG_WARNING);
else {
	// generate tabs
	$tabBox = new CTabBox('?m=trac',dPgetConfig('root_dir').'/modules/trac/',$tab);
	foreach($tracenvs as $env)
		$tabBox->add('embed',$env['dtvalue']);
	$tabBox->show();
}
?>

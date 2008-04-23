<?php
/**
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.3-rc1
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

if (!defined('DP_BASE_DIR')){
        die('You should not access this file directly.');
}

// Checking permissions
$perms =& $AppUI->acl();
$canDelete = $perms->checkModule( $m, 'delete');
$canAdd = $perms->checkModule( $m, 'add');

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Trac', 'trac_logo.png', $m, "$m.$a" );
$titleBlock->addCrumb('?m=trac&a=index',$AppUI->_('List Environments'));
$titleBlock->addCrumb('?m=trac&a=setUrl',$AppUI->_('Set Trac URL'));
$titleBlock->show();
$tracpr = new CTracProj();

if (($newenv = dPgetParam($_REQUEST,'environment')) != '' && $canAdd){
	if($tracpr->addEnvironment($newenv))
		$AppUI->setMsg('Environment added',UI_MSG_OK);
	else
		$AppUI->setMsg('Failed adding',UI_MSG_ERROR);
	
} elseif (($todel = dPgetParam($_REQUEST,'delete')) != '' && $canDelete){
	if($tracpr->deleteEnvironment($todel))
		$AppUI->setMsg('Environment deleted',UI_MSG_OK);
	else
		$AppUI->setMsg('Failed deleting',UI_MSG_ERROR);
}

?>
<h3>Existing Environments</h3>
<form name="deleteEnv" action="?m=trac&a=addEnv" method="post">
<table style="border: 1px solid navy; background: skyblue url(style/default/images/titlegrad.jpg) repeat scroll 0% 50%; width: 40%; color: white;">
<thead><tr><th>Name</th></tr></thead><tbody>
<?php
foreach($tracpr->fetchEnvironments() as $env){
	print('<tr><td>'.$env['dtvalue'].'</td><td>');
	if($canDelete){
		print('<button id="delete'.$env['dtvalue'].'" name="delete"'
		.'value="'.$env['dtvalue'].'" style="background: transparent;'
		.'border:0;" title="Click to delete this environment">'
		.dPshowImage( './images/icons/stock_delete-16.png', '16', '16',  '' )
		.'</button></td></tr>');
	}
}
?>
</tbody></table>
</form>
<?php
if ($canAdd) {
?>
	<h3>New Environment</h3>
	<form action="?m=trac&a=addEnv" method="post" name="addNewEnv">
	<table style="border: 1px solid navy; background: skyblue url(style/default/images/titlegrad.jpg) repeat scroll 0% 50%; width: 40%; color: white;">
	<thead><tr><th>Name</th></tr></thead><tbody>
	<tr><td><label for="environment">Environment name</label>&nbsp;<input id="newenv" name="environment" type="text"/></td></tr>
	<tr><td><button name="submit" value="submit" title="Click to add">Add</button></td></tr>
	</tbody></table>
	</form>
<?php
}
?>

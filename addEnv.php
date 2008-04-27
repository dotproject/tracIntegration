<?php
/**
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.3-rc2
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */
if (!defined('DP_BASE_DIR')){
        die('You should not access this file directly.');
}

// Checking permissions
$perms =& $AppUI->acl();
$canRead = $perms->checkModule( $m, 'view');
$canDelete = $perms->checkModule( $m, 'delete');
$canAdd = $perms->checkModule( $m, 'add');

if(!$canRead)
	$AppUI->redirect('m=public&a=access_denied');

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Trac', 'trac_logo.png', $m, "$m.$a" );
$titleBlock->addCrumb('?m=trac&a=index',$AppUI->_('List Environments'));
$titleBlock->show();

$tracpr = new CTracIntegrator();

// first check if a project_id is defined. Else we can't save anything at all
if(($project_id = dPgetParam($_REQUEST,'project_id')) != ''){

	// Save environments
	if (($newenv = dPgetParam($_REQUEST,'newenv')) != '' && $canAdd){
		if($tracpr->addEnvironment($newenv,$project_id))
			$AppUI->setMsg('Environment added',UI_MSG_OK);
		else
			$AppUI->setMsg('Failed adding',UI_MSG_ERROR);
	}
	
	// Save new host
	if (($newurl = dPgetParam($_REQUEST,'newurl')) != '' && $canAdd){
		if($tracpr->addHost($newurl,$project_id))
			$AppUI->setMsg('Host added',UI_MSG_OK);
		else
			$AppUI->setMsg('Host not added',UI_MSG_ERROR);

	// Delete environments	
	} elseif (($todel = dPgetParam($_REQUEST,'delete')) != '' && $canDelete){
		if($tracpr->deleteEnvironment($todel))
			$AppUI->setMsg('Environment deleted',UI_MSG_OK);
		else
			$AppUI->setMsg('Failed deleting',UI_MSG_ERROR);
	}
	// redirect to projects module if that's we we came from
	if (dPgetParam($_REQUEST,'submit') == 'saveTracConf'){
		$tab = $AppUI->getState('tabid');
		$AppUI->redirect('m=projects&a=view&project_id='.$project_id.'&tab='.$tab);	
		return;
	}
}

?>
<h3>Existing Environments</h3>
<form name="deleteEnv" action="?m=trac&a=addEnv" method="post">
<table style="border: 1px solid navy; background: skyblue url(style/default/images/titlegrad.jpg) repeat scroll 0% 50%; width: 40%; color: white;">
<thead><tr><th>Name</th></tr></thead><tbody>
<?php
$envs = $tracpr->fetchEnvironments();
foreach($envs as $env){
	print('<tr><td>'.$env['dtenvironment'].'</td><td>');
	if($canDelete){
		print('<button id="delete'.$env['dtenvironment'].'" name="delete"'
		.'value="'.$env['idenvironment'].'" style="background: transparent;'
		.'border:0;" title="Click to delete this environment">'
		.dPshowImage( './images/icons/stock_delete-16.png', '16', '16',  '' )
		.'</button></td></tr>');
	}
}
?>
</tbody></table>
</form>
<?php
/**
 * this way of editing is deprecated in favor of adding environments directly in the projects module, but I'll leave this option open for the time being
 * @todo try to set the host automatically when a project has been selected or to narrow down the possible choices of projects when a host has been selected
 */

if ($canAdd) {
	// fetch the existing projects
	require_once $AppUI->getModuleClass('projects');
	$projObj = new CProject();
	$projectRows = $projObj->getAllowedProjectsInRows($AppUI->user_id);
	$projects = $projectRows->GetRows();	// FetchRow(), GetAssoc()	[AdoDB RecordSet object]
?>
	<h3>New Environment</h3>
	<form action="?m=trac&a=addEnv" method="post" name="addNewEnv">
	<table style="border: 1px solid navy; background: skyblue url(style/default/images/titlegrad.jpg) repeat scroll 0% 50%; width: 40%; color: white;">
	<thead><tr><th>Name</th></tr></thead><tbody>
	<tr><td><label for="project">Project</label></td><td><select id="projects" name="project_id" onchange=""/>	<!-- Restrict hosts -->
	<?php
		foreach($projects as $project)
			printf('<option value="%d">%s</option>',$project['project_id'],$project['project_name']);
	?>
	</select></td></tr>
	<tr><td><label for="host">Host</label></td><td><input id="newenv" name="environment" type="text"/></td></tr>
	<tr><td><label for="environment">Environment name</label></td><td><input id="environment" name="newenv" type="text"/></td></tr>
	<tr><td colspan="2" style="text-align:right;"><button name="submit" value="saveEnv" title="Click to add">Add</button></td></tr>
	</tbody></table>
	</form>
<?php
}
?>

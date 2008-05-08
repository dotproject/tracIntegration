<?php
/**
 * $Id: projects_tab.trac_environment.php,v 1.6 2008/05/07 11:45:15 david_iondev Exp $
 * This tab extends the projects module with options to work with the tracIntegration module
 * It checks whether there is a trac environment for the currently selected project
 * @author David Raison <david@ion.lu>
 * @version 0.5
 * @since 0.3-rc2
 * @package TracIntegration
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}
define('XMLRPCHP','http://trac-hacks.org/wiki/XmlRpcPlugin');
// load our class
require_once $AppUI->getModuleClass('trac');
$tracProj = new CTracIntegrator();

// save tab id
$tab = dPgetParam($_REQUEST,'tab');
$AppUI->setState('tabid',$tab);

$project_id = intval( dPgetParam( $_REQUEST, 'project_id', 0 ) );
$myhost = $tracProj->fetchHosts($project_id);
$environment = $tracProj->fetchEnvironments($project_id);
$reconfigure = dPgetParam($_REQUEST, 'trac_configure', NULL);

// check if a host has been defined for this module
if (!empty($myhost) && !empty($environment) && $reconfigure == NULL) { 
	$env = $tracProj->fetchEnvironments($project_id);
 	print('<table style="border:1px solid black;width:70%;"><tr>'
 		.'<th>Link:</th><td><a href="?m=trac&envId='.$env['idenvironment'].'">'
 		.'Go to <strong>'.$env['dtenvironment'].'</strong> trac environment.</a></td>'
 		.'<th>Configure:</th><td><a href="?m=projects&a=view&project_id='.$project_id.'&tab='.$tab.'&trac_configure=true">'
 		.dPshowImage( './images/icons/stock_edit-16.png', '16', '16',  '' )
 		.'</a></td></tr>');
 	if($env['dtrpc']){
 		$tracrpc = new CTracRPC($env);
 		$open = $tracrpc->getTotalOpenTickets();
 		$milestones = $tracrpc->fetchMilestones();
 		$components = $tracrpc->fetchComponents();
 		print('<tr><th colspan="4" style="border-top:1px dashed black;background-color:white;">Additional data supplied by XMLRPC</th></tr>'
 			.'<tr><th colspan="2">Total of open tickets:</th><td colspan="2">'.$open.'</td></tr>'
 			.'<tr><th>Milestones:</th><td>'.implode('<br/>',$milestones).'</td>'
 			.'<th>Components:</th><td>'.implode('<br/>',$components).'</td></tr>');
 	}
 	print('</table>');
} elseif (empty($myhost) || empty($environment) || $reconfigure == "true"){ // if no host found or configuration request, offer a form:
	$hosts = $tracProj->fetchHosts();
	$out = '<p>If there is a trac environment available for this project, please select or enter the URL 
			of its HOST and the name of the environment below:</p>
		   <form action="?m=trac&a=addEnv" method="post" name="setHostEnv">
			<input type="hidden" name="project_id" value="'.$project_id.'"/>
		   <table style="width: auto;">
		   <tbody>';
   	if(!empty($hosts)){		// if we have some hosts already, display them to select from
		$out .= '<tr><td><label for="existURL">Trac HOST</label></td>
			<td><select class="text" name="existURL">
			<option value="0">Pick a host</option>';
	     	// generate options for the select box
	    if (!empty($myhost)) $AppUI->setState('oldhost',$myhost['idhost']);	// we will need this later when checking for changes to the setup
   		foreach($hosts as $host){
			$selected = ($host['fiproject'] == $project_id) ? 'selected' : 'false';
			$out .= sprintf('<option value="%1$d" selected="%3$s">%2$s</option>',$host['idhost'],$host['dthost'],$selected);
		}
	    $out .= '</select>&nbsp;or:&nbsp;</td></tr>';
    }	// !empty($hosts)
    // add a text field to add new hosts
    $env = $tracProj->fetchEnvironments($project_id);
    $enabled = ($env['dtrpc']) ? 'checked="checked"' : '';
    $AppUI->setState('oldenv',$env['dtenvironment']);
    $AppUI->setState('oldrpc',$env['dtrpc']);
   	$out .= '<tr><td><label for="newurl">Enter a new host</label></td>
			<td><input class="text" id="tracurl" name="newurl" maxlength="60" type="text" size="50"/></td></tr>
			<tr><td><label for="newenv">Trac Environment</label></td>
			<td><input id="tracenv" class="text" name="newenv" type="text" maxlength="60" size="50" value="'.$env['dtenvironment'].'"/></td></tr>
			<tr><td><label for="hasrpc">XML-RPC support</label></td>
			<td><input id="hasrpcCheck" name="hasrpc" value="1" '.$enabled.' type="checkbox"/>
			&nbsp;does this environment have the <a style="text-decoration:underline;" href="'.XMLRPCHP.'">XmlRpcPlugin</a> installed?</td>
			<tr><td colspan="2" style="text-align:right;"><button class="button" name="submit" value="saveTracConf" title="Click to Save">Save</button></td></tr>
			</tbody></table></form>';
	print($out);
}
?>

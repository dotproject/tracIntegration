<?php
/**
 * $Id$
 * This tab extends the projects module with options to work with the tracIntegration module
 * It checks whether there is a trac environment for the currently selected project
 * @author David Raison <david@ion.lu>
 * @version 0.3
 * @since 0.3-rc2
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @todo 0.5: add xmlrpc support
 */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}
// load our class
require_once $AppUI->getModuleClass('trac');
$tracProj = new CTracIntegrator();

// save tab id
$tab = dPgetParam($_REQUEST,'tab');
$AppUI->setState('tabid',$tab);

$project_id = intval( dPgetParam( $_REQUEST, 'project_id', 0 ) );
$myhost = $tracProj->fetchHosts($project_id);
$environment = $tracProj->fetchEnvironments($project_id);

$reconfigure = dPgetParam($_REQUEST, 'trac_configure');

// check if a host has been defined for this module
if (!empty($myhost) && !empty($environment) && $reconfigure == NULL) { 
	$env = $tracProj->fetchEnvironments($project_id);
 	print('<div style="padding:5px;width:50%;">'
 			.'<a href="?m=trac&envId='.$env['idenvironment'].'">Go to <strong>'.$env['dtenvironment'].'</strong> trac environment.</a>&nbsp;'
 			.'<a href="?m=projects&a=view&project_id='.$project_id.'&tab='.$tab.'&trac_configure=true">'
 			.dPshowImage( './images/icons/stock_edit-16.png', '16', '16',  '' )
 			.'</a></div>');
} elseif (empty($myhost) || empty($environment) || $reconfigure == "true"){ // if no host found or configuration request, offer a form:
	$hosts = $tracProj->fetchHosts();
	$out = '<p>If there is a trac environment available for this project, please select or enter the URL 
			of its HOST and the name of the environment below:</p>
		   <form action="?m=trac&a=addEnv" method="post" name="setHostEnv">
			<input type="hidden" name="project_id" value="'.$project_id.'"/>
		   <table style="width: auto;">
		   <tbody>';
   	if(!empty($hosts)){
		$out .= '<tr><td><label for="existURL">Trac HOST</label></td>
			<td><select class="text" name="existURL">
			<option value="0">Pick a host</option>';
	     	// generate options
   		foreach($hosts as $host){
			$selected = ($host['fiproject'] == $project_id) ? 'selected' : 'false';
	        $out .= sprintf('<option value="%d" selected="%s">%s</option>',$host['idhost'],$host['fiproject'],$host['dthost']);
		}
	    $out .= '</select></td></tr>';
    }
   	$out .= '<tr><td><label for="newurl">Enter a new host</label></td>
			<td><input class="text" id="tracurl" name="newurl" type="text" size="40" value="'.$url.'"/>
			</td></tr><tr><td><label for="newenv">Trac Environment</label></td>
			<td><input id="tracenv" class="text" name="newenv" type="text" size="40" value="'.$env.'"/></td></tr>
			<tr><td colspan="2" style="text-align:right;"><button class="button" name="submit" value="saveTracConf" title="Click to Save">Save</button></td></tr>
			</tbody></table></form>';
	print($out);
	}
?>

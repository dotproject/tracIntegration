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

// load our class
require_once $AppUI->getModuleClass('trac');
$tracProj = new CTracProject();

$project_id = intval( dPgetParam( $_REQUEST, 'project_id', 0 ) );
$host = $tracProj->fetchHost($project_id);
if (!empty($host)) { // check if a host has been defined for this module
/* 
 * check if some environment already exists for this module
 *   a) offer a link
 *   b) see if we can extract some information (curl?!)
 */

} elseif (empty($host) || dPgetParam($_REQUEST, 'trac_configure', 0)) {// if no host found or configuration request, offer a form:
	?>
	<p>If there is a trac environment available for this project, please select or enter the URL of its HOST and the name of the environment below:</p>
	<form action="?m=trac&a=addEnv" method="post" name="setHostEnv">
	<table style="width: auto;">
	<tbody>
	<tr><td>
	<label for="existURL">Trac HOST</label></td>
	<!-- a select box would be better here -->
	<td><select name="existURL">
	<option value="0">Pick a host</option>
	<?php
		// generate options
	?>
	</select></td></tr>
	<tr><td><label for="newurl">OR enter a new one</label></td>
	<td><input id="tracurl" name="newurl" type="text" size="40" value="<?php print($url); ?>"/>
	</td></tr><tr><td>
	<label for="newenv">Trac Environment</label></td>
	<td><input id="tracenv" name="newenv" type="text" size="40" value="<?php print($env); ?>"/></td></tr>
	<tr><td colspan="2" style="text-align:right;"><button name="submit" value="saveTracConf" title="Click to Save">Save</button></td></tr>
	</tbody></table>
	</form>
	<?php
}
?>

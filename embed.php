<?php
/**
 * $Id$
 * Trac integration for dotProject
 *
 * @author David Raison <david@ion.lu>
 * @package dpTrac
 * @version 0.3
 * @since 0.1
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */
// $tab and $envId are used in the same way
$tracenvs = $AppUI->getState('tracenvs');
$envids = array_keys($tracenvs);
$defaultEnv = $tracenvs[$envids[0]]['idenvironment'];
$envId = dPgetParam($_REQUEST,'envId',$defaultEnv);
$envId = ($envId == '' || empty($envId)) ? dPgetParam($_REQUEST,'tab',$defaultEnv) : $envId;
$envName = $tracenvs[$envId]['dtenvironment'];

$ticket = dPgetParam($_REQUEST,'ticket');

$tracProj = new CTracIntegrator();
$host = $tracProj->getHostFromEnvironment($envId);

$url = $host.$envName;
$url = ($ticket != '') ? $url.'/ticket/'.$ticket : $url;

print('<iframe src="'.$url.'" width="100%" height="700" frameborder="0" name="tracFrame" style="padding:2px;">Iframes-Error</iframe>');

?>

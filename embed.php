<?php

// what trac environment to load
$envName = $AppUI->getState('environment');

var_dump($_REQUEST);
// overall environment array to resolve tab idx
if ($env == '' || empty($env)) {
	$tracenvs = $AppUI->getState('tracenvs');
	$tab = dPgetParam($_REQUEST,'tab',0);
	$envName = $tracenvs[$tab]['dtenvironment'];
	$envId = $tracenvs[$tab]['idenvironment'];
}
$tracProj = new CTracIntegrator();
$url = $tracProj->getHostFromEnvironment($envId);

print('<iframe src="'.$url.$envName.'" width="100%" height="700" frameborder="0" name="tracFrame" style="padding:2px;">Iframes-Error</iframe>');

?>

<?php

// $tab and $envId are used in the same way
$envId = dPgetParam($_REQUEST,'envId',1);
$envId = ($envId == '' || empty($envId)) ? dPgetParam($_REQUEST,'tab',1) : $envId;
$tracenvs = $AppUI->getState('tracenvs');
$envName = $tracenvs[$envId-1]['dtenvironment'];	// array starts from 0, envs start from 1

$tracProj = new CTracIntegrator();
$url = $tracProj->getHostFromEnvironment($envId);

print('<iframe src="'.$url.$envName.'" width="100%" height="700" frameborder="0" name="tracFrame" style="padding:2px;">Iframes-Error</iframe>');

?>

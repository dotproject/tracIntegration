<?php

// $tab and $envId are used in the same way
$tracenvs = $AppUI->getState('tracenvs');
$envids = array_keys($tracenvs);
$defaultEnv = $tracenvs[$envids[0]]['idenvironment'];
$envId = dPgetParam($_REQUEST,'envId',$defaultEnv);
$envId = ($envId == '' || empty($envId)) ? dPgetParam($_REQUEST,'tab',$defaultEnv) : $envId;
$envName = $tracenvs[$envId]['dtenvironment'];


// @TODO 0.3 if a ticket number has been given, figure it out and change the url accordingly

$tracProj = new CTracIntegrator();
$url = $tracProj->getHostFromEnvironment($envId);

print('<iframe src="'.$url.$envName.'" width="100%" height="700" frameborder="0" name="tracFrame" style="padding:2px;">Iframes-Error</iframe>');

?>

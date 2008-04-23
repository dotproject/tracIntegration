<?php

// what trac environment to load
$env = $AppUI->getState('environment');

// overall environment array to resolve tab idx
if ($env == ''| empty($env)) {
	$tracenvs = $AppUI->getState('tracenvs');
	$tab = dPgetParam($_REQUEST,'tab',0);
	$env = $tracenvs[$tab]['dtvalue'];
}
// fetch the url from the DB later on
$tracProj = new CTracProj();
$url = $tracProj->getURL();

print('<iframe src="'.$url.$env.'" width="100%" height="700" frameborder="0" name="tracFrame" style="padding:2px;">Iframes-Error</iframe>');

?>

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
$canEdit = $perms->checkModule( $m, 'edit');
if (!$canEdit) {
        $AppUI->redirect( "m=public&a=access_denied" );
}  

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'Trac', 'trac_logo.png', $m, "$m.$a" );
$titleBlock->addCrumb('?m=trac&a=index',$AppUI->_('List Environments'));
$titleBlock->addCrumb('?m=trac&a=addEnv',$AppUI->_('Manage Trac Environments'));
$titleBlock->show();
$tracpr = new CTracProj();

// we also need to consider deletion of the url
if(dPgetParam($_REQUEST,'submit') == 'change'){
	$newurl = dPgetParam($_REQUEST,'newurl');
        if($tracpr->setURL($newurl))
		$AppUI->setMsg('URL changed',UI_MSG_OK);
	else
		$AppUI->setMsg('URL not changed',UI_MSG_ERROR);
}

$url = $tracpr->getURL();

?>
<h3>Set Trac URL</h3>
<form action="?m=trac&a=setUrl" method="post" name="changeUrl">
<table style="border: 1px solid navy; background: skyblue url(style/default/images/titlegrad.jpg) repeat scroll 0% 50%; width: 40%; color: white;">
<tbody>
<tr><td>
<label for="newurl">Trac URL</label>&nbsp;
<input id="tracurl" name="newurl" type="text" size="40" value="<?php print($url); ?>"/>
</td><td><button name="submit" value="change" title="Click to change">Change</button></td></tr>
</tbody></table>
</form>




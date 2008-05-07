<?php
/**
 * $Id: ticket_backend.php,v 1.1 2008/05/02 15:34:16 david_iondev Exp $
 * @since 0.3
 * @version 0.5
 * @package TracIntegration
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @author David Raison <david@ion.lu>
 */

if (!defined('DP_BASE_DIR')){
   die('You should not access this file directly.');
}
require_once 'trac.class.php';
$tracticket = new CTracTicket();

$task = dPgetParam($_REQUEST,'task_id');

$perms =& $AppUI->acl();
$canAdd = $perms->checkModule('trac','add');
$canEdit = $perms->checkModule('trac','edit');
$canDelete = $perms->checkModule('trac','delete');

if(!$canAdd){
	$AppUI->redirect();
} else {
	if (dPgetParam($_REQUEST,'addticket') == 'yes' && $canAdd && $task != ''){
		if($tracticket->addTicket(dPgetParam($_REQUEST,'ticketnum'),dPgetParam($_REQUEST,'newsummary'),$task))
			$AppUI->setMsg('Ticket attached',UI_MSG_OK);
		else
			$AppUI->setMsg('Error attaching ticket',UI_MSG_ERROR);
	} elseif (($id = dPgetParam($_REQUEST,'changesummary')) != '' && $canEdit){
		$summaries = dPgetParam($_REQUEST,'summary');
		if($tracticket->updateTicket($id,$summaries[$id]))
			$AppUI->setMsg('Ticket summary changed',UI_MSG_OK);
		else
			$AppUI->setMsg('Error updating ticket summary',UI_MSG_ERROR);
	} elseif (($todel = dPgetParam($_REQUEST,'deleteticket')) != '' && $canDelete){
		if($tracticket->deleteTicket($todel))
			$AppUI->setMsg('Ticket deleted',UI_MSG_OK);
		else
			$AppUI->setMsg('Error deleting ticket',UI_MSG_ERROR);
	}
	$AppUI->redirect('m=tasks&a=addedit&task_id='.$task);
}

?>

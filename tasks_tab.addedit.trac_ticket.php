<?php
/**
 * 
 * @since 0.3
 * @version 0.3
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @author David Raison <david@ion.lu>
 */

if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

// figure out what project this task belongs to by loading the task item
require_once $AppUI->getModuleClass('tasks');
$task = new CTask();
$task_id = dPgetParam($_REQUEST,'task_id',0);
$task->load($task_id);
$task_project = intval($task->task_project);

if(!$task_project)
	$task_project = dPgetParam($_REQUEST,'task_project',0);
if(!$task_project){
	$AppUI->setMsg( "badTaskProject", UI_MSG_ERROR );
	$AppUI->redirect();
}

// now load our own class
require_once $AppUI->getModuleClass('trac');
$tracticket = new CTracTicket();
$env = $tracticket->hasTrac($task_project);
if (!$env){
	print('<p>'.$AppUI->_('This Project has no Trac Environment.').'</p>');
	return;
}

$perms =& $AppUI->acl();
$canAdd = $perms->checkModule($m,'add');
$canEdit = $perms->checkModule($m,'edit');
$canDelete = $perms->checkModule($m,'delete');

if(!$canAdd){
	print('<p>'.$AppUI->_('You do not have permission to add tickets to this task.').'</p>');
	return;
}

// 1. list attached tickets (num [label], summary [textbox], dustbin, update)
?>
<p>Attached Tickets<br/>
<form name="tracTicketAttach" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<table><thead><tr><th>Ticket #</th><th>Summary</th><th colspan="2">Actions</th></tr></thead>
<tbody>
<?php
	if($task_project && $task_id){
		// fetch host and environment
		//$host = $tracticket->fetchHosts($task_project);
		$env = $tracticket->fetchEnvironments($task_project);
		// fetch ticket numbers
		$tickets = $tracticket->fetchTickets($task_id);
		foreach($tickets as $ticket){
			$url = '?m=trac&envId='.$env['idenvironment'].'&ticket='.$ticket['fiticket'];
			printf('<tr><td><a href="%1$s">#%2$s</a></td>'
					.'<td><input type="text" class="text" value="%3$s" name="summary[]" '
					.'maxlength="50" style="width:50em;"/></td>'
					.'<td><button id="deleteTicket_%4$d" name="deleteticket"'
            	.'value="%4$d" style="background: transparent;'
            	.'border:0;" title="Click to delete this attachment">'
            	.dPshowImage( './images/icons/stock_delete-16.png', '16', '16',  '' )
            	.'</button></td>'
					.'<td><button name="action" class="button" value="changeticket" '
					.'title="'.$AppUI->_('Click to update this ticket').'">'
					.'Update</button></td></tr>',$url,$ticket['fiticket'],$ticket['dtsummary'],$ticket['idticket']);
		}
	}
// 2. add an empty row to attach a new ticket (num [textbox 5], summary [textbox 50], attach) 
?>
<tr>
<td>#<input type="text" class="text" name="ticketnum" maxlength="5" style="width:5em;"/></td>
<td><input type="text" class="text" name="summary" maxlength="50" style="width:50em;"/></td>
<td><button name="action" class="button" value="newticket" title="<?php echo $AppUI->_('Click to attach this ticket'); ?>">
Attach</button></td>
</tr>
</table>
</form>
</p>
<?php

return;
?>

<?php
/**
 * $Id: tasks_tab.addedit.trac_ticket.php,v 1.4 2008/05/07 14:07:56 david_iondev Exp $
 * @since 0.3
 * @version 0.5
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @author David Raison <david@ion.lu>
 * @todo		0.5 xmlrpc
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
$canAdd = $perms->checkModule('trac','add');
$canEdit = $perms->checkModule('trac','edit');
$canDelete = $perms->checkModule('trac','delete');

if(!$canAdd){
	print('<p>'.$AppUI->_('You do not have permission to add tickets to this task.').'</p>');
	return;
}

// 1. list attached tickets (num [label], summary [textbox], dustbin, update)
?>
<div style="border:2px solid black;">
<p><strong>Attached Trac Tickets</strong><br/>
<form name="tracTicketAttach" method="post" action="?m=trac&a=ticket_backend">
<input type="hidden" name="task_id" value="<?php echo $task_id ?>"/>
<table><thead><tr><th>Ticket #</th><th>Summary (displayed only if xmlrpc is not available)</th><th colspan="2">Actions</th></tr></thead>
<tbody>
<?php
	if($task_project && $task_id){
		// fetch host and environment
		$env = $tracticket->fetchEnvironments($task_project);
		// fetch ticket numbers
		$tickets = $tracticket->fetchTickets($task_id);
		/** NEW & TODO
		 * 2. Also offer a drop-down box with the environments open tickets to select from
		 */
		foreach($tickets as $ticket){
			$url = '?m=trac&envId='.$env['idenvironment'].'&ticket='.$ticket['fiticket'];
			printf('<tr><td><a href="%1$s">#%2$d</a></td>'
					.'<td><input type="text" class="text" value="%3$s" name="summary[%4$d]" '
					.'maxlength="50" style="width:50em;"/></td>'
					.'<td><button name="changesummary" style="background-color:transparent;border:0;" value="%4$d" '
					.'title="'.$AppUI->_('Click to update this ticket').'">'
					.dPshowImage( './images/icons/stock_attach-16.png', '16', '16',  '' )
					.'</button></td>'
					.'<td><button id="deleteTicket_%4$d" name="deleteticket"'
            		.'value="%4$d" style="background: transparent;'
            		.'border:0;" title="Click to delete this attachment">'
            		.dPshowImage( './images/icons/stock_delete-16.png', '16', '16',  '' )
            		.'</button></td>'
					.'</tr>',$url,$ticket['fiticket'],$ticket['dtsummary'],$ticket['idticket']);
		}
	}
// 2. add an empty row to attach a new ticket (num [textbox 5], summary [textbox 50], attach [button]) 
print('<tr>');
if ($env['dtrpc']) {	
	// fetch all tickets and offer them in a drop-down select box
	$tracrpc = new CTracRPC($env);
	$options = 0;
	foreach($tracrpc->fetchTicketList() as $ticket)
		$options .= sprintf('<option value="%1$d">%1$d</option>',$ticket);
	print('<td>#<select name="ticketnum" class="text" style="width:5em;">'.$options.'</select></td>');
} else {
	print('<td>#<input type="text" class="text" name="ticketnum" maxlength="5" style="width:5em;"/></td>');
}
print('<td><input type="text" class="text" name="newsummary" maxlength="50" style="width:50em;"/></td>');
print('<td><button name="addticket" id="attachButton" style="background-color:transparent;border:0px;" value="yes" title="'.$AppUI->_('Click to attach this ticket').'">');
print(dPshowImage( './images/icons/stock_attach-16.png', '16', '16',  '' ).'</button>');
print('</td></tr></table></form></p></div>');


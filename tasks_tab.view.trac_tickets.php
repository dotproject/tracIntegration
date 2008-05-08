<?php
/**
 * $Id: tasks_tab.view.trac_tickets.php,v 1.4 2008/05/07 14:07:56 david_iondev Exp $
 * @since 0.3
 * @version 0.5
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 * @author David Raison <david@ion.lu>
 * @todo	0.5 xmlrpc
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

?>
<div style="border:2px solid black;">
<p><strong>Attached Trac Tickets</strong><br/>
<table style="border:1px solid black;width:100%"><thead><tr><th>Ticket #</th><th>Summary</th><th>Type</th><th>Priority</th></tr></thead>
<tbody>
<?php
	if($task_project && $task_id){
		// fetch host and environment
		$env = $tracticket->fetchEnvironments($task_project);
				
		// does this env support xmlrpc?
		if ($env['dtrpc']){
			try {
				$txrpc = new CTracRPC($env);
				$tickets = $txrpc->fetchTickets($task_id);
			} catch (Exception $e) {
				$AppUI->setMsg('Remote procedure call failed: '.$e->getMessage());
				$tickets = $tracticket->fetchTickets($task_id);	// get them without xmlrpc data then
			}
		} else
			$tickets = $tracticket->fetchTickets($task_id);
		
		foreach($tickets as $ticket){
			$color = ($color == 'white') ? 'transparent' : 'white';
			$url = '?m=trac&envId='.$env['idenvironment'].'&ticket='.$ticket['fiticket'];
			printf('<tr style="background-color:'.$color.';"><td><a href="%1$s">#%2$d</a></td><td><a href="%1$s">%3$s</a></td><td>%4$s</td><td>%5$s</td>'
					.'</tr>',$url,$ticket['fiticket'],$ticket['dtsummary'],$ticket['type'],$ticket['priority']);
		}
	}
?>
</table>
</form>
</p>
</div>
<?php

return;
?>

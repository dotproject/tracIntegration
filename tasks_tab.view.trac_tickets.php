<?php
/**
 * $Id$
 * @since 0.3
 * @version 0.3
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
<table><thead><tr><th>Ticket #</th><th>Summary</th><th colspan="2">Actions</th></tr></thead>
<tbody>
<?php
	if($task_project && $task_id){
		// fetch host and environment
		$env = $tracticket->fetchEnvironments($task_project);
		// fetch ticket numbers
		$tickets = $tracticket->fetchTickets($task_id);
		foreach($tickets as $ticket){
			$url = '?m=trac&envId='.$env['idenvironment'].'&ticket='.$ticket['fiticket'];
			printf('<tr><td><a href="%1$s">#%2$s</a></td><td>%3$s</td>'
					.'</tr>',$url,$ticket['fiticket'],$ticket['dtsummary'],$ticket['idticket']);
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

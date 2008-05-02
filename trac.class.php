<?php
/**
 * $Id: trac.class.php,v 1.8 2008/05/02 11:59:11 david_iondev Exp $ 
 * This class contains all methods used by the dpTrac module
 *
 * @author David Raison <david@ion.lu>
 * @version 0.4
 * @since 0.1
 * @package TracIntegration
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

class CTracIntegrator {
 //extends CDpObject {
	protected $host;
	protected $environments;

	public function __construct(){
	}

	public function fetchEnvironments($project_id=0){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addQuery('idenvironment, dtenvironment');
		if($project_id)
			$q->addWhere("fiproject = '$project_id'");
		$q->prepare();
		$res = ($project_id) ? $q->loadHash() : $q->loadHashList('idenvironment');
		return($res);
	}

	public function hasTrac($project_id){
		$env = $this->fetchEnvironments($project_id);
		return($env);
	}

	public function deleteEnvironment($id){
		$q = new DBQuery();
		$q->setDelete('trac_environment');
		$q->addWhere('idenvironment = '.$id);
		if (!($q->exec())) {
			return db_error();
		}
		return true;
	}

	public function addEnvironment($name,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addInsert(array('fiproject','dtenvironment'),array($project_id,$name),true);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}
	
	public function updateEnvironment($env,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addUpdate('dtenvironment',$env);
		$q->addWhere('fiproject = '.$project_id);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function fetchHosts($project_id=0){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addQuery('fiproject,idhost,dthost');
		if($project_id)
			$q->addWhere('fiproject = '.$project_id);
		$q->prepare();
		if($project_id)
			return($q->loadHash());
		else
			return($q->loadList());
		$q->clear();
	}

	public function updateHost($id,$url,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addUpdate('dthost',$url);
		$q->addWhere('idhost = '.$id);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function addHost($url,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addInsert(array('fiproject','dthost'),array($project_id,$url),true);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function deleteHost($id){
		$q = new DBQuery();
		$q->setDelete('trac_host');
		$q->addWhere('idhost = '.$id);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function getProjectFromHost($host){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addQuery('fiproject');
		$q->addWhere('idhost = '.$host);
		$q->prepare();
		$res = $q->loadResult();
		$q->clear();
		return($res);
	}

	public function getProjectFromEnvironment($env){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addQuery('fiproject');
		$q->addWhere('idenvironment = '.$env);
		$q->prepare();
		$res = $q->loadResult();
		$q->clear();
		return($res);
	}

	public function getHostFromEnvironment($env){
		$q = new DBQuery();
		$q->addTable('trac_environment','e');
		$q->addJoin('trac_host', 'h', 'h.fiproject = e.fiproject');
		$q->addQuery('h.dthost');
		$q->addWhere('e.idenvironment = '.$env);
		$q->prepare();
		$res = $q->loadResult();
		$q->clear();
		return($res);
	}

	public function getEnvironmentsFromHost($host){
		$q = new DBQuery();
		$q->addTable('trac_environment','e');
		$q->addJoin('trac_host', 'h', 'h.fiproject = e.fiproject');
		$q->addQuery('e.idenvironment,e.dtenvironment');
		$q->addWhere('h.idhost = '.$host);
		$q->prepare();
		$res = $q->loadList();
		$q->clear();
		return($res);
	}
}

class CTracTicket extends CTracIntegrator{

	protected $ticket;

	public function __contruct(){
	}

	public function fetchTickets($task_id){
		$q = new DBQuery();
		$q->addTable('trac_ticket');
		$q->addQuery('idticket,fiticket,dtsummary');
		$q->addWhere('fitask = '.$task_id);
		$q->prepare();
		$res = $q->loadList();
		$q->clear();
		return($res);
	}

	public function addTicket($num,$summary,$task){
		$q = new DBQuery;
		$q->addTable('trac_ticket');
		$q->addInsert(array('fiticket','dtsummary','fitask'),array($num,$summary,$task),true);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function updateTicket($id,$summary){
		$q = new DBQuery();
		$q->addTable('trac_ticket');
		$q->addUpdate('dtsummary',$summary);
		$q->addWhere('idticket = '.$id);
		if (!($q->exec())) {	// dunno if this is ideal, but this is how it's done elsewhere (tasks.class.php)
			return db_error();
		}
		$q->clear();
		return true;
	}

	public function deleteTicket($id){
		$q = new DBQuery();
		$q->setDelete('trac_ticket');
		$q->addWhere('idticket = '.$id);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

}
?>

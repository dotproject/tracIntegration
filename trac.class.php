<?php
/**
 * $Id$ 
 * This class contains all methods used by the dpTrac module
 *
 * @author David Raison <david@ion.lu>
 * @version 0.3
 * @since 0.1
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

class CTracIntegrator{

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
		$q->exec();
		// how can I check for success?
		return true;
	}

	public function addEnvironment($name,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addInsert(array('fiproject','dtenvironment'),array($project_id,$name),true);
		$q->exec();
		$q->clear();
		// how can I check for success?
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
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}

	public function addHost($url,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addInsert(array('fiproject','dthost'),array($project_id,$url),true);
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}

	public function deleteHost($id){
		$q = new DBQuery();
		$q->setDelete('trac_host');
		$q->addWhere('idhost = '.$id);
		$q->exec();
		$q->clear();
		// how can I check for success?
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
		// @TODO 0.3 : write joins!
		$project = $this->getProjectFromEnvironment($env);
		$host = $this->fetchHosts($project);
		return($host['dthost']);
	}

	public function getEnvironmentsFromHost($host){
		// @TODO 0.3 : write joins!
		$project = $this->getProjectFromHost($host);
		$environments = $this->fetchEnvironments($project);
		return($environments);
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
		$q->exec();
		$q->clear();
		return true;
	}

	public function updateTicket($id,$summary){
		$q = new DBQuery();
		$q->addTable('trac_ticket');
		$q->addUpdate('dtsummary',$summary);
		$q->addWhere('idticket = '.$id);
		$q->exec();
		$q->clear();
		return true;
	}

	public function deleteTicket($id){
		$q = new DBQuery();
		$q->setDelete('trac_ticket');
		$q->addWhere('idticket = '.$id);
		$q->exec();
		$q->clear();
		return true;
	}

}
?>

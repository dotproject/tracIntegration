<?php
/**
 * $Id: trac.class.php,v 1.9 2008/05/02 14:10:37 david_iondev Exp $ 
 * This class contains all methods used by the dpTrac module
 *
 * @author David Raison <david@ion.lu>
 * @version 0.5
 * @since 0.1
 * @package TracIntegration
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

/**
 * This class is needed because dotproject doesn't seem to have a way of retrieving the insertID
 */
class TDBQuery extends DBQuery {
	public function getInsertId(){
		global $db;
		return($db->Insert_ID());
	}
}

/**
 * Main Trac class
 */
class CTracIntegrator {
 //extends CDpObject {
	protected $host;
	protected $environments;

	/**
	 * fetches a list of environments from the database.
	 * @param $project_id If this parameter is not null, we only query data on that specific environment, else we query all environments available
	 * @return a dictionary (associative array) with data on a specific environment if $project_id was set, else a dictionary indexed by environment ids
	 */
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

	/**
	 * Checks whether a given project has a trac environment
	 * Basically this is just a wrapper for fetchEnvironments where $project_id is always set
	 * @param $project_id The id of the project we need to test.
	 * @return Data on the project's environment if found, false if no environment is associated to this project
	 */
	public function hasTrac($project_id){
		$env = $this->fetchEnvironments($project_id);
		return($env);
	}

	/**
	 * Delete an environment
	 * @param $id The id of the environment to be deleted
	 * @return A boolean value depending on the success of the operation
	 */
	public function deleteEnvironment($id){
		$q = new DBQuery();
		$q->setDelete('trac_environment');
		$q->addWhere('idenvironment = '.$id);
		if (!($q->exec())) {
			return db_error();
		}
		return true;
	}

	/**
	 * add an environment
	 * @param $env The name of the trac environment to be added.
	 * @param $project_id The id of the project this new environment is to be associated with.
	 * @return A boolean value depending on the success of the operation
	 */
	public function addEnvironment($env,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addInsert(array('fiproject','dtenvironment'),array($project_id,$env),true);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}
	
	/**
	 * update an environment
	 * @param $env The name of the trac environment to be updated.
	 * @param $project_id The id of the project this new environment is associated with.
	 * @return A boolean value depending on the success of the operation
	 */
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

	/**
	 * Fetch a list of hosts or the host associated to a specific project
	 * @param $project_id If set, return only the host associated with that project
	 * @return Depending on the value of project_id, an array of hosts or a single host array
	 */
	public function fetchHosts($project_id=0){
		$q = new DBQuery();
		$q->addTable('trac_host','h');
		$q->addJoin('trac_host2project','h2p','h.idhost = h2p.fihost');
		$q->addQuery('h2p.fiproject,h.idhost,h.dthost');
		if($project_id)
			$q->addWhere('h2p.fiproject = '.$project_id);
		$q->prepare();
		$res = ($project_id) ? $q->loadHash() : $q->loadHashList('idhost');
		$q->clear();
		return $res;
	}

	/**
	 * update a host
	 * @param $id The host's id
	 * @param $url The host's FQDN
	 * @return A boolean value depending on the success of the operation
	 */
	public function updateHost($id,$url){
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

	/** 
	 * Add a host to the host table
	 * @param $url The host's FQDN
	 * @return The insertID (needed for the linking table)
	 */
	public function addHost($url){
		$q = new TDBQuery();
		$q->addTable('trac_host');
		$q->addInsert('dthost',$url);
		if (!$q->exec()) {
			return db_error();
		}
		$iid = $q->getInsertId();
		$q->clear();
		return($iid);
	}

	/**
	 * Delete a host from the host table
	 * @param $id The ID of the host that is to be deleted
	 */
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
	 
	/**
	 * Add a host<-->project link to the host2project table
	 * @param $host_id
	 * @param $project_id
	 * @return bool
	 */
	public function addHostLink($host_id,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_host2project');
		$q->addInsert(array('fihost','fiproject'),array($host_id,$project_id),true);
		if (!$q->exec()) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	/**
	 * Remove a host <---> project link from the host2project table
	 * @param $host_id
	 */
	public function deleteHostLink($host_id){
		$q = new DBQuery();
		$q->setDelete('trac_host2project');
		$q->addWhere('fihost = '.$host_id);
		if (!($q->exec())) {
			return db_error();
		}
		$q->clear();
		return true;
	}

	/**
	 * Get the project_id corresponding to a given host 
	 * @param $host The host for which we will select the corresponding project
	 * @return A project id
	 */
	public function getProjectFromHost($host){
		$q = new DBQuery();
		$q->addTable('trac_host','h');
		$q->addJoin('trac_host2project','h2p','h.idhost = h2p.fihost');
		$q->addQuery('h2p.fiproject');
		$q->addWhere('h.idhost = '.$host);
		$q->prepare();
		$res = $q->loadResult();
		$q->clear();
		return($res);
	}

	/**
	 * Get the project_id from a specific environment
	 * @param $env The environment to fetch the project_id for
	 * @return The associated project_id
	 */
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

	/**
	 * Get the host that hosts the given environment
	 * @param $env The environment for which we want to find out its host
	 * @return The url of the host that hosts the environment $env
	 */
	public function getHostFromEnvironment($env){
		$q = new DBQuery();
		$q->addTable('trac_environment','e');
		$q->addJoin('trac_host2project', 'h2p', 'h2p.fiproject = e.fiproject');
		$q->addJoin('trac_host', 'h', 'h2p.fihost = h.idhost');
		$q->addQuery('h.dthost');
		$q->addWhere('e.idenvironment = '.$env);
		$q->prepare();
		$res = $q->loadResult();
		$q->clear();
		return($res);
	}

	/**
	 * Get Environments associated with given host
	 * @param $host The host for which environments ought to be looked up
	 * @return An array of environments which live under the given host
	 */
	public function getEnvironmentsFromHost($host){
		$q = new DBQuery();
		$q->addTable('trac_environment','e');
		$q->addJoin('trac_host2project', 'h2p', 'h2p.fiproject = e.fiproject');
		$q->addJoin('trac_host', 'h', 'h2p.fihost = h.idhost');
		$q->addQuery('e.idenvironment,e.dtenvironment');
		$q->addWhere('h.idhost = '.$host);
		$q->prepare();
		$res = $q->loadList();
		$q->clear();
		return($res);
	}
}

/** 
 * This class contains methods that work on tickets
 * It extends CTracIntegrator
 */
class CTracTicket extends CTracIntegrator{
	

	protected $ticket;

	/**
	 * Fetches tickets associated with a dotproject task
	 * @param $task_id The id of the task to which this ticket ought to be attached
	 * @return An array of tickets associated with the given task
	 */
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

	/**
	 * Attach a new ticket to a given task
	 * @param $num The ticket number as saved in the Trac Environment
	 * @param $summary A summary of the ticket
	 * @param $task The task's id with which this ticket ought to be associated
	 * @return bool
	 */
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

	/**
	 * Update a ticket, setting a new summary
	 * @param $id The ticket's id
	 * @param $summary The new summary to be displayed
	 * @return bool
	 */
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

	/**
	 * Deletes a ticket attachment
	 * @param $id The id of the attached ticket
	 * @return bool
	 */
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

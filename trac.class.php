<?php
/**
 * This class contains all methods used by the dpTrac module
 *
 * @author David Raison <david@ion.lu>
 * @version 0.3-rc2
 * @since 0.1
 * @package dpTrac
 * @copyright ION Development (www.iongroup.lu)
 * @license http://www.gnu.org/copyleft/gpl.html GPL License 2 or later
 */

class CTracProject{

	public function __construct(){
	}

	public function fetchEnvironments($project_id=0){
		$q = new DBQuery();
		$q->addTable('trac_environment');
		$q->addQuery('idenvironment, dtenvironment');
		if($project_id)
			$q->addWhere("fiproject = '$project_id'");
		$q->prepare();
		return($q->loadList());	// loadHash???
		$q->clear();
	}

	public function deleteEnvironment($id){
		$q = new DBQuery();
		$q->setDelete('trac_environment');
		$q->addWhere('idenvironment = '.$id);
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}

	public function deleteProjectEnvironments($project_id){
		$envs = $this->fetchEnvironments($project_id);
		foreach($envs as $env)
			$this->deleteEnvironment($env['idenvironment']);
		// return value? (see deleteEnvironment())
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

	public function fetchHost($project_id=0){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addQuery('dthost');
		if($project_id)
			$q->addWhere('fiproject = '.$project_id);
		$q->prepare();
		if($project_id)
			return($q->loadResult());
		else
			return($q->loadList());
		$q->clear();
	}

	public function updateHost($url,$project_id){
		$q = new DBQuery();
		$q->addTable('trac_host');
		$q->addUpdate('dtvalue',$url);
		$q->addWhere("dtkey = 'url'");
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
}

?>

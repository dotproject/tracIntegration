<?php

class CTracProj{

	public function __construct(){
	}

	public function fetchEnvironments(){
		$q = new DBQuery();
		$q->addTable('trac_config');
		$q->addQuery('dtvalue');
		$q->addWhere("dtkey = 'env'");
		$q->prepare();
		return($q->loadList());
		$q->clear();
	}

	public function deleteEnvironment($name){
		$q = new DBQuery();
		$q->setDelete('trac_config');
		$q->addWhere('dtkey = "env" AND dtvalue = "'.$name.'"');
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}

	public function addEnvironment($name){
		$q = new DBQuery();
		$q->addTable('trac_config');
		$q->addInsert(array('dtkey','dtvalue'),array('env',$name),true);
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}

	public function getURL(){
		$q = new DBQuery();
		$q->addTable('trac_config');
		$q->addQuery('dtvalue');
		$q->addWhere("dtkey = 'url'");
		$q->prepare();
		return($q->loadResult());
		$q->clear();
	}

	public function setURL($url){
		$q = new DBQuery();
		$q->addTable('trac_config');
		$q->addUpdate('dtvalue',$url);
		$q->addWhere("dtkey = 'url'");
		$q->exec();
		$q->clear();
		// how can I check for success?
		return true;
	}
}

?>

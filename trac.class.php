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

class CTracIntegrator{

	public function __construct(){
	}

	public function displayConfigForm($project_id=0){
		$hosts = $this->fetchHosts();
		$out = '<p>If there is a trac environment available for this project, please select or enter the URL 
				of its HOST and the name of the environment below:</p>
			   <form action="?m=trac&a=addEnv" method="post" name="setHostEnv">
				<input type="hidden" name="project_id" value="'.$project_id.'"/>
			   <table style="width: auto;">
			   <tbody>';
   	if(!empty($hosts)){
			$out .= '<tr><td><label for="existURL">Trac HOST</label></td>
				   <td><select name="existURL">
			   	<option value="0">Pick a host</option>';
	      // generate options
   	   foreach($hosts as $host){
					$selected = ($host['fiproject'] == $project_id) ? 'selected' : 'false';
	            $out .= sprintf('<option value="%d" selected="%s">%s</option>',$host['idhost'],$host['fiproject'],$host['dthost']);
			}
	      $out .= '</select></td></tr>';
      }
   	$out .= '<tr><td><label for="newurl">Enter a new host</label></td>
				   <td><input id="tracurl" name="newurl" type="text" size="40" value="'.$url.'"/>
				   </td></tr><tr><td><label for="newenv">Trac Environment</label></td>
				   <td><input id="tracenv" name="newenv" type="text" size="40" value="'.$env.'"/></td></tr>
   				<tr><td colspan="2" style="text-align:right;"><button name="submit" value="saveTracConf" title="Click to Save">Save</button></td></tr>
				   </tbody></table></form>';
		return($out);
	}

	public function showLink($project_id){
		$env = $this->fetchEnvironments($project_id);
		return('<a href="?m=trac&envId='.$env['idenvironment'].'">Go to <strong>'.$env['dtenvironment'].'</strong> trac environment.</a>');
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
?>

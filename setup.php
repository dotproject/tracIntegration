<?php
/*
 * Name:      dpTrac
 * Directory: trac
 * Version:   0.4
 * Class:     user
 * UI Name:   dpTrac
 * UI Icon:	trac_logo.png
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dpTrac';
$config['mod_version'] = '0.4';
$config['mod_directory'] = 'trac';
$config['mod_setup_class'] = 'CSetupTrac';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Trac';
$config['mod_ui_icon'] = 'trac_logo.png';
$config['mod_description'] = 'Integrating trac into dotproject';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupTrac {   

	public function install() {
		$q = new DBQuery;
		$this->_runInstallTasks($q);
		return db_error();
	}

	/**
	 * written to easily upgrade old versions
	 */
	private function _runInstallTasks($q){
		// trac_environment
		$sql = ' (  `idenvironment` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
				 	`fiproject` MEDIUMINT UNSIGNED NOT NULL ,
				 	`dtenvironment` VARCHAR( 60 ) NOT NULL ,
				 	PRIMARY KEY ( `idenvironment` ) ,
			 		INDEX ( `fiproject` )
			 		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		$q->createTable('trac_environment');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();

		// trac_host
		$sql = ' (  `idhost` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
				 	`fiproject` MEDIUMINT(8) UNSIGNED NOT NULL ,
				 	`dthost` VARCHAR( 60 ) NOT NULL ,
				 	PRIMARY KEY ( `idhost` ) ,
			 		INDEX ( `fiproject` )
			 		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		$q->createTable('trac_host');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();

		// trac_ticket
		$sql = ' (  `idticket` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
				 	`fiticket` MEDIUMINT(8) UNSIGNED NOT NULL ,
					`dtsummary` VARCHAR(50) NOT NULL ,
				 	`fitask` MEDIUMINT(8) UNSIGNED NOT NULL ,
				 	PRIMARY KEY ( `idticket` ) ,
			 		INDEX ( `fitask` )
			 		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		$q->createTable('trac_ticket');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
	}
	
	public function remove() {
		$q = new DBQuery;
		$q->dropTable('trac_host');
		$q->exec();
		$q->clear();
		$q->dropTable('trac_environment');
		$q->exec();
		$q->clear();
		$q->dropTable('trac_ticket');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	public function upgrade($old_version) {
		switch ($old_version) {
			case '0.1':
				// since we had no db in v0.1, do a simple install, then break
				return($this->install());
			break;
			case '0.2':
			case '0.3-rc1':
				// 0.2 and 0.3-rc1 had an entirely different db structure.
				$q = new DBQuery;
				$q->dropTable('trac_config');
				$q->exec();
				$q->clear();
				$this->_runInstallTasks($q);
			case '0.3-rc2':
				// make sure we have a database connection
				$q = (!is_object($q)) ? new DBQuery : $q;
				// trac_ticket was added
				$sql = ' (  `idticket` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
				 	`fiticket` MEDIUMINT(8) UNSIGNED NOT NULL ,
					`dtsummary` VARCHAR(50) NOT NULL ,
				 	`fitask` MEDIUMINT(8) UNSIGNED NOT NULL ,
				 	PRIMARY KEY ( `idticket` ) ,
			 		INDEX ( `fitask` )
			 		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
				$q->createTable('trac_ticket');
				$q->createDefinition($sql);
				$q->exec();
				$q->clear();
			break;
		}
		return true;
	}
}
?>

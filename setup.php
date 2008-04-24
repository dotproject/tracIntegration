<?php
/*
 * Name:      dpTrac
 * Directory: trac
 * Version:   0.3-rc2
 * Class:     user
 * UI Name:   dpTrac
 * UI Icon:	trac_logo.png
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dpTrac';
$config['mod_version'] = '0.3-rc2';
$config['mod_directory'] = 'trac';
$config['mod_setup_class'] = 'CSetupTrac';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Trac';
$config['mod_ui_icon'] = 'trac_logo.png';
$config['mod_description'] = 'A module for tracking changes';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupTrac {   

	public function install() {
		$q = new DBQuery;

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
		return db_error();
	}
	
	public function remove() {
		$q = new DBQuery;
		$q->dropTable('trac_host');
		$q->exec();
		$q->clear();
		$q->dropTable('trac_environment');
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
				// Remove it first, then do a simple install
				$q = new DBQuery;
				$q->dropTable('trac_config');
				$q->exec();
				$q->clear();
				return($this->install());
			break;
		}
	}
}

?>

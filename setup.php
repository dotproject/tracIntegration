<?php
/*
 * Name:      dpTrac
 * Directory: trac
 * Version:   0.3-rc1
 * Class:     user
 * UI Name:   dpTrac
 * UI Icon:	trac_logo.png
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dpTrac';
$config['mod_version'] = '0.3-rc1';
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
		$sql = ' (  `idconfig` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
			 `dtkey` VARCHAR( 20 ) NOT NULL ,
			 `dtvalue` VARCHAR( 50 ) NOT NULL ,
			 PRIMARY KEY ( `idconfig` ) ,
			 INDEX ( `dtkey` )
			 ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
		$q = new DBQuery;
		$q->createTable('trac_config');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		$q->addTable('trac_config');
		$q->addInsert('dtkey','url');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	public function remove() {
		$q = new DBQuery;
		$q->dropTable('trac_config');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	public function upgrade($old_version) {
		switch ($old_version) {
			case '0.1':
				// we had no DB in v0.1
				$this->install();
			case '0.2':
				// no upgrades, everything worked fine ;)
				return(true);
			break;
		}
	}
}

?>

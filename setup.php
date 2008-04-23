<?php
/*
 * Name:      dpTrac
 * Directory: trac
 * Version:   0.2
 * Class:     user
 * UI Name:   dpTrac
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'dpTrac';
$config['mod_version'] = '0.2';
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

	function install() {
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
	
	function remove() {
		$q = new DBQuery;
		$q->dropTable('trac_config');
		$q->exec();
		$q->clear();
		return db_error();
	}
	
	function upgrade($old_version) {
	/*
		$q = new DBQuery;
		switch ($old_version) {
			case '0.1':
				$q->alterTable('trac_config');
				$q->addField('history_table', 'varchar(15) NOT NULL default \'\'');
				$q->dropField('history_module');
				$q->exec();
				$q->clear();
				$q->addIndex('index_history_item', '(history_item)');
				$q->exec();
				$q->clear();
		}
		return db_error();
		*/
	}
}

?>

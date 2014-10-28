<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Profile Pic Installation
abstract class Install extends Installation {
	
	
/****** Plugin Variables ******/
	
	// These addon plugins will be selected for installation during the "addon" installation process:
	public static array <str, bool> $addonPlugins = array(	// <str:bool>
	//	"ExamplePlugin"		=> true
	//,	"AnotherPlugin"		=> true
	);
	
	
/****** App-Specific Installation Processes ******/
	public static function setup(
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	{
		// Create the avatar table, which is used in multiple plugins
		Database::exec("
		CREATE TABLE IF NOT EXISTS `avatars`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`title`					varchar(22)					NOT NULL	DEFAULT '',
			
			UNIQUE (`uni_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY (`uni_id`) PARTITIONS 5;
		");
		
		// Make sure the newly installed tables exist
		return DatabaseAdmin::columnsExist("avatars", array("uni_id", "title"));
	}
}
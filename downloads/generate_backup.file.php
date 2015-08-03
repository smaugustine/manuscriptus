<?php
/*
 * Database Settings
 */
function db_connect(){
	if(!file_exists('../config.ini')) header("Location: ".'../setup.php?error=config');
	$db_settings = parse_ini_file("../config.ini");
	$username	 = $db_settings['username'];
	$password	 = $db_settings['password'];
	$host		 = $db_settings['host'];
	$db_name     = $db_settings['db_name'];
	define('MS_DIR', $db_settings['directory']);
	
	static $db_connect = false;
	if($db_connect) die('db_connect can only be called once.');
	else $db_connect = true;
	
	//Before establishing ezSQL connection, check if database info is correct
	//and that manuscriptus database exists
	$link = mysqli_connect($host, $username, $password, $db_name);
	if(!$link) header("Location: ".'setup.php?error=db');
	mysqli_close($link);
	
	//Establish ezSQL connection
	global $db;
	$db = new ezSQL_mysqli($username, $password, $db_name, $host);
}

###Database connection###
require_once('../ez_sql_core.php'); //ezSQL core needed for all ezSQL libraries
require_once('../ez_sql_mysqli.php'); //ezSQL for MySQLi

db_connect();
$db->query("SET NAMES 'UTF8'");
$db->hide_errors();
$counts = $db->get_row("SELECT COUNT(*) AS corpus_count,(SELECT COUNT(*) FROM manuscripts) AS ms_count FROM corpuses");
if(!$counts) if(!$link) header("Location: ".'setup.php?error=db');

$db_name = $db->get_row("SELECT DATABASE() AS db_name");
$db_name = $db_name->db_name;

header("Content-Disposition: attachment; filename=\"" . basename($db_name.'-backup-'.time().'.sql')."\"");
header("Content-Type: application/force-download");
header("Content-Length: " . filesize($db_name.'-backup-'.time().'.sql'));
header("Connection: close");

	#Get a list of tables
	$db_tables = $db->get_results("SHOW TABLES");
	foreach($db_tables as $table){
		foreach($table as $name => $value){
			$tables[] = $value;
		}
	}
	
	#For each table, output the table creation code
	foreach($tables as $table){
		$sql_code = $db->get_row("SHOW CREATE TABLE $table");
		$sql_code = $sql_code->{'Create Table'};
		$sql_code = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $sql_code);
		echo "$sql_code;";
		echo "\n\n";
	}
	
	#For each table, output the insert codes for each row
	foreach($tables as $table){
		echo "INSERT INTO `$table` (";
		$table_cols = $db->get_row("SELECT * FROM $table LIMIT 1");
		$init = 1;
		foreach($table_cols as $field => $data){
			if(!$init) echo ", ";
			echo "`$field`";
			$init = 0;
		}
		echo ") VALUES\n";
		$table_data = $db->get_results("SELECT * FROM $table");
		$init = 1;
		foreach($table_data as $row){
			if(!$init) echo ",\n";
			echo "(";
			$jnit = 1;
			foreach($row as $field => $data){
				if(!$jnit) echo ", ";
				echo "'$data'";
				$jnit = 0;
			}
			echo ")";
			$init = 0;
		}
		echo ";";
		echo "\n\n";
	}
exit();
?>
<?php
###Create backup
if(argument(0) == 'create'):
?>
<ul class="breadcrumb">
	<li><a href="<?=THE_BASE_URL?>">Manuscriptus: Codex</a></li>
	<li class="active">Create Backup</li>
</ul>
<h1 class="page-header" style="text-align: left;">Create Backup</h1>

<p class="lead">This page allows you to create an SQL backup of the database. This backup file can be used by going to "File > Restore from Backup" and uploading the file. Do not close this browser window until the file has finished downloading.</p>

<p class="lead" style="text-align: center;"><a class="btn btn-primary" href="<?=THE_BASE_URL?>downloads/generate_backup.file.php"><span class="glyphicon glyphicon-download"></span> Download Backup File</a></p>

<p class="lead">Alternatively, the SQL code for the backup can be copied below:</p>
<pre style="max-height: 200px;">
<?php	
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
?>
</pre>
<?php
###Restore backup
elseif(argument(0) == 'restore'):
?>
<ul class="breadcrumb">
	<li><a href="<?=THE_BASE_URL?>">Manuscriptus: Codex</a></li>
	<li class="active">Create Backup</li>
</ul>
<h1 class="page-header" style="text-align: left;">Restore from Backup</h1>
<?php endif; ?>
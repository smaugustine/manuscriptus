<?php
/*
 * setup.php
 * Setup PHP File
 */

function step($status, $title, $text = ''){
	if($status == 'success' || $status == 'warning' || $status == 'danger'){
		if($status == 'success') $icon = 'ok';
		if($status == 'warning') $icon = 'alert';
		if($status == 'danger') $icon = 'remove';
		
		return '<div class="alert alert-'.$status.'"><p class="lead"><span class="glyphicon glyphicon-'.$icon.'"></span>'.$title.'</p><p>'.$text.'</p></div>';
	}
}

function pretty_error($link){
	if(!empty(mysqli_error($link))) return mysqli_error($link).'.';
	else return '';
}

function create_config_file($username, $password, $host, $db_name){
	$config_file = fopen('config.ini', "w");
	if (!is_resource($config_file)) {
		die("Unable to access ini file");
	}
	
	fwrite($config_file, sprintf("%s = %s\n", 'username', $username));
	fwrite($config_file, sprintf("%s = %s\n", 'password', $password));
	fwrite($config_file, sprintf("%s = %s\n", 'host', $host));
	fwrite($config_file, sprintf("%s = %s\n", 'db_name', $db_name));
	fwrite($config_file, sprintf("%s = %s\n", 'directory', basename(getcwd())));
	
	fclose($config_file);
	
	return true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Setup Assistant</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style type="text/css">
	div.alert p.lead{
		padding-left: 0;
	}
	
	div.alert p{
		padding-left: 37px;
	}
	
	div.alert span.glyphicon{
		margin-right: 15px;
	}
	
	p:empty{
		display: none;
	}
	</style>
</head>
<body>
<div class="container-fluid main col-sm-offset-3 col-sm-6">
<h1>Setup Assistant</h1>
<?php
if(isset($_GET['error']) && $_GET['error'] == 'config') echo '<div class="alert alert-danger">No config.ini file was found. You may create one using the setup assistant. This will not delete any content in the database.</div>';
elseif(isset($_GET['error']) && $_GET['error'] == 'db') echo '<div class="alert alert-danger">Unable to connect to the database using the settings in config.ini. You may create a new config.ini using the setup assistant. This will not delete any content in the database.</div>';

if(isset($_GET['host']) && isset($_GET['username']) && isset($_GET['password'])):
	$link = mysqli_connect($_GET['host'], $_GET['username'], $_GET['password']);
	if($link) echo step('success', 'Establish MySQL Connection');
	else echo step('danger', 'Establish MySQL Connection', 'Please check that you have provided the correct host, username, and password for the MySQL database.<br><tt>'.mysqli_connect_error().'.</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-danger">Retry</a> <a href="setup.php" class="btn btn-danger">Start Over</a>');
	
	if(isset($_GET['prefix'])){
		$db_name = mysqli_real_escape_string($link, $_GET['prefix']);
		if(!empty($db_name)) $db_name .= '_manuscriptus';
		else $db_name = 'manuscriptus';
	}
	else $db_name = 'manuscriptus';
	$query = mysqli_query($link, 'CREATE DATABASE '.$db_name);
	if($query) echo step('success', 'Create New Database');
	elseif(mysqli_errno($link) == 1007) echo step('warning', 'Create New Database', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-warning">Retry</a>');
	else echo step('danger', 'Create New Database', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?" class="btn btn-danger">Retry</a>');
	
	$link = mysqli_connect($_GET['host'], $_GET['username'], $_GET['password'], $db_name);
	if($link) echo step('success', 'Connect to Database');
	else echo step('danger', 'Connect to Database', '<tt>'.mysqli_connect_error().'.</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-danger">Retry</a>');

	mysqli_query($link, "SET NAMES 'UTF8'");
	$query = mysqli_query($link, 'CREATE TABLE corpuses (id varchar(30) PRIMARY KEY, name varchar(60) CHARACTER SET utf8 NOT NULL, description text CHARACTER SET utf8 NOT NULL)');
	if($query) echo step('success', 'Create New Table for Corpora');
	elseif(mysqli_errno($link) == 1050) echo step('warning', 'Create New Table for Corpora', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-warning">Retry</a>');
	else echo step('danger', 'Create New Table for Corpora', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-danger">Retry</a>');
	
	$query = mysqli_query($link, 'CREATE TABLE manuscripts (id varchar(30) PRIMARY KEY, corpus varchar(30), name varchar(60) CHARACTER SET utf8 NOT NULL, siglum varchar(4) CHARACTER SET utf8 NOT NULL, frange varchar(20) CHARACTER SET utf8 NOT NULL, date varchar(60) CHARACTER SET utf8 NOT NULL, link varchar(60) CHARACTER SET utf8 NOT NULL, description text CHARACTER SET utf8 NOT NULL, INDEX corpus (corpus ASC))');
	if($query) echo step('success', 'Create New Table for Manuscripts');
	elseif(mysqli_errno($link) == 1050) echo step('warning', 'Create New Table for Manuscripts', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-warning">Retry</a>');
	else echo step('danger', 'Create New Table for Manuscripts', '<tt>'.pretty_error($link).'</tt></p><p class="text-right"><a href="setup.php?host='.$_GET['host'].'&username='.$_GET['username'].'&password='.$_GET['password'].'&prefix='.$_GET['prefix'].'" class="btn btn-danger">Retry</a>');
	
	if(create_config_file($_GET['username'], $_GET['password'], $_GET['host'], $db_name)) echo step('success', 'Create Config File (<tt>config.ini</tt>)');
	else echo step('danger', 'Create Config File (<tt>config.ini</tt>)');
?>
<div style="text-align: center;">
<a class="btn btn-primary" href="./">Go to Manuscriptus: Codex <span class="glyphicon glyphicon-arrow-right"></span></a>
</div>
<?php
else: 
	//determine if setup has already been run
	//display alert that multiple dbs may be created
?>
<form method="get" action="setup.php">
	<div class="form-group">
		<label for="host">MySQL Host</label>
		<input type="text" class="form-control" id="host" name="host" autocomplete="off" placeholder="MySQL Host" value="localhost" required>
	</div>
	<div class="form-group">
		<label for="username">MySQL User Name</label>
		<input type="text" class="form-control" id="username" name="username" autocomplete="off" placeholder="MySQL User Name" required>
	</div>
	<div class="form-group">
		<label for="password">MySQL Password</label>
		<input type="text" class="form-control" id="password" name="password" autocomplete="off" placeholder="MySQL Password" required>
	</div>
	<div class="form-group">
		<label for="prefix">Database Prefix (Optional)</label>
		<p>If you are planning to use multiple sites running Manuscriptus: Codex on the same host or already have a database named "manuscriptus", enter a prefix for the database name to avoid conflicts. Max length: 5 characters.</p>
		<input type="text" class="form-control" id="prefix" name="prefix" autocomplete="off" placeholder="Prefix" maxlength="5">
	</div>
	<div class="form-group" style="text-align: center;">
		<button type="submit" class="btn btn-primary">Begin Setup</button>
		<button type="reset" class="btn btn-warning">Reset</button>
</form>
<?php endif; ?>
</div>
</body>
</html>
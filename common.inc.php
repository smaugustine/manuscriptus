<?php
/*
 * common.inc.php
 * Common PHP File
 * Contains functions common to all pages.
 * Handles database connection, includes plugins,
 * and contains function declarations.
 * This is the only file (besides plugins) that is included
 * before output, so all header() redirects must occur here.
 */
 
/*
 * Database Settings
 */
function db_connect(){
	$username	= 'root'; 		//Username for MySQL database (e.g. 'root')
	$password	= 'root';		//Username for MySQL database (e.g. 'root')
	$host		= 'localhost';	//Host for MySQL database (if unknown, use 'localhost')
	
	static $db_connect = false;
	if($db_connect) die('db_connect can only be called once.');
	else $db_connect = true;
	
	//Before establishing ezSQL connection, check if database info is correct
	//and that manuscriptus database exists (and if not, create it)
	$link = mysqli_connect($host, $username,$password);
	if(!$link) die('Fatal error: could not connect to database. Check database information.');
	mysqli_query($link, 'CREATE DATABASE IF NOT EXISTS manuscriptus');
	$link = mysqli_connect($host, $username,$password, 'manuscriptus');
	if(!$link) die('Fatal error: could not connect to database. Check database information.');
	mysqli_close($link);
	
	//Establish ezSQL connection
	global $db;
	$db = new ezSQL_mysqli($username,$password,'manuscriptus',$host);
}

define('MP_VERSION', 'v0.4.2');
header("Content-Type: text/html; charset=UTF-8");

//THE_BASE_URL: used to ensure that Manuscriptus Pro
//links work in subdirectories
$manuscriptus_dir = 'manuscriptus-pro';
define('THE_BASE_URL', strstr($_SERVER['REQUEST_URI'], $manuscriptus_dir, true)."$manuscriptus_dir/");

//Session is mostly used for sending info (e.g. alerts) without using POST or GET.
//Note that session is closed at the end of toolbar.php,
//so all session variables must be used or preserved before then.
session_start();

//Check if short echo tag is useable
//short echo tags are enabled by
//default in PHP >= 5.4
//and included in short_open_tag in PHP < 5.4
if(version_compare(phpversion(),'5.4','lt') && !ini_get('short_open_tag')) die('Fatal error: short_open_tag must be enabled in php.ini if the PHP version is less than 5.4.');

###Database connection###
require_once('ez_sql_core.php'); //ezSQL core needed for all ezSQL libraries
require_once('ez_sql_mysqli.php'); //ezSQL for MySQLi

db_connect();
$db->query("SET NAMES 'UTF8'");
$db->query("CREATE TABLE IF NOT EXISTS corpuses (id varchar(30) PRIMARY KEY, name varchar(60) CHARACTER SET utf8 NOT NULL, description text CHARACTER SET utf8 NOT NULL)");
$db->query("CREATE TABLE IF NOT EXISTS manuscripts (id varchar(30) PRIMARY KEY, corpus varchar(30), name varchar(60) CHARACTER SET utf8 NOT NULL, siglum varchar(4) CHARACTER SET utf8 NOT NULL, frange varchar(20) CHARACTER SET utf8 NOT NULL, date varchar(60) CHARACTER SET utf8 NOT NULL, link varchar(60) CHARACTER SET utf8 NOT NULL, description text CHARACTER SET utf8 NOT NULL, INDEX corpus (corpus ASC))");
$db->hide_errors();
$counts = $db->get_row("SELECT COUNT(*) AS corpus_count,(SELECT COUNT(*) FROM manuscripts) AS ms_count FROM corpuses");
if(!$counts) if(!$link) die('Fatal error: could not connect to required tables. Check database information.');

/*
 * Function Declarations
 */
//Returns Bootstrap inputs
function new_input($type, $id, $title, $val, $helptext = false, $attributes = false, $placeholder = false, $addon = false, $maxlength = 60){
	if(!$placeholder) $placeholder = $title;
	$return = '<input type="'.$type.'" class="form-control" id="'.$id.'" name="'.$id.'" autocomplete="off" placeholder="'.$placeholder.'" maxlength="'.$maxlength.'"';
	if($attributes) $return .= " $attributes";
	if(is_string($val) || is_int($val)) $return .= ' value="'.$val.'"';
	$return .= ">\n";
	if($helptext) $return .= '<span class="input-group-addon"><span tabindex="0" class="glyphicon glyphicon-info-sign" data-toggle="popover" title="'.$title.'" data-content="'.$helptext.'" data-placement="bottom"></span></span>';
	if($addon) $return = '<span class="input-group-addon">'.$addon.'</span>' ."\n". $return;
	return $return;
}

//Processes Markdownish syntax for formatting lines
$footnote_count = 0;
function markdown($string){
	$string = preg_replace('/(\*)((\S)(.*?)(\S)|(\S))(\*)/', '<strong>\2</strong>', $string);										// *bold*
	$string = preg_replace('/(\#)((\S)(.*?)(\S)|(\S))(\#)/', '<em>\2</em>', $string);												// #italics#
	$string = preg_replace('/(\_)((\S)(.*?)(\S)|(\S))(\_)/', '<span style="text-decoration: underline;">\2</span>', $string);		// _underline_
	$string = preg_replace('/(\~)((\S)(.*?)(\S)|(\S))(\~)/', '<del>\2</del>', $string);												// ~strikethrough~
	$string = preg_replace('/(\^)((\S)(.*?)(\S)|(\S))(\^)/', '<sup>\2</sup>', $string);												// ^superscript^
	$string = preg_replace('/(\@)((\S)(.*?)(\S)|(\S))(\@)/', '<span style="text-decoration: overline;">\2</span>', $string);		// @overline@
	$string = preg_replace('/(\()(.*?)(\))(\[)(\S*?)(\])/', '<span style="color: \5;">\2</span>', $string);							// (this text is red)[red]
	
	//Format and count footnotes
	$string = preg_replace_callback('/( *)(\[\* )(.+?)(\])/', function ($string) { global $footnote_count; return '<small class="footnote"><sup>'.++$footnote_count.'</sup></small>'; }, $string);
	
	$string = nl2br($string);
	return $string;
}

//Replaces dashes with underscores for DB table names
function dash2us($string){
	return str_replace('-', '_', $string);
}

//Handles plural and singular nouns
//If plural form is just suffix (e.g. "-es"),
//append suffix to singular.
//$return_num prepends the supplied number with a space
function plural($n, $sg, $pl, $return_num = false){
	if(strpos($pl, '-') === 0) $pl = $sg.substr($pl, 1);
	if($return_num){ $sg = $n.' '.$sg; $pl = $n.' '.$pl; }
	if($n == 1) return $sg;
	else return $pl;
}

//Removes HTML tags, like strip_tags(),
//but leaves text between angle brackets that isn't HTML
function strip_html($string){
	if(preg_match('/\<(.+?)\>(.*?)\<\/(.+?)\>/', $string)) $_SESSION['alerts'][] = array('warning', 'The use of HTML is not permitted. All HTML tags have been removed. Please use the Markdown syntax outlined in the help documentation.');
	elseif(preg_match('/(\<)(br|hr|input|embed|img|link|meta|script)(.*?)(\>)/', $string)) $_SESSION['alerts'][] = array('warning', 'The use of HTML is not permitted. All HTML tags have been removed. Please use the Markdown syntax outlined in the help documentation.');
	
	$string = preg_replace('/\<(.+?)\>(.*?)\<\/(.+?)\>/', '\2', $string);							//Removes HTML start and end tags, leaving text
	$string = preg_replace('/(\<)(br|hr|input|embed|img|link|meta|script)(.*?)(\>)/', '', $string);	//Removes HTML elements that don't need an end tag
	$string = str_replace('<', '&lt;', $string); $string = str_replace('>', '&gt;', $string);		//Replace the rest with harmless HTML < and >
	
	return $string;
}

//Controls access to namespaces
//and registers new namespaces.
//Also reserves base namespaces
//(including the unused namespaces)
function nspace($namespace = false, $plugin_name = false){
	static $namespaces = array(	'view'		=> 'main.page.php',
								'new'		=> 'functions/new.page.php',
								'edit'		=> 'functions/edit.page.php',
								'compare'	=> 'functions/compare.page.php',
								'import'	=> 'functions/import.page.php',
								'export'	=> 'functions/export.page.php',
								'backup'	=> 'functions/backup.page.php',
								'help'		=> 'docs/main.page.php',
								'faq'		=> 'docs/faq.page.php',
								'about'		=> 'docs/about.page.php',
								'main'		=> 'main.page.php',
								'corpus'	=> 'main.page.php',
								'manuscript'=> 'main.page.php',
								'ms'		=> 'main.page.php',
								'line'		=> 'main.page.php');
	if($namespace && $plugin_name && array_key_exists($namespace, $namespaces)) die('A namespace cannot be registered more than once.');
	elseif($namespace && $plugin_name) $namespaces[$namespace] = 'plugins/'.$plugin_name.'/'.$namespace.'.page.php';
	elseif($namespace && array_key_exists($namespace, $namespaces)) return $namespaces[$namespace];
	else return false;
}

//Parses URL and returns arguments
//using this format:
// <base>/<namespace>/<0>/<1>/.../<n>
//where <base> includes /manuscriptus-pro
function argument($key){
	$path = array();
	$path['base'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
	
	$args = explode('/', utf8_decode(substr(urldecode(rtrim($_SERVER['REQUEST_URI'], '/')), strlen($path['base']) + 1)));
	
	if(empty($args[0])) $args[0] = false;
	else $path['namespace'] = $args[0];
	unset($args[0]);
	
	$path = array_merge($path, $args);
	
	if(array_key_exists($key, $path)) return $path[$key];
	else return false;
}

//Add to page title
//Text is prepended to <title>
//Until returned with page_title()
function page_title($string = false){
	static $page_title = 'Manuscriptus Pro';
	static $modified = false;
	if($string && !$modified){ $page_title = $string.' | '.$page_title; $modified = true; }
	elseif($string) $page_title = $string.$page_title;
	else return $page_title;
}

//Add to breadcrumbs
//Last item receives "active" class
//There must be at least 2 breadcrumbs
//Return breadcumbs with breadcrumbs()
function breadcrumbs($title = false, $url = false){
	static $breadcrumbs = array('All Corpuses' => THE_BASE_URL);
	if($title && $url){
		$breadcrumbs[$title] = $url;
	}
	elseif(count($breadcrumbs) < 2) return false;
	else{
		$breadcrumbs_return = '<ul class="breadcrumb">';
		$activecrumb = array_search(end($breadcrumbs), $breadcrumbs);
		foreach($breadcrumbs as $title => $url){
			if($title == $activecrumb) $breadcrumbs_return .= '<li class="active">'.$title.'</li>';
			else $breadcrumbs_return .= '<li><a href="'.$url.'">'.$title.'</a></li>';
		}
		$breadcrumbs_return .= '</ul><h1>'.$activecrumb.'</h1>';
		return $breadcrumbs_return;
	}
}

//Adds items to navbar
//$items is an array of arrays
//of the following format:
//array('title' => '<TheTitle>', 'url' => '<the/url/>', 'condition' => <bool>)
//If $title already exists, items are
//appended to it following a divider.
//Use nav_menu() to return nav menu.
function nav_menu($title = false, $items = false){
	static $nav_menu = array();
	if($title && !empty($items)){
		if(array_key_exists($title, $nav_menu)){
			$nav_menu[$title][] = false;
			$nav_menu[$title] = array_merge($nav_menu[$title], $items);
		}
		else $nav_menu[$title] = $items;
	}
	else{
		$nav_return = '';
		foreach($nav_menu as $title => $items){
			$nav_return .= '<li class="dropdown">';
			$nav_return .= '<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$title.'</a>';
			$nav_return .= '<ul class="dropdown-menu" role="menu">';
			foreach($items as $item){
				if(!is_array($item)) $nav_return .= '<li class="divider"></li>';
				elseif($item['condition']) $nav_return .= '<li><a href="'.$item['url'].'">'.$item['title'].'</a></li>';
				else $nav_return .= '<li class="disabled"><a>'.$item['title'].'</a></li>';
			}
			$nav_return .= '</ul></li>';
		}
		return $nav_return;
	}
}

###Important Variables Used by Main & Basic Functions###
$the_corpus = false;
$the_ms = false;
$the_line = false;

/*
 * Require Include Files for Main & Basic Functions
 */
require_once('main.inc.php');
require_once('functions/functions.inc.php');

/*
 * Require Plugin Include Files
 */
//Do this!

###Help Menu###
//The Help menu is always last
nav_menu('Help', array(
	//array('title' => 'Manuscriptus Pro FAQ', 'url' => 'faq/', 'condition' => 1),
	array('title' => 'Manuscriptus Pro Help', 'url' => 'help/', 'condition' => 1),
	array('title' => 'About Manuscriptus Pro', 'url' => 'about/', 'condition' => 1)
));

###If POST Header Still Full###
//Throw error, because form was
//not processed.
if(!empty($_POST)){
	$_SESSION['alerts'][] = array('danger', "A form was submitted, but no action was taken.");
	header("Location: ".THE_BASE_URL);
	exit();
}

###Form Data from Failed Form Submission###
//If a form submission fails, the entered data is stored
//in the session and the user is returned to the form.
//This transfers the session variables to an array
//so that the form page continues to function.
if(isset($_SESSION['formdata'])) $form_data = $_SESSION['formdata'];
else $form_data = false;

###If Namespace is not Registered###
if(argument('namespace') && !nspace(argument('namespace'))){
	$_SESSION['alerts'][] = array('danger', argument('namespace').' is not a registered namespace.');
	header("Location: ".THE_BASE_URL);
	exit();
}

//Welcome messages for first time users
$ms_tables = $db->get_row("SELECT COUNT(*) AS table_count FROM information_schema.tables WHERE table_schema = 'manuscriptus'")->table_count - 2;

if($counts->corpus_count < 1 && $counts->ms_count < 1 && strpos($_SERVER['PHP_SELF'],'new') !== false) $_SESSION['alerts'][] = array('info', "With this form you can create a new corpus. Simply enter a name for the corpus and a unique corpus identifier will be suggested. You can change the identifier if you wish, but it must be unique and can only contain lowercase alphanumeric characters and dashes. When you are finished simply click <b>Create Corpus</b>.");

elseif($counts->corpus_count < 1 && $counts->ms_count < 1) $_SESSION['alerts'][] = array('info', "Welcome to Manuscriptus Pro. Everything seems to have been set up correctly. You can get started with your first corpus by going to <b>File</b> > <b>New Corpus...</b>");

elseif($counts->ms_count < 1 && strpos($_SERVER['PHP_SELF'],'new') !== false && $the_corpus) $_SESSION['alerts'][] = array('info', "With this form you can add a manuscript to the corpus you created previously. Simply enter in the details for the manuscript, then click <b>Create Manuscript</b>. As before, an identifier will be suggested, but you can change it. It must, however, be unique and only contain lowercase alphanumeric characters and dashes.");

elseif($counts->ms_count < 1) $_SESSION['alerts'][] = array('info', "Now that you've created your first corpus, it's time to create your first manuscript. You can add a new manuscript to the corpus by going to <b>File</b> > <b>New Manuscript...</b>");

elseif($ms_tables < 1 && strpos($_SERVER['PHP_SELF'],'new' && $the_corpus && $the_ms) !== false) $_SESSION['alerts'][] = array('info', "With this form you can add a new line to a manuscript. Simply enter the line number and text then click <b>Save</b>. Clicking <b>Save + Continue...</b> will save the line then refresh the form so that you can add another line. Note that you can format the text (e.g. bold, italics, etc.) and add footnotes using the Markdown syntax outlined in the help documentation. This will then be displayed in the Preview area below.");

elseif($ms_tables < 1) $_SESSION['alerts'][] = array('info', "Now that you've created your first manuscript, it's time to add a line (or lines) to it. You can add a new line to a manuscript by going to <b>File</b> > <b>New Line...</b>");
<?php
/*
 * Important variables
 * the_corpus, the_ms, and the_line
 * identical to those used by
 * the basic functions.
 */
if(in_array(argument('namespace'), array('new', 'edit', 'import', 'export')) && argument(0)){
	$the_corpus = $db->escape(strip_html(argument(0)));
	$the_corpus = $db->get_row("SELECT * FROM corpuses WHERE id = '$the_corpus'");
	page_title($the_corpus->name);
	breadcrumbs($the_corpus->name, 'view/'.$the_corpus->id.'/');
	if(!$the_corpus){
		$_SESSION['alerts'][] = array('danger', 'No corpus with the identifier <b>'.argument(0).'</b> could be found.');
		header("Location: ".THE_BASE_URL);
		exit();
	}
}

if(in_array(argument('namespace'), array('new', 'edit', 'import', 'export')) && $the_corpus && argument(1)){
	$the_ms = $db->escape(strip_html(argument(1)));
	$the_ms = $db->get_row("SELECT * FROM manuscripts WHERE id = '$the_ms'");
	page_title($the_ms->name.' « ');
	breadcrumbs($the_ms->name, 'view/'.$the_corpus->id.'/'.$the_ms->id.'/');
	if(!$the_ms){
		$_SESSION['alerts'][] = array('danger', 'No manuscript with the identifier <b>'.argument(1).'</b> could be found in the corpus <b>'.argument(0).'</b>.');
		header("Location: ".THE_BASE_URL."view/".$the_corpus->id."/");
		exit();
	}
}

if(argument('namespace') == 'edit' && $the_corpus && $the_ms && argument(2)){
	$the_line = $db->escape(strip_html(argument(2)));
	$the_line = $db->get_row("SELECT * FROM ".dash2us($the_ms->id)." WHERE number = '$the_line'");
	if(!$the_line){
		$_SESSION['alerts'][] = array('danger', '<b>Line '.argument(2).'</b> of <b>'.argument(1).'</b> could be found in the corpus <b>'.argument(0).'</b>.');
		header("Location: ".THE_BASE_URL."view/".$the_corpus->id."/".$the_ms->id."/");
		exit();
	}
}

/*
 * Active Breadcrumbs & Page Titles
 */

if(argument('namespace') == 'new' && $the_corpus && $the_ms) {breadcrumbs('Add Line', 'new/'.$the_corpus->id.'/'.$the_ms->id.'/'); page_title('New Line « ');}
elseif(argument('namespace') == 'new' && $the_corpus) {breadcrumbs('Create New Manuscript', 'new/'.$the_corpus->id.'/'); page_title('New Manuscript « ');}
elseif(argument('namespace') == 'new') {breadcrumbs('Create New Corpus', 'new/'); page_title('New Corpus');}

elseif(argument('namespace') == 'edit' && $the_corpus && $the_ms && $the_line) breadcrumbs('Edit Line '.$the_line->number, 'edit/'.$the_corpus->id.'/'.$the_ms->id.'/'.$the_line->number.'/');
elseif(argument('namespace') == 'edit' && $the_corpus && $the_ms) breadcrumbs('Edit Manuscript', 'edit/'.$the_corpus->id.'/'.$the_ms->id.'/');
elseif(argument('namespace') == 'edit' && $the_corpus) breadcrumbs('Edit Corpus', 'edit/'.$the_corpus->id.'/');

elseif(argument('namespace') == 'backup' && argument(0) == 'create') page_title('Create Backup');
elseif(argument('namespace') == 'backup' && argument(0) == 'restore') page_title('Restore from Backup');
elseif(argument('namespace') == 'backup') header("Location: ".THE_BASE_URL);

if(argument('namespace') == 'edit') page_title('Edit « ');

/*
 * Nav Items
 * Nav menu items for basic functions.
 * Creates dropdowns File and Edit.
 */
nav_menu('File', array(
	array('title' => 'New Corpus...', 'url' => 'new/', 'condition' => 1),
	array('title' => 'New Manuscript...', 'url' => 'new/'.($the_corpus ? $the_corpus->id : '').'/', 'condition' => $the_corpus),
	array('title' => 'New Line...', 'url' => 'new/'.($the_corpus ? $the_corpus->id : '').'/'.($the_ms ? $the_ms->id : '').'/', 'condition' => $the_ms)
));
/*nav_menu('File', array(
	array('title' => 'Import Corpus...', 'url' => 'import/', 'condition' => 1),
	array('title' => 'Import Manuscript...', 'url' => 'import/'.($the_corpus ? $the_corpus->id : '').'/', 'condition' => $the_corpus),
	array('title' => 'Import Lines...', 'url' => 'import/'.($the_corpus ? $the_corpus->id : '').'/'.($the_ms ? $the_ms->id : '').'/', 'condition' => $the_ms)
));
nav_menu('File', array(
	array('title' => 'Export Corpus', 'url' => 'export/'.($the_corpus ? $the_corpus->id : '').'/', 'condition' => $the_corpus),
	array('title' => 'Export Manuscript', 'url' => 'export/'.($the_corpus ? $the_corpus->id : '').'/'.($the_ms ? $the_ms->id : '').'/', 'condition' => $the_ms),
	array('title' => 'Export Lines...', 'url' => 'export/'.($the_corpus ? $the_corpus->id : '').'/'.($the_ms ? $the_ms->id : '').'/lines/', 'condition' => $the_ms)
));*/
nav_menu('File', array(
	array('title' => 'Create Backup', 'url' => 'backup/create/', 'condition' => 1)
));
nav_menu('Edit', array(
	array('title' => 'Edit Corpus...', 'url' => 'edit/'.($the_corpus ? $the_corpus->id : '').'/', 'condition' => $the_corpus),
	array('title' => 'Edit Manuscript...', 'url' => 'edit/'.($the_corpus ? $the_corpus->id : '').'/'.($the_ms ? $the_ms->id : '').'/', 'condition' => $the_ms)
));

/*
 * Form Processing
 * Processing of forms for
 * basic functions (new, edit,
 * delete, import, export, backup)
 */
//If a form was submitted
if(!empty($_POST)){
	//Create New Corpus
	if(isset($_POST['corpusName']) && isset($_POST['corpusId'])){
		//Escape and store POST variables
		$corpusId = $db->escape(strip_html($_POST['corpusId']));
		$corpusName = $db->escape(strip_html($_POST['corpusName']));
		$corpusDescription = $db->escape(strip_html($_POST['corpusDescription']));
		//Check if corpus name is duplicate, throw warning if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE name = '$corpusName') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('warning', "A corpus with the name <b>$corpusName</b> already exists. Consider using a unique corpus name.");
		}
		//Check if corpus id is duplicate, throw error if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE id = '$corpusId') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "A corpus with the identifier <b>$corpusId</b> already exists. Please use a unique corpus identifier.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/");
			exit();
		}
		//Else insert corpus into DB
		$query = $db->query("INSERT INTO corpuses (id, name, description) VALUES ('$corpusId','$corpusName','$corpusDescription')");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The new corpus was created successfully.');
			header("Location: ".THE_BASE_URL."view/$corpusId/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The new corpus could not be created due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Create New MS
	elseif(isset($_POST['MSName']) && isset($_POST['MSId']) && isset($_POST['MSCorpus'])){
		//Escape and store POST variables
		$MSId = $db->escape(strip_html($_POST['MSId']));
		$MSCorpus = $db->escape(strip_html($_POST['MSCorpus']));
		$MSSiglum = $db->escape(strip_html($_POST['MSSiglum']));
		$MSName = $db->escape(strip_html($_POST['MSName']));
		$MSRange = $db->escape(strip_html($_POST['MSRange']));
		$MSDate = $db->escape(strip_html($_POST['MSDate']));
		$MSLink = $db->escape(strip_html($_POST['MSLink']));
		$MSDescription = $db->escape(strip_html($_POST['MSDescription']));
		//Check if corpus exists, throw error if false
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE id = '$MSCorpus') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "No corpus with the identifier <b>$MSCorpus</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Check if manuscript name is duplicate within corpus, throw warning if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE name = '$MSName' AND corpus = '$MSCorpus') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('warning', "There are multiple manuscripts with the name <b>$MSName</b> in this corpus. Consider using a unique name for each manuscript.");
		}
		//Check if manuscript id is duplicate, throw error if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE id = '$MSId') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "A manuscript with the identifier <b>$MSId</b> already exists. Please use a unique manuscript identifier.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/$MSCorpus/");
			exit();
		}
		//Check if manuscript id is 'all', throw error if true
		if($MSId == 'all'){
			$_SESSION['alerts'][] = array('danger', "<b>all</b> is a reserved word and cannot be used as a manuscript identifier.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/$MSCorpus/");
			exit();
		}
		//Check if manuscript id is 'manuscripts', throw error if true
		if($MSId == 'manuscripts'){
			$_SESSION['alerts'][] = array('danger', "<b>manuscripts</b> is a reserved word and cannot be used as a manuscript identifier.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/$MSCorpus/");
			exit();
		}
		//Check if manuscript sigla is duplicate, throw error of true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE siglum = '$MSSiglum' AND corpus = '$MSCorpus') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "A manuscript with the siglum <b>$MSSiglum</b> already exists in this corpus. Please use a unique siglum for each manuscript.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/$MSCorpus/");
			exit();
		}
		//Else insert MS into DB
		$query = $db->query("INSERT INTO manuscripts (id, corpus, name, siglum, frange, date, link, description) VALUES ('$MSId', '$MSCorpus', '$MSName', '$MSSiglum', '$MSRange', '$MSDate', '$MSLink', '$MSDescription')");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The new manuscript was created successfully.');
			header("Location: ".THE_BASE_URL."view/$MSCorpus/$MSId/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The new manuscript could not be created due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Add New Line
	elseif(isset($_POST['lineNumber']) && isset($_POST['lineText']) && isset($_POST['lineMS'])){
		//Escape and store POST variables
		$lineNumber = $db->escape(strip_html($_POST['lineNumber']));
		$lineText = $db->escape(strip_html($_POST['lineText']));
		$lineComments = $db->escape(strip_html($_POST['lineComments']));
		$lineCorpus = $db->escape(strip_html($_POST['lineCorpus']));
		$lineMS = $db->escape(strip_html($_POST['lineMS']));
		//Check if MS exists, throw error if false
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE id = '$lineMS') AS bool");
		if(!$is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "No manuscript with the identifier <b>$lineMS</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Create table for MS if it doesn't exist
		$db->query("CREATE TABLE IF NOT EXISTS ".dash2us($lineMS)." (number varchar(7) PRIMARY KEY, text text CHARACTER SET utf8 NOT NULL, comments text CHARACTER SET utf8 NOT NULL)");
		//Check if line number is duplicate, throw error of true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM ".dash2us($lineMS)." WHERE number = '$lineNumber') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "Line number <b>$lineNumber</b> already exists in this manuscript. Please use a unique line number for each line in a manuscript.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."new/$lineCorpus/$lineMS/");
			exit();
		}
		//Else insert line into DB
		$query = $db->query("INSERT INTO ".dash2us($lineMS)." (number, text, comments) VALUES ('$lineNumber', '$lineText', '$lineComments')");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The line was saved successfully.');
			$_SESSION['formdata']['lineNumber'] = ++$lineNumber;
			if($_POST['save'] == 'continue') header("Location: ".THE_BASE_URL."new/$lineCorpus/$lineMS/");
			else header("Location: ".THE_BASE_URL."view/$lineCorpus/$lineMS/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The line could not be saved due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Edit Corpus
	elseif(isset($_POST['corpusNameNew']) && isset($_POST['corpusId'])){
		//Escape and store POST variables
		$corpusId = $db->escape(strip_html($_POST['corpusId']));
		$corpusName = $db->escape(strip_html($_POST['corpusNameNew']));
		$corpusDescription = $db->escape(strip_html($_POST['corpusDescriptionNew']));
		//Check if corpus name is duplicate, throw warning if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE name = '$corpusName' AND id != '$corpusId') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('warning', "A corpus with the name <b>$corpusName</b> already exists. Consider using a unique corpus name.");
		}
		//Check if corpus being edited exists, throw error if false
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE id = '$corpusId') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "No corpus with the identifier <b>$corpusId</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Else update DB
		$query = $db->query("UPDATE corpuses SET name = '$corpusName', description = '$corpusDescription' WHERE id = '$corpusId'");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The corpus was edited successfully.');
			header("Location: ".THE_BASE_URL."view/$corpusId/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The corpus could not be edited due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Edit Manuscript
	elseif(isset($_POST['MSNameNew']) && isset($_POST['MSId']) && isset($_POST['MSCorpus'])){
		//Escape and store POST variables
		$MSId = $db->escape(strip_html($_POST['MSId']));
		$MSCorpus = $db->escape(strip_html($_POST['MSCorpus']));
		$MSSiglum = $db->escape(strip_html($_POST['MSSiglumNew']));
		$MSName = $db->escape(strip_html($_POST['MSNameNew']));
		$MSRange = $db->escape(strip_html($_POST['MSRangeNew']));
		$MSDate = $db->escape(strip_html($_POST['MSDateNew']));
		$MSLink = $db->escape(strip_html($_POST['MSLinkNew']));
		$MSDescription = $db->escape(strip_html($_POST['MSDescriptionNew']));
		//Check if name is duplicate, throw warning if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE name = '$MSName' AND corpus = '$MSCorpus' AND id != '$MSId') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('warning', "There are multiple manuscripts with the name <b>$MSName</b> in the corpus <b>$MSCorpus</b>. Consider using a unique name for each manuscript.");
		}
		//Check if siglum is duplicate, throw error if true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE siglum = '$MSSiglum' AND corpus = '$MSCorpus' AND id != '$MSId') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "A manuscript with the siglum <b>$MSSiglum</b> already exists in the corpus <b>$MSCorpus</b>. Please use a unique siglum for each manuscript.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."edit/$MSCorpus/$MSId/");
			exit();
		}
		//Check if ms being edited exists, throw error if false
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE id = '$MSId') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "No manuscript with the identifier <b>$MSId</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Else update DB
		$query = $db->query("UPDATE manuscripts SET name = '$MSName', siglum = '$MSSiglum', frange = '$MSRange', date = '$MSDate', link = '$MSLink', description = '$MSDescription' WHERE id = '$MSId'");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The manuscript was edited successfully.');
			header("Location: ".THE_BASE_URL."view/$MSCorpus/$MSId/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The manuscript could not be edited due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Edit Line
	elseif(isset($_POST['lineNumberNew']) && isset($_POST['lineTextNew'])){
		//Escape and store POST variables
		$lineNumber = $db->escape(strip_html($_POST['lineNumberNew']));
		$lineNumberOld = $db->escape(strip_html($_POST['lineNumberOld']));
		$lineText = $db->escape(strip_html($_POST['lineTextNew']));
		$lineComments = $db->escape(strip_html($_POST['lineCommentsNew']));
		$lineCorpus = $db->escape(strip_html($_POST['lineCorpus']));
		$lineMS = $db->escape(strip_html($_POST['lineMS']));
		//Check if line number is duplicate, throw error of true
		$is_duplicate = $db->get_row("SELECT EXISTS(SELECT 1 FROM ".dash2us($lineMS)." WHERE number = '$lineNumber' AND number != '$lineNumberOld') AS bool");
		if($is_duplicate->bool){
			$_SESSION['alerts'][] = array('danger', "Line number <b>$lineNumber</b> already exists in this manuscript. Please use a unique line number for each line in a manuscript.");
			$_SESSION['formdata'] = $_POST;
			header("Location: ".THE_BASE_URL."edit/$lineCorpus/$lineMS/$lineNumber/");
			exit();
		}
		//Check if ms being edited exists, throw error if false
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM ".dash2us($lineMS)." WHERE number = '$lineNumberOld') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "<b>Line $lineNumber</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Else update DB
		$query = $db->query("UPDATE ".dash2us($lineMS)." SET number = '$lineNumber', text = '$lineText', comments = '$lineComments' WHERE number = '$lineNumberOld'");
		if($query){
			$_SESSION['alerts'][] = array('success', 'The line was edited successfully.');
			$_SESSION['formdata']['lineNumber'] = ++$lineNumber;
			$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM ".dash2us($lineMS)." WHERE number = '$lineNumber') AS bool");
			if($_POST['save'] == 'continue' && $does_exist->bool) header("Location: ".THE_BASE_URL."edit/$lineCorpus/$lineMS/$lineNumber/");
			elseif($_POST['save'] == 'continue') header("Location: ".THE_BASE_URL."new/$lineCorpus/$lineMS/");
			else header("Location: ".THE_BASE_URL."view/$lineCorpus/$lineMS/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'The line could not be edited due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
		}
		exit();
	}
	//Delete Corpus
	elseif(isset($_POST['deleteCorpusId'])){
		//Escape and store POST variables
		$corpusId = $db->escape(strip_html($_POST['deleteCorpusId']));
		//Check if corpus exists
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM corpuses WHERE id = '$corpusId') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "No corpus with the identifier <b>$MSId</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Else delete corpus from corpuses table
		$query = $db->query("DELETE FROM corpuses WHERE id = '$corpusId'");
		if(!$query){
			$_SESSION['alerts'][] = array('danger', 'The corpus could not be deleted due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//and delete all MSS in corpus
		$MSList = $db->get_results("SELECT * FROM manuscripts WHERE corpus = '$corpusId'");
		if(!empty($MSList)) foreach($MSList as $deleteMS){
			$MSId = $deleteMS->id;
			$query = $db->query("DELETE FROM manuscripts WHERE id = '$MSId'");
			if(!$query){
				$_SESSION['alerts'][] = array('danger', 'A manuscript in the corpus could not be deleted due to a database error. For more information, please consult the help documentation.');
				header("Location: ".THE_BASE_URL);
				exit();
			}
			//and their tables
			$query = $db->query("DROP TABLE IF EXISTS ".dash2us($MSId));
			if($query === false){
				$_SESSION['alerts'][] = array('danger', 'Due to a database error, a manuscript in the corpus was only partially deleted. For more information, please consult the help documentation.');
				header("Location: ".THE_BASE_URL);
				exit();
			}
		}
		$_SESSION['alerts'][] = array('success', 'The corpus was deleted successfully.');
		header("Location: ".THE_BASE_URL);
		exit();
	}
	//Delete Manuscript
	elseif(isset($_POST['deleteMSId'])){
		//Escape and store POST variables
		$MSId = $db->escape(strip_html($_POST['deleteMSId']));
		$MSCorpus = $db->escape(strip_html($_POST['MSCorpus']));
		//Check if MS exists
		$does_exist = $db->get_row("SELECT EXISTS(SELECT 1 FROM manuscripts WHERE id = '$MSId') AS bool");
		if(!$does_exist->bool){
			$_SESSION['alerts'][] = array('danger', "No manuscript with the identifier <b>$MSId</b> could be found.");
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//Else delete MS from manuscripts table
		$query = $db->query("DELETE FROM manuscripts WHERE id = '$MSId'");
		if(!$query){
			$_SESSION['alerts'][] = array('danger', 'The manuscript could not be deleted due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
			exit();
		}
		//and delete MS table
		$query = $db->query("DROP TABLE IF EXISTS ".dash2us($MSId));
		if($query !== false){
			$_SESSION['alerts'][] = array('success', 'The manuscript was deleted successfully.');
			header("Location: ".THE_BASE_URL."view/$MSCorpus/");
		}
		else{
			$_SESSION['alerts'][] = array('danger', 'Due to a database error, the manuscript was only partially deleted. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL."view/$MSCorpus/");
		}
		exit();
	}
	//Delete Line
	elseif(isset($_POST['deleteLineNumber'])){
		$lineNumber = $db->escape(strip_html($_POST['deleteLineNumber']));
		$lineMS = $db->escape(strip_html($_POST['deleteLineMS']));
		$lineCorpus = $db->escape(strip_html($_POST['deleteLineCorpus']));
		$query = $db->query("DELETE FROM ".dash2us($lineMS)." WHERE number = '$lineNumber'");
		if(!$query){
			$_SESSION['alerts'][] = array('danger', 'The line could not be deleted due to a database error. For more information, please consult the help documentation.');
			header("Location: ".THE_BASE_URL);
			exit();
		}
		else{
			$_SESSION['alerts'][] = array('success', 'The line was deleted successfully.');
			header("Location: ".THE_BASE_URL."view/$lineCorpus/$lineMS/");
			exit();
		}
	}
}
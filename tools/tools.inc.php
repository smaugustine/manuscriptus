<?php
/*
 * Important variables
 * the_corpus, the_ms, and the_line
 * identical to those used by
 * the basic functions.
 */
if(in_array(argument('namespace'), array('analyze', 'search')) && argument(0)){
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

if(in_array(argument('namespace'), array('analyze', 'search')) && $the_corpus && argument(1)){
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

/*
 * Active Breadcrumbs & Page Titles
 */

if(argument('namespace') == 'search' && $the_corpus && $the_ms) {breadcrumbs('Search', '/'); page_title('Search');}

/*
 * Nav Items
 * Nav menu items for basic functions.
 * Creates dropdowns File and Edit.
 */
nav_menu('Tools', array(
	//array('title' => 'Search...', 'url' => 'search/', 'condition' => 1),
	//array('title' => 'Analyze', 'url' => 'analyze/', 'condition' => $the_corpus)
));
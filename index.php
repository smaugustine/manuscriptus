<?php
/*
 * index.php
 * Base Page Template
 * Used for loading all page content
 */

require_once('common.inc.php'); //Required before any output
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<base href="<?=THE_BASE_URL?>">
	<title><?=page_title()?></title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid main col-sm-offset-1 col-sm-10">
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#toolbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			<a class="navbar-brand" href="<?=THE_BASE_URL?>">Manuscriptus: Codex</a>
			</div>
		
			<div class="collapse navbar-collapse" id="toolbar">
				<ul class="nav navbar-nav">
					<?=nav_menu()?>
				</ul>
			</div>
		</div>
	</nav>
<?php
	###Alerts###
	//Note that alerts are displayed
	//in the order that they are sent
	if(isset($_SESSION['alerts'])){
		foreach($_SESSION['alerts'] as $alert): ?>
			<div class="alert alert-<?=$alert[0]?> alert-dismissible" role="alert" style="margin-top: 10px;">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?=$alert[1]?>
			</div>
		<?php
		endforeach;
	}
	session_destroy();

	echo breadcrumbs();
	
	/*
	 * Loader for Page Template
	 * based on registered namespaces
	 */
	if(nspace(argument('namespace'))) require_once(nspace(argument('namespace')));
	else require_once('main.page.php');
?>
</div>
<footer class="footer">
	<div class="container row col-sm-offset-1 col-sm-10">
		<p class="text-muted text-center"><small>Powered by <b><a href="about/">Manuscriptus: Codex</a> <?=MC_VERSION?></b>. This project has <b><?=plural($counts->ms_count, 'manuscript', '-s', true)?></b> in <b><?=plural($counts->corpus_count, 'corpus', '-es', true)?></b>.</small></p>
	</div>
</footer>
<script>
	$(document).ready(function(){
		$("p,li,h1,a:contains('Manuscriptus: Codex')").html(function(_, html) {
			return html.replace(/(Codex)/g, '<span class="mscodex">$1</span>');
		});
		$("p a:contains('Manuscriptus: Codex')").html(function(_, html) {
			return html.replace(/(Manuscriptus: )/g, '<span style="color: #333;">$1</span>');
		});
	});
</script>
</body>
</html>
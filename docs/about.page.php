<h1 class="page-header" style="text-align: left;">About Manuscriptus Pro</h1>
<p class="lead">This project is powered by <b><a href="http://manscriptus.org/" target="_blank">Manuscriptus Pro</a></b>, a free open-source application intended to be an elegant, modern solution for preparing, organizing, and analyzing manuscript corpuses. It is a web-based application that can be used by a single user on their home/work computer or by several collaborators on a website/server.</p>

<h2>This Project</h2>
<?php
$counts = $db->get_row("SELECT COUNT(*) AS corpus_count,(SELECT COUNT(*) FROM manuscripts) AS ms_count FROM corpuses");

$word_count = 0;
$line_count = 0;
?>
<p>This project has <b><?=plural($counts->ms_count, 'manuscript', '-s', true)?></b> in <b><?=plural($counts->corpus_count, 'corpus', '-es', true)?></b>.</p>
<table class="table table-bordered" style="width: auto;">
	<thead><th>Identifier</th><th>Line Count</th><th>Word Count</th></thead>
	<tbody>
<?php
$corpuses = $db->get_results("SELECT id FROM corpuses");
foreach($corpuses as $corpus){
	$manuscripts = $db->get_results("SELECT id FROM manuscripts WHERE corpus = '".$corpus->id."'");
	$corpus_word_count = 0; $corpus_line_count = 0;
	if(!empty($manuscripts)) foreach($manuscripts as $manuscript){
		$corpus_word_count += $db->get_row("SELECT SUM(LENGTH(text) - LENGTH(REPLACE(text, ' ', ''))+1) AS word_count FROM ".dash2us($manuscript->id))->word_count + 0;
		$corpus_line_count += $db->get_row("SELECT COUNT(*) AS line_count FROM ".dash2us($manuscript->id))->line_count  + 0;
	}
	$word_count += $corpus_word_count;
	$line_count += $corpus_line_count;
	echo '<tr><th>'.$corpus->id.'</th>';
	echo '<th style="text-align: right;">'.$corpus_line_count.'</th>';
	echo '<th style="text-align: right;">'.$corpus_word_count.'</th>';
	if(!empty($manuscripts)) foreach($manuscripts as $manuscript){
		echo '<tr><td style="text-indent: 1.5em;">'.$manuscript->id.'</td>';
		echo '<td style="text-align: right;">';
			echo $db->get_row("SELECT COUNT(*) AS line_count FROM ".dash2us($manuscript->id))->line_count + 0;
		echo '</td>';
		echo '<td style="text-align: right;">';
			echo $db->get_row("SELECT SUM(LENGTH(text) - LENGTH(REPLACE(text, ' ', ''))+1) AS word_count FROM ".dash2us($manuscript->id))->word_count + 0;
		echo '</td>';
	}
}
?>
	</tbody>
	<tfoot>
		<th>Total</th>
		<th style="text-align: right;"><?=$line_count?></th>
		<th style="text-align: right;"><?=$word_count?></th>
	</tfoot>
</table>

<h2>Technical Information</h2>
<p>This project uses <b>Manuscriptus Pro <?=MP_VERSION?></b> with<?php if(!is_file('../user.inc.php')) echo 'out'; ?> the user account module. Manuscriptus Pro was written in <a href="http://php.net/" target="_blank">PHP 5</a> and uses a <a href="http://mysql.com/" target="_blank">MySQL database</a>. It makes use of the <a href="" target="_blank">ezSQL class</a> and also the <a href="http://jquery.org/" target="_blank">jQuery library</a>, including <a href="" target="_blank">autogrow.js</a>, <a href="" target="_blank">tinysort</a>, and the <a href="" target="_blank">Bootstrap 3 Validator</a>. The front-end is built with the HTML5/CSS3-based <a href="http://getbootstrap.com/" target="_blank">Bootstrap framework</a>. Text is encoded using UTF-8 in order to allow virtually any language or alphabet to be used (however no font support is provided).</p>
<p><b>Manuscriptus Pro &copy;2015 <a href="http://shawndickinson.com/" target="_blank">Shawn Michael Dickinson</a></b>. Manuscriptus Pro is released under the <a href="http://manuscriptus.org/license/" target="_blank">MIT license</a> (<a href="help/license/" target="_blank">included here</a>), free of any warranty, and may be freely modified and redistributed provided that the original copyright notice and license are included.</p>
<p>This project and the contents therein is copyright its respective owner(s) and contributor(s).</p>
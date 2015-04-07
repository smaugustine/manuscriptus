<?php
###View single ms of corpus###
###url: manuscriptus-pro/view/<corpus>/<ms>/###
if($the_corpus && $the_ms):
?>
	<p class="lead">
		<b>Siglum:</b> <?=$the_ms->siglum?><br>
		<?php
		if(!empty($the_ms->frange)) echo '<b>Folio Range:</b> '.$the_ms->frange.'<br>';
		if(!empty($the_ms->date)) echo '<b>Date:</b> '.$the_ms->date.'<br>';
		if(!empty($the_ms->link)) echo '<b>Link:</b> <a href="'.$the_ms->link.'" target="_blank">'.$the_ms->link.'</a><br>';
		if(!empty($the_ms->description)) echo '<b>Description:</b> '.markdown($the_ms->description);
		?>
	</p>
	<?php
	$lines = $db->get_results("SELECT * FROM ".dash2us($the_ms->id)." ORDER BY number + 0 ASC");
	if($lines):
	?>
		<table id="manuscript-lines" class="table table-striped table-hover table-condensed">
			<?php
			$footnote_recount = 0;
			foreach($lines as $line):
			?>
				<tr>
					<th>
						<a class="btn btn-primary btn-xs glyphicon glyphicon-pencil" href="edit/<?=$the_corpus->id.'/'.$the_ms->id.'/'.$line->number?>/"></a>
						<a class="btn btn-danger btn-xs glyphicon glyphicon-trash" style="margin-left: -5px;" data-toggle="modal" data-target="#deleteLine" data-linenumber="<?=$line->number?>"></a>
					</th>
					<th><small><?=$the_ms->siglum.'.'.$line->number?></small></th>
					<td>
					<?php
						preg_match_all('/( *)(\[\* )(.+?)(\])/', $line->text, $footnotes);
						//If line has footnotes and/or comments
						if(!empty($footnotes[0]) || !empty($line->comments)):
					?>
							<a class="ms-line" data-toggle="collapse" href="#line-<?=$line->number?>-data"><?=markdown($line->text)?></a>
							<span class="glyphicon glyphicon-option-vertical"  style="float: right;"></span>
							<div id="line-<?=$line->number?>-data" class="collapse">
							<?php
								if(!empty($footnotes[0])){
									echo '<span class="footnote-divider"></span>';
									foreach($footnotes[0] as $footnote){
										$footnote = preg_replace('/(\[\* )(.+?)(\])/', '$2', $footnote);
										echo '<small class="footnote"><sup>'.++$footnote_recount.'</sup>'.markdown($footnote).'</small><br>';
									}
								}
								if(!empty($line->comments)) echo '<div class="panel panel-info"><div class="panel-body">'.markdown($line->comments).'</div></div>';
							?>
							</div>
					<?php
						//If line does not have footnotes or comments
						else: echo markdown($line->text);
						endif;
					?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<div class="modal fade" id="deleteLine" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Delete Line?</h4>
					</div>
					<div class="modal-body">
						Are you sure you want to delete <b id="infolinenumber">Line </b> of <b><?=$the_ms->name?></b>? This will permanently delete the line. The data cannot be recovered unless you have a backup or an export of it.
					</div>
					<div class="modal-footer">
						<form action="<?=THE_BASE_URL?>" method="post">
							<button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
							<input type="hidden" id="deletelinenumber" name="deleteLineNumber" value="">
							<input type="hidden" name="deleteLineMS" value="<?=$the_ms->id?>">
							<input type="hidden" name="deleteLineCorpus" value="<?=$the_corpus->id?>">
							<button type="submit" class="btn btn-danger">Delete Line</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		$('#deleteLine').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var linenumber = button.data('linenumber');
			var modal = $(this);
			modal.find('#infolinenumber').text('Line ' + linenumber);
			modal.find('#deletelinenumber').val(linenumber);
		});
		</script>
<?php
	else: echo '<div class="alert alert-warning" role="alert">No lines were found. A new line can be added by going to <b>File</b> > <b>New Line...</b></div>';
	endif;
?>

<?php
###View single corpus###
###url: manuscriptus-pro/view/<corpus>/###
elseif($the_corpus):
	if(!empty($the_corpus->description)) echo '<p class="lead">'.markdown($the_corpus->description).'</p>';
	$manuscripts = $db->get_results("SELECT * FROM manuscripts WHERE corpus = '".$the_corpus->id."' ORDER BY siglum ASC");
	if($manuscripts): ?>
		<!--<a href="view/<?=$the_corpus->id?>/all/" class="btn btn-primary" style="margin-bottom: 15px;">View All Manuscripts</a>-->
		<script src="js/tinysort.min.js"></script>
		<div id="manuscript-table"><table class="table table-striped table-hover table-bordered">
			<thead><tr>
				<th><a id="sort-siglum" class="sort sorted">Siglum</a></th>
				<th><a id="sort-name" class="sort">Name</a></th>
				<th><a id="sort-date" class="sort">Date</a></th>
				<th><a id="sort-lines" class="sort">Lines</a></th>
				<th>Folio Range</th>
				<th>Description</th>
			</tr></thead>
			<tbody>
		<?php foreach($manuscripts as $manuscript):
			$line_count = $db->get_row("SELECT COUNT(*) AS line_count FROM ".dash2us($manuscript->id));
			if($line_count) $line_count = $line_count->line_count;
			else $line_count = 0; ?>
			<tr data-href="<?='view/'.$the_corpus->id.'/'.$manuscript->id?>/" data-siglum="<?=$manuscript->siglum?>" data-name="<?=$manuscript->name?>" data-date="<?=$manuscript->date?>" data-lines="<?=$line_count?>">
				<td align="center"><?=$manuscript->siglum?></td>
				<td><?=$manuscript->name?></td>
				<td><?=$manuscript->date?></td>
				<td align="center"><?=$line_count?></td>
				<td><?=$manuscript->frange?></td>
				<td style="max-height: 18px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"><?=$manuscript->description?></td>
			</tr>
		<?php
		endforeach;
		echo '</tbody></table></div>';
	else: echo '<div class="alert alert-warning" role="alert">No manuscripts were found. A new manuscript can be created by going to <b>File</b> > <b>New Manuscript...</b></div>';
	endif;
	?>
	<script type="text/javascript">
	$(document).ready(function(){
		$("tbody tr").click(function(){
			window.document.location = $(this).data("href");
		});
		$("tbody tr").mouseover(function(){
			window.status = 'Go to "' + window.location.protocol + '//' + window.location.host + '<?=THE_BASE_URL?>' + $(this).data("href") + '"';
		});
		$("tbody tr").mouseout(function(){
			window.status = '';
		});
		
		var toggle = 0;
		$('.sort').click(function(){
			if(!$(this).hasClass('sorted')) toggle = 1;
			$('.sort').removeClass('sorted');
			$(this).addClass('sorted');
		});
		$('#sort-siglum').click(function(){
			if(toggle) { tinysort('tbody tr',{data:'siglum', order:'asc'}); toggle = 0; }
			else { tinysort('tbody tr',{data:'siglum', order:'desc'}); toggle = 1; }
			
		});
		$('#sort-name').click(function(){
			if(toggle) { tinysort('tbody tr',{data:'name', order:'asc'}); toggle = 0; }
			else { tinysort('tbody tr',{data:'name', order:'desc'}); toggle = 1; }
		});
		$('#sort-date').click(function(){
			if(toggle) { tinysort('tbody tr',{data:'date', order:'asc'}); toggle = 0; }
			else { tinysort('tbody tr',{data:'date', order:'desc'}); toggle = 1; }
		});
		$('#sort-lines').click(function(){
			if(toggle) { tinysort('tbody tr',{data:'lines', order:'asc'}); toggle = 0; }
			else { tinysort('tbody tr',{data:'lines', order:'desc'}); toggle = 1; }
		});
	});
	</script>

<?php
###If corpus has not been set, then display list of corpuses###
###url: manuscriptus-pro/###
else:
	echo '<h1>All Corpuses</h1>';
	$corpuses = $db->get_results("SELECT * FROM corpuses ORDER BY name ASC");	
	if($corpuses):
		echo '<div class="list-group">';
		foreach($corpuses as $corpus):
		$ms_count = $db->get_row("SELECT COUNT(*) AS ms_count FROM manuscripts WHERE corpus='".$corpus->id."'"); ?>
		<a href="view/<?=$corpus->id?>/" class="list-group-item">
			<span class="badge"><?=$ms_count->ms_count ?> MSS</span>
			<h4 class="list-group-item-heading"><?=$corpus->name?></h4>
			<p class="list-group-item-text"><?=markdown($corpus->description)?></p>
		</a>
		<?php
		endforeach;
		echo '</div>';
	else: echo '<div class="alert alert-warning" role="alert">No corpuses were found. A new corpus can be created by going to <b>File</b> > <b>New Corpus...</b></div>';
	endif;

endif;
?>
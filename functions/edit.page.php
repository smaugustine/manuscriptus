<script src="js/validator.min.js"></script>
<?php
###Edit line of manuscript###
###url: /manuscriptus-pro/edit/<corpus>/<ms>/<line>/###
if($the_corpus && $the_ms && $the_line):
?>
	<script src="js/autogrow.min.js"></script>
	<form id="editLine" class="form-horizontal" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<div class="col-sm-2 col-sm-offset-5 input-group">
				<?=new_input('text', 'lineNumberNew', 'Line Number', isset($form_data['lineNumberNew']) ? $form_data['lineNumberNew'] : $the_line->number, false, 'required', false, $the_ms->siglum.'.', 7)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-12">
				<textarea class="form-control" rows="2" id="lineTextNew" name="lineTextNew" placeholder="Line Text" required><?=isset($form_data['lineTextNew']) ? $form_data['lineTextNew'] : $the_line->text?></textarea>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading" role="tab">
				<a class="panel-title" data-toggle="collapse" href="#preview" style="text-align: center;"><h4 class="panel-title">Preview</h4></a>
				</div>
				<div id="preview" class="panel-collapse collapse in" role="tabpanel">
				<div id="preview-content" class="panel-body"><span class="text-muted">Preview of Line Text</span></div>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-12">
				<textarea class="form-control" rows="3" id="lineCommentsNew" name="lineCommentsNew" placeholder="Comments (Optional)"><?=isset($form_data['lineCommentsNew']) ? $form_data['lineCommentsNew'] : $the_line->comments?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-4">
				<input type="hidden" name="lineCorpus" id="lineCorpus" value="<?=$the_corpus->id?>">
				<input type="hidden" name="lineMS" id="lineMS" value="<?=$the_ms->id?>">
				<input type="hidden" name="lineNumberOld" id="lineNumberOld" value="<?=isset($form_data['lineNumberOld']) ? $form_data['lineNumberOld'] : $the_line->number?>">
				<button type="submit" name="save" value="continue" class="btn btn-success">Save + Continue...</button>
				<button type="submit" name="save" value="stop" class="btn btn-primary">Save</button>
				<button type="reset" class="btn btn-warning">Reset</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#lineTextNew').autogrow();
		
		$('#preview-content').html(footnote(markdown(strip_html($('#lineTextNew').val()))));
		
		$('#lineTextNew').keyup(function(){
			$('#preview-content').html(footnote(markdown(strip_html($(this).val()))));
		});
		
		$('#createLine').on('reset', function(e) {
			setTimeout(function() {$('#preview-content').html('');});
		});
		
		//Function for processing and formatting footnotes
		function footnote(string){
			var footnotes = string.match(/( *)(\[\* )(.+?)(\])/g);
			var i = 1;
			string = string.replace(/( *)(\[\* )(.+?)(\])/g, function(match) { return '<small  class="footnote"><sup>'+(i++)+'</sup></small>';});
			
			if(footnotes){
				var ret = '<span class="footnote-divider"></span><small class="footnote">';
				footnotes.forEach(function(element, index, array) {
					ret = ret + '<sup>'+(index + 1)+'</sup> ' + element.replace(/( *)(\[\* )(.+?)(\])/g, '$3') + '<br>';
				});
				
				return string + ret + '</small>';
			}
			else return string;
		}
		
		//Function for processing Markdownish syntax
		function markdown(string){
			string = string.replace(/(\*)((\S)(.*?)(\S)|(\S))(\*)/g, '<strong>$2</strong>');
			string = string.replace(/(\#)((\S)(.*?)(\S)|(\S))(\#)/g, '<em>$2</em>');
			string = string.replace(/(\_)((\S)(.*?)(\S)|(\S))(\_)/g, '<span style="text-decoration: underline;">$2</span>');
			string = string.replace(/(\~)((\S)(.*?)(\S)|(\S))(\~)/g, '<del>$2</del>');
			string = string.replace(/(\^)((\S)(.*?)(\S)|(\S))(\^)/g, '<sup>$2</sup>');
			string = string.replace(/(\@)((\S)(.*?)(\S)|(\S))(\@)/g, '<span style="text-decoration: overline;">$2</span>');
			string = string.replace(/(\()(.*?)(\))(\[)(\S*?)(\])/g, '<span style="color: $5;">$2</span>');
			
			string = string.replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
			
			return string;
		}
		
		function strip_html(string) {
			string = string.replace(/\<(.+?)\>(.*?)\<\/(.+?)\>/g, '$2');
			string = string.replace(/(\<)(br|hr|input|embed|img|link|meta|script)(.*?)(\>)/g, '');
			string = string.replace(/\</g, '&lt;'); string = string.replace(/\>/g, '&gt;');
			
			return string;
		}
	});
	
	//Polyfill for forEach funtion
	if (!Array.prototype.forEach) {
	
	  Array.prototype.forEach = function(callback, thisArg) {
	
		var T, k;
	
		if (this == null) {
		  throw new TypeError(' this is null or not defined');
		}
		var O = Object(this);
		var len = O.length >>> 0;
		if (typeof callback !== "function") {
		  throw new TypeError(callback + ' is not a function');
		}
		if (arguments.length > 1) {
		  T = thisArg;
		}
		k = 0;
		while (k < len) {
	
		  var kValue;
		  if (k in O) {
	
			kValue = O[k];
	
			callback.call(T, kValue, k, O);
		  }
		  k++;
		}
	  };
	}
	</script>
<?php 
###Edit manuscript###
###url: /manuscriptus-pro/edit/<corpus>/<ms>/###
elseif($the_corpus && $the_ms):
?>
	<script src="js/autogrow.min.js"></script>
	<form id="createMS" class="form" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?=new_input('text', 'MSNameNew', 'Manuscript Shelf Mark', isset($form_data['MSNameNew']) ? $form_data['MSNameNew'] : $the_ms->name, 'The manuscript shelf mark or name.', 'required');?>
				</span>
			</span>
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?php echo new_input('text', 'MSSiglumNew', 'Manuscript Siglum', isset($form_data['MSSiglumNew']) ? $form_data['MSSiglumNew'] : $the_ms->siglum, 'The manuscript siglum can be no longer than 4 characters.', 'required', false, false, 4)?>
				</span>
			</span>
		</div>
		<div class="form-group row">
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?=new_input('text', 'MSRangeNew', 'Folio Range (Optional)', isset($form_data['MSRangeNew']) ? $form_data['MSRangeNew'] : $the_ms->frange, 'The folio range of the desired text within the manuscript.', false, false, false, 20);?>
				</span>
			</span>
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?php echo new_input('text', 'MSDateNew', 'Approx. Date (Optional)', isset($form_data['MSDateNew']) ? $form_data['MSDateNew'] : $the_ms->date, 'An approximate date for the manuscript. The first character must be an integer. E.g. 1234 or 13th century.', 'pattern="^([0-9]+)(.*)$"', false, false, 30)?>
				</span>
			</span>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 input-group col-sm-offset-3">
				<?=new_input('text', 'MSId', 'Manuscript Identifier', $the_ms->id, 'The unique manuscript identifier must contain only lowercase alphanumeric characters and dashes. It cannot be changed once the manuscript has been created.', 'required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" readonly', 'manuscript-identifier', '/corpus/'.$the_corpus->id.'/', 30)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 col-sm-offset-3 input-group">
				<?=new_input('url', 'MSLinkNew', 'Link (Optional)', isset($form_data['MSLinkNew']) ? $form_data['MSLinkNew'] : $the_ms->link, 'A link to a related webpage, e.g. the library site or an image gallery.')?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 col-sm-offset-3">
				<textarea class="form-control" rows="4" id="MSDescriptionNew" name="MSDescriptionNew" placeholder="Description (Optional)"><?=isset($form_data['MSDescriptionNew']) ? $form_data['MSDescriptionNew'] : $the_ms->description?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
				<input type="hidden" name="MSCorpus" id="MSCorpus" value="<?=$the_corpus->id?>">
				<input type="hidden" name="MSId" id="MSId" value="<?=$the_ms->id?>">
				<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">Edit Manuscript</button>
				<button type="reset" class="btn btn-warning" style="margin-bottom: 5px;">Reset</button>
				<a class="btn btn-danger" style="margin-bottom: 5px;" data-toggle="modal" data-target="#deleteMS">Delete Manuscript</a>
			</div>
		</div>
	</form>
	<div class="modal fade" id="deleteMS" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Manuscript?</h4>
				</div>
				<div class="modal-body">
					Are you sure you want to delete the manuscript <b><?=$the_ms->name?></b> [<b><?=$the_ms->id?></b>]? This will permanently delete the manuscript and all of the lines in it. The data cannot be recovered unless you have a backup or an export of it.
				</div>
				<div class="modal-footer">
					<form action="<?=THE_BASE_URL?>" method="post">
						<button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
						<input type="hidden" name="deleteMSId" value="<?=$the_ms->id?>">
						<input type="hidden" name="MSCorpus" value="<?=$the_corpus->id?>">
						<button type="submit" class="btn btn-danger">Delete Manuscript</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php 
###Edit corpus###
###url: /manuscriptus-pro/edit/<corpus>/###
else:
?>
	<form id="editCorpus" class="form-horizontal" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6 input-group">
				<?=new_input('text', 'corpusNameNew', 'Corpus Name', isset($form_data['corpusNameNew']) ? $form_data['corpusNameNew'] : $the_corpus->name, 'The name of the corpus.', 'required')?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6 input-group">
				<?=new_input('text', 'corpusId', 'Corpus Identifier', $the_corpus->id, 'The unique corpus identifier must contain only lowercase alphanumeric characters and dashes. It cannot be changed once the corpus has been created.', 'required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" readonly', 'corpus-identifier', '/corpus/', 30)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6">
				<textarea class="form-control" rows="3" id="corpusDescriptionNew" name="corpusDescriptionNew" placeholder="Description (Optional)"><?=isset($form_data['corpusDescriptionNew']) ? $form_data['corpusDescriptionNew'] : $the_corpus->description?></textarea>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6">
				<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">Edit Corpus</button>
				<button type="reset" class="btn btn-warning" style="margin-bottom: 5px;">Reset</button>
				<a class="btn btn-danger" style="margin-bottom: 5px;" data-toggle="modal" data-target="#deleteCorpus">Delete Corpus</a>
			</div>
		</div>
	</form>
	<div class="modal fade" id="deleteCorpus" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Corpus?</h4>
				</div>
				<div class="modal-body">
					Are you sure you want to delete the corpus <b><?=$the_corpus->name?></b> [<b><?=$the_corpus->id?></b>]? This will permanently delete the corpus and all of the manuscripts in it. The data cannot be recovered unless you have a backup or an export of it.
				</div>
				<div class="modal-footer">
					<form action="<?=THE_BASE_URL?>" method="post">
						<button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
						<input type="hidden" name="deleteCorpusId" value="<?=$the_corpus->id?>">
						<button type="submit" class="btn btn-danger">Delete Corpus</button>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>
<script type="text/javascript">
$(document).ready(function(){
	$('[data-toggle="popover"]').popover({container: 'body', trigger: 'click'});
});
</script>
<script src="<?=THE_BASE_URL?>js/validator.min.js"></script>
<script src="<?=THE_BASE_URL?>js/autogrow.min.js"></script>
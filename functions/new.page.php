<script src="js/validator.min.js"></script>
<?php
###Add line to manuscript###
###url: /manuscriptus-pro/new/<corpus>/<ms>/###
if($the_corpus && $the_ms):

if(!$form_data['lineNumber']){
	$new_line_number = $db->get_row("SELECT number FROM ".dash2us($the_ms->id)." ORDER BY number + 0 DESC");
	if($new_line_number) $new_line_number = ++$new_line_number->number;
}
else $new_line_number = $form_data['lineNumber'];
?>
	<script src="js/autogrow.min.js"></script>
	<form id="createLine" class="form-horizontal" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<div class="col-sm-2 col-sm-offset-5 input-group">
				<?=new_input('text', 'lineNumber', 'Line Number', $new_line_number, false, 'required', false, $the_ms->siglum.'.', 7)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-12">
				<textarea class="form-control" rows="2" id="lineText" name="lineText" placeholder="Line Text" required><?=$form_data['lineText']?></textarea>
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
				<textarea class="form-control" rows="3" id="lineComments" name="lineComments" placeholder="Comments (Optional)"><?=$form_data['lineComments']?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-4">
				<input type="hidden" name="lineCorpus" id="lineCorpus" value="<?=$the_corpus->id?>">
				<input type="hidden" name="lineMS" id="lineMS" value="<?=$the_ms->id?>">
				<button type="submit" name="save" value="continue" class="btn btn-success">Save + Continue...</button>
				<button type="submit" name="save" value="stop" class="btn btn-primary">Save</button>
				<button type="reset" class="btn btn-warning">Reset</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#lineText').autogrow();
		
		$('#lineText').keyup(function(){
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
###Create new manuscript###
###url: /manuscriptus-pro/new/<corpus>/###
elseif(argument(0)):
?>
	<form id="createMS" class="form" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?=new_input('text', 'MSName', 'Manuscript Shelf Mark', $form_data['MSName'], 'The manuscript shelf mark or name.', 'required');?>
				</span>
			</span>
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?php echo new_input('text', 'MSSiglum', 'Manuscript Siglum', $form_data['MSSiglum'], 'The manuscript siglum can be no longer than 4 characters.', 'required', false, false, 4)?>
				</span>
			</span>
		</div>
		<div class="form-group row">
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?=new_input('text', 'MSRange', 'Folio Range (Optional)', $form_data['MSRange'], 'The folio range of the desired text within the manuscript.', false, false, false, 20);?>
				</span>
			</span>
			<span class="form-group form-inline">
				<span class="col-sm-3 input-group">
					<?php echo new_input('text', 'MSDate', 'Approx. Date (Optional)', $form_data['MSDate'], 'An approximate date for the manuscript. The first character must be an integer. E.g. 1234 or 13th century.', 'pattern="^([0-9]+)(.*)$"', false, false, 30)?>
				</span>
			</span>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 input-group col-sm-offset-3">
				<?=new_input('text', 'MSId', 'Manuscript Identifier', $form_data['MSId'], 'The unique manuscript identifier must contain only lowercase alphanumeric characters and dashes. It cannot be changed once the manuscript has been created.', 'required pattern="^[a-z0-9]+(-[a-z0-9]+)*$"', 'manuscript-identifier', '/corpus/'.$the_corpus->id.'/', 30)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 col-sm-offset-3 input-group">
				<?=new_input('url', 'MSLink', 'Link (Optional)', $form_data['MSLink'], 'A link to a related webpage, e.g. the library site or an image gallery.')?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-6 col-sm-offset-3">
				<textarea class="form-control" rows="4" id="MSDescription" name="MSDescription" placeholder="Description (Optional)"><?=$form_data['MSDescription']?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-sm-4">
				<input type="hidden" name="MSCorpus" id="MSCorpus" value="<?=$the_corpus->id?>">
				<button type="submit" class="btn btn-primary">Create Manuscript</button>
				<button type="reset" class="btn btn-warning">Reset</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		var userEdited = 0; //Tracks if user has edited id field
		
		$('#MSId').keydown(function(){
			userEdited = 1;
		});
		
		//If user has not edited id field, suggest id value
		//based on name
		$('#MSName').keyup(function(){
			var name = $(this).val();
			if(!userEdited) $('#MSId').val(formatUrl(name));
		});
		
		$('form').on('reset', function(){
		    userEdited = 0;
		});
	});
	</script>
<?php
###Else create corpus###
###url: /manuscriptus-pro/new/###
else:
?>
	<form id="createCorpus" class="form-horizontal" method="post" action="<?=THE_BASE_URL?>" data-toggle="validator">
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6 input-group">
				<?=new_input('text', 'corpusName', 'Corpus Name', $form_data['corpusName'], 'The name of the corpus.', 'required')?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6 input-group">
				<?=new_input('text', 'corpusId', 'Corpus Identifier', $form_data['corpusId'], 'The unique corpus identifier must contain only lowercase alphanumeric characters and dashes. It cannot be changed once the corpus has been created.', 'pattern="^[a-z0-9]+(-[a-z0-9]+)*$" required', 'corpus-identifier', '/corpus/', 30)?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6">
				<textarea class="form-control" rows="3" id="corpusDescription" name="corpusDescription" placeholder="Description (Optional)"><?=$form_data['corpusDescription']?></textarea>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-6">
				<button type="submit" class="btn btn-primary">Create Corpus</button>
				<button type="reset" class="btn btn-warning">Reset</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		var userEdited = 0; //Tracks if user has edited id field
		
		$('#corpusId').keydown(function(){
			userEdited = 1;
		});
		
		//If user has not edited id field, suggest id value
		//based on name
		$('#corpusName').keyup(function(){
			var name = $(this).val();
			if(!userEdited) $('#corpusId').val(formatUrl(name));
		});
		
		$('form').on('reset', function(){
		    userEdited = 0;
		});
	});
	</script>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function(){
	$('[data-toggle="popover"]').popover({container: 'body', trigger: 'click'});
});
function formatUrl(theUrl){
	return theUrl.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g,  '-').replace(/^-|-$/g, '');
}
</script>
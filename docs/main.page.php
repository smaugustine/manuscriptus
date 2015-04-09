<ul class="breadcrumb">
	<li><a href="<?=THE_BASE_URL?>">Manuscriptus Pro</a></li>
	<li class="active">Help</li>
</ul>
<h1>Manuscriptus Pro Help</h1>
<div class="row">
	<div class="col-sm-3" style="padding-top: 50px;">
		<ul class="nav nav-pills nav-stacked">
			<li class="active"><a href="help/">Overview</a></li>
			<li><a href="help/#add">Adding Content</a></li>		
			<li><a href="help/#edit">Editing & Deleting Content</a></li>
			<li><a href="help/#markdown">Markdown</a></li>
			<li><a href="help/#errors">Error Messages</a></li>
		</ul>
	</div>
	<div id="add" class="col-sm-9 help-content">
		<h2>Adding Content</h2>
		<p>Content (corpuses, manuscripts, and lines) may be added from the <b>File</b> menu. Note that you need to have a corpus open to be able to add a manuscript and you need to have a manuscript open to be able to add lines.</p>
		<p>There is a form for adding each type of content. Information about what is required for each field can be found by clicking the "i" next to the field. The following text may be formatted using <b>Markdown</b>: corpus description, manuscript description, line text, and line comments. HTML is not permitted in any field. Note that footnotes can be added to line text using Markdown syntax.</p>
		<p>When adding lines, there are two buttons for saving the content: <b>Save</b> and <b>Save & Continue...</b>. The <b>Save</b> button will simply save the content and return to the manuscript. The <b>Save & Continue...</b> button will save the content and then open a new form for adding another line.</p>
		<div class="alert alert-warning">
			Note that identifiers for manuscripts and corpuses can only contain <b>lowercase Latin alphanumeric characters and dashes</b>. They cannot start or end with a dash. It also has to be <b>unique</b> within Manuscriptus Pro. It is possible to use your own identifier instead of the suggested one, but must follow those requirements. <b>It is not possible to change an identifier after the corpus or manuscript has been created.</b>
		</div>
	</div>
	<div id="edit" class="col-sm-9 help-content">
		<h2>Editing & Deleting Content</h2>
		<p>Any open corpus or manuscript can be edited from the <b>Edit</b> menu. Any line can be edited by clicking the <span class="glyphicon glyphicon-pencil"></span> button next to it.</p>
		<p>Corpuses and manuscripts can be deleted from the edit page by clicking the <b>Delete</b> button. A Line can be deleted by clicking the <span class="glyphicon glyphicon-trash"></span> button next to it (it cannot be deleted from the edit page).
		<div class="alert alert-danger">
			Deletions are <b>permanent</b>. They cannot be undone and the deleted data cannot be recovered. It is recommended to keep regular backups and/or exports to ensure that important data is not lost.
		</div>
	</div>
	<div id="markdown" class="col-sm-9 help-content">
		<h2>Markdown</h2>
		<p>In order to format text in Manuscriptus Pro it is necessary to use a syntax called Markdown. Manuscriptus Pro does not use the standard flavours of Markdown (e.g. John Gruber's or GitHub's), but rather a modified flavour that is aimed at the sort of formatting that is more useful for manuscript transcription.</p>
		<h3>Basic Formatting</h3>
		<table class="table" style="width: auto;">
			<tr><th>Syntax</th><th>Result</th></tr>
			<tr><td><code>*Bolded Text*</code></td><td><b>Bolded Text</b></td></tr>
			<tr><td><code>#Italicized Text#</code></td><td><i>Italicized Text</i></td></tr>
			<tr><td><code>_Underlined Text_</code></td><td><u>Underlined Text</u></td></tr>
			<tr><td><code>@Overlined Text@</code></td><td><span style="text-decoration: overline;">Overlined Text</span></td></tr>
			<tr><td><code>~Struckthrough Text~</code></td><td><s>Struckthrough Text</s></td></tr>
			<tr><td><code>^Superscript Text^</code></td><td><sup>Superscript Text</sup></td></tr>
			<tr><td><code>(Coloured Text)[blue]</code></td><td><span style="color: blue;">Coloured Text</span></td></tr>
		</table>
		<h3>Nested Formatting</h3>
		<p>The above rules for formatting text can be nested, combining the effects. Some examples:</p>
		<table class="table" style="width: auto;">
			<tr><th>Syntax</th><th>Result</th></tr>
			<tr><td><code>*#Bold, Italicized Text#*</code></td><td><b><i>Bold, Italicized Text</i></b></td></tr>
			<tr><td><code>(_Underlined Green Text_)[green]</code></td><td><span style="color: green;"><u>Underlined Green Text</u></span></td></tr>
			<tr><td><code>@_Overlined & Underlined_@</code></td><td><span style="text-decoration: overline;"><u>Overlined & Underlined</u></span></td></tr>
		</table>
		<h3>Footnotes</h3>
		<p>The Markdown syntax for adding footnotes is: <code style="white-space: nowrap;">[* This is a footnote]</code>. An example of the syntax in use: <code style="white-space: nowrap;">This is some text[* This is a footnote]. This is some more text.</code> Note that footnotes may contain Markdown formatting such as bold, italics, etc. For a footnote to be formatted correctly, the asterisk must be followed by a space. It does not matter whether or not the footnote itself is separated from the body of text by a space(s).
	</div>
	<div id="errors" class="col-sm-9 help-content">
		<h2>Error Messages</h2>
		<h4>No [x] with the identifier [y] could be found.</h4>
		<p>One of the identifiers given in the URL (the parts after /view/ or /edit/) does not match any identifiers in the database.</p>
		<h4>A [x] with the name [y] already exists.</h4>
		<p>This is simply a suggestion, since having multiple items with the same name can be confusing. It will not affect any functions, since those use the unique identifiers.</p>
		<h4>A [x] with the identifier [y] already exists.</h4>
		<p>Identifiers must be unique. For corpuses, no two corpuses may have the same identifier. Likewise, no two manuscripts, even if they are in different corpuses, may have the same identifier.</p>
		<h4>[x] is a reserved word and cannot be used as an identifier.</h4>
		<p>Some words are used for certain Manuscriptus Pro functions. In order for these functions to work properly, the required words are not allowed to be used as identifiers. They may still be used as names.</p>
		<h4>The use of HTML is not permitted.</h4>
		<p>HTML cannot be used in any field in Manuscriptus Pro. Only the provided Markdown syntax may be used. Its use is limited to the corpus description, manuscript description, line text, and line comments fields. This warning will not prevent the form from being processed, but all HTML within the text will either be stripped or neutralized.</p>
		<h4>A form was submitted but no action was taken.</h4>
		<p>This error is triggered when the POST header has not been emptied. Once a form is processed, the POST header should be emptied and an alert (either success or error) triggered. The result of this error is that, although a form was submitted, it was not processed and no action was taken.</p>
		<h4>The [x] could not be [created/edited/deleted] due to a database error.</h4>
		<p>The database returned an error after it was queried. This is an unspecified error. It is not related to the database configuration, but is instead entirely dependent on the syntax of the query. Try removing any punctuation, symbols, and non-Latin characters from all fields and ensure that no code is being injected. If the issue persists, submit an issue report through GitHub.</p>
		<h4>Due to a database error, a manuscript was only partially deleted.</h4>
		<p>This error is similar to the one above. Although the manuscript was deleted from the table of manuscripts, the table which contained the lines of the manuscript was not deleted. Although this is not a visible issue, it means that there is an orphaned table in the database that will still use storage resources and can still be accessed through control panels.</p>
		<h4>A manuscript in the corpus could not be deleted due to a database error.</h4>
		<p>Another error similar to the above. It means that, although the corpus was deleted from the table of corpuses, a manuscript within the corpus was not deleted from the table of manuscripts. It also means that the table for the manuscript was not deleted. Although this is not a visible issue, it means that there are orphaned rows and tables in the database. These rows and tables, although inaccessible through the program, can still be accessed using a control panel and will still use up storage resources.</p>
		<h4>[x] is not a registered namespace.</h4>
		<p>Namespaces are the first part of the URL after the Manuscriptus Pro directory. The default namespaces are view, new, edit, compare, import, export, backup, help, faq, and about. Plugins are also able to register their own namespaces. Attempting to open a page with a namespace that is neither one of the default namespaces nor one of the namespaces registered by a plugin results in this error, similar to a 404 error from a website.</p>
	</div>
	<div id="overview" class="col-sm-9 help-content">
		<h2>Overview</h2>
		<p>Manuscriptus Pro is a web-based application designed for storing and analyzing manuscript corpuses.</p>
		
		<h3>How it works</h3>
		<p>Manuscriptus Pro has three underlying structures: <b>lines</b>, <b>manuscripts</b>, and <b>corpuses</b>. <b>Lines</b> are numbered lines of formatted text which can also have footnotes and comments. <b>Manuscripts</b> are collections of lines. They have a unique identifier as well as a siglum that is unique to the manuscript's corpus. <b>Corpuses</b> are collections of manuscripts. They also have unique identifiers.
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.nav-pills li').click(function(){
		$('.nav-pills li').removeClass('active');
		$(this).addClass('active');
	});
	
	var url = window.location.toString();
	url = url.substring(url.indexOf('help/'));
	$('.nav-pills li a').each(function(){
		var theHref = $(this).attr('href');
		if(url == theHref) {
			$('.nav-pills li').removeClass('active');
			$(this).closest("li").addClass('active');
		}
	});
});
</script>
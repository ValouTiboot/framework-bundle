/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import './css/style.scss';

// import $ from "jquery";
window.$ = window.jQuery = require("jquery");
global.$ = global.jQuery = jQuery = $;
require('bootstrap');
// import 'bootstrap/dist/js/bootstrap.min';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/widgets/draggable';
import Tagify from '@yaireo/tagify';
import { initTranslationEditor } from './translation';

// Import TinyMCE
import tinymce from 'tinymce';
// Default icons are required for TinyMCE 5.3 or above
import 'tinymce/icons/default/icons.min';
// A theme is also required
import 'tinymce/themes/silver/theme.min';
// Any plugins you want to use has to be imported
import 'tinymce/plugins/print/plugin.min';
import 'tinymce/plugins/preview/plugin.min';
import 'tinymce/plugins/paste/plugin.min';
import 'tinymce/plugins/importcss/plugin.min';
import 'tinymce/plugins/searchreplace/plugin.min';
import 'tinymce/plugins/autolink/plugin.min';
import 'tinymce/plugins/autosave/plugin.min';
import 'tinymce/plugins/save/plugin.min';
import 'tinymce/plugins/directionality/plugin.min';
import 'tinymce/plugins/code/plugin.min';
import 'tinymce/plugins/visualblocks/plugin.min';
import 'tinymce/plugins/visualchars/plugin.min';
import 'tinymce/plugins/fullscreen/plugin.min';
import 'tinymce/plugins/image/plugin.min';
import 'tinymce/plugins/link/plugin.min';
import 'tinymce/plugins/media/plugin.min';
import 'tinymce/plugins/template/plugin.min';
import 'tinymce/plugins/codesample/plugin.min';
import 'tinymce/plugins/table/plugin.min';
import 'tinymce/plugins/charmap/plugin.min';
import 'tinymce/plugins/hr/plugin.min';
import 'tinymce/plugins/pagebreak/plugin.min';
import 'tinymce/plugins/nonbreaking/plugin.min';
import 'tinymce/plugins/anchor/plugin.min';
import 'tinymce/plugins/toc/plugin.min';
import 'tinymce/plugins/insertdatetime/plugin.min';
import 'tinymce/plugins/advlist/plugin.min';
import 'tinymce/plugins/lists/plugin.min';
import 'tinymce/plugins/wordcount/plugin.min';
import 'tinymce/plugins/imagetools/plugin.min';
import 'tinymce/plugins/textpattern/plugin.min';
import 'tinymce/plugins/noneditable/plugin.min';
import 'tinymce/plugins/help/plugin.min';
import 'tinymce/plugins/charmap/plugin.min';
import 'tinymce/plugins/quickbars/plugin.min';
import 'tinymce/plugins/emoticons/plugin.min';
import 'tinymce/plugins/emoticons/js/emojiimages.min';
import 'tinymce/plugins/emoticons/js/emojis.min';

console.log('Hello Webpack Encore! Edit me in asset/admin/_dev/admin.js');

$(document).ready(function(){

	// translation editor (inline edition), first: it must not depend on the rest of this callback
	initTranslationEditor();

	// Menu
	$('.menu-items a[href^="#"]').click(function(e){
		e.preventDefault();
		$('.menu-items a[href^="#"]').removeClass('expand').parent().find('.sub-menu').slideUp('fast');

		if ($(this).parent().find('.sub-menu').is(':visible'))
			$(this).removeClass('expand').parent().find('.sub-menu').slideUp('fast');
		else
			$(this).addClass('expand').parent().find('.sub-menu').slideDown('fast');
	});

	// Tagify
	$('.tag').each(function(){
		var input = $(this);
		new Tagify(input[0], {
			pattern : /^.{0,30}$/,
		});
	});

	// DGTX-switch
	$('.dgtx-switch').each(function(){
		var check = $('input[type="radio"]:checked', $(this));

		if (typeof check !== 'undefined' && check.val() == 1) {
			$(this).addClass('dgtx-switch-yes');
			check.next().addClass('checked');
		} else {
			$(this).addClass('dgtx-switch-no');
			$('input[value="0"]', $(this)).attr('checked','checked').next().addClass('checked');
		}
	});

	$('.dgtx-switch label').on('click', function(){

		var parent = $(this).parent().parent();

		$('label', parent).removeClass('checked');
		$(this).addClass('checked');

		if (parent.hasClass('dgtx-switch-no'))
			parent.removeClass('dgtx-switch-no').addClass('dgtx-switch-yes');
		else
			parent.removeClass('dgtx-switch-yes').addClass('dgtx-switch-no');
	});

	// FriendlyUrl
	$('input[id*="translatableRewrite"]').on('keyup blur', function(){
		$(this).val(toRewriteUrl($(this).val()));
	});

	$('input[id*="translatableName"]').on('blur', function(){
		var associateRewriteFieldId = $(this).prop('id').replace('Name', 'Rewrite');
		var associateRewriteField = $('#'+associateRewriteFieldId);

		if (associateRewriteField.val() == '')
			associateRewriteField.val(toRewriteUrl($(this).val()));
	});

	$('.item-delete').on('click', function(e){
		if (!confirm("Confirm deletion?"))
			e.preventDefault();
	});

	// sortable
	if ($.fn.sortable) {
		$('table.sortable tbody').sortable({
			handle:'.sortable-item',
			items:'tr',
			axis: 'y',
			update: function(event,ui) {
				$.post($('table.sortable').data('sortable-action'), $(this).sortable("serialize"));
			}
		});
	}

	// copyright
	$(window).on('scroll', showfooter);
	showfooter();

	// TinyMce
	tinymce.init({
		selector: 'textarea.tinymce',
		plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
		imagetools_cors_hosts: ['picsum.photos'],
		menubar: 'file edit view insert format tools table help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
		toolbar_sticky: true,
		autosave_ask_before_unload: true,
		autosave_interval: '30s',
		autosave_prefix: '{path}{query}-{id}-',
		autosave_restore_when_empty: false,
		autosave_retention: '2m',
		image_advtab: true,
		link_list: [],
		image_list: [],
		image_class_list: [],
		importcss_append: true,
		relative_urls: true,
		image_title: true,
  		automatic_uploads: true,
		file_picker_callback: function (cb, value, meta) {
		    var input = document.createElement('input');
		    input.setAttribute('type', 'file');
		    // input.setAttribute('accept', 'image/*');

		    /*
		      Note: In modern browsers input[type="file"] is functional without
		      even adding it to the DOM, but that might not be the case in some older
		      or quirky browsers like IE, so you might want to add it to the DOM
		      just in case, and visually hide it. And do not forget do remove it
		      once you do not need it anymore.
		    */

		    input.onchange = function () {
		      var file = this.files[0];

		      var reader = new FileReader();
		      reader.onload = function () {
		        /*
		          Note: Now we need to register the blob in TinyMCEs image blob
		          registry. In the next release this part hopefully won't be
		          necessary, as we are looking to handle it internally.
		        */
		        var id = 'blobid' + (new Date()).getTime();
		        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
		        var base64 = reader.result.split(',')[1];
		        var blobInfo = blobCache.create(id, file, base64);
		        blobCache.add(blobInfo);

		        /* call the callback and populate the Title field with the file name */
		        cb(blobInfo.blobUri(), { title: file.name });
		      };
		      reader.readAsDataURL(file);
		    };

		    input.click();
		},
		templates: [
		    { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
			{ title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
			{ title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
		],
		template_cdate_format: '[Date Created (CDATE): %d/%m/%Y : %H:%M:%S]',
		template_mdate_format: '[Date Modified (MDATE): %d/%m/%Y : %H:%M:%S]',
		height: 500,
		image_caption: true,
		quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		noneditable_noneditable_class: 'mceNonEditable',
		toolbar_mode: 'sliding',
		contextmenu: 'link image imagetools table',
		skin: false,
		content_css: false,
		// content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
	});

});


function toRewriteUrl(str) {
	var encodedUrl = str.toString().toLowerCase(); // make the url lowercase
	encodedUrl = encodedUrl.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
	encodedUrl = encodedUrl.split(/\&+/).join("-and-"); // replace & with and
	encodedUrl = encodedUrl.split(/[^a-z0-9]/).join("-"); // remove invalid characters
	encodedUrl = encodedUrl.split(/-+/).join("-"); // remove duplicates
	encodedUrl = encodedUrl.trim('-'); // trim leading & trailing characters

	return encodedUrl;
}

function showfooter() {
	var tHeight = $('body').height();
	var currentHeight = $(window).height()+$(window).scrollTop()+10;

	if (currentHeight > tHeight)
		$('#footer').addClass('open');
	else if ($('#footer').hasClass('open'))
		$('#footer').removeClass('open');

}

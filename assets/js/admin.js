/*
 * Admin theme entry point: jQuery + Bootstrap, Tagify, SortableJS, the page
 * modules (translation editor, menu builder, notifications) and TinyMCE
 * loaded on demand (editor.js) for the pages that have a rich text field.
 */
window.$ = window.jQuery = require('jquery');
global.$ = global.jQuery = jQuery = $;
require('bootstrap');
import Tagify from '@yaireo/tagify';
import Sortable from 'sortablejs';
import './notify';
import { notify } from './notify';
import { initTranslationEditor } from './translation';
import { initMenuBuilder } from './menu-builder';

// where webpack loads the on-demand chunks from (the layout exposes the assets base URL)
if (document.body && document.body.dataset.assets) {
  __webpack_public_path__ = document.body.dataset.assets.replace(/\/?$/, '/');
}

$(document).ready(function(){

  // page modules first: they must not depend on the rest of this callback
  initTranslationEditor();
  initMenuBuilder();

  // rich text editors, only when the page has one
  if (document.querySelector('textarea.tinymce')) {
    import(/* webpackChunkName: "editor" */ './editor').then(function(editor){ editor.initEditors(); });
  }

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

  // permission matrix: a column header ticks the whole column, "all" dims the rest of its row
  $('.dgtx-permissions').on('change', '[data-permission-column]', function(){
    $(this).closest('table').find('tbody input[data-permission="' + $(this).data('permission-column') + '"]')
      .prop('checked', this.checked).trigger('change');
  });
  $('.dgtx-permissions').on('change', 'tbody input[data-permission]', function(){
    var row = $(this).closest('tr');
    row.toggleClass('is-all', row.find('input[data-permission="all"]').prop('checked'));
  });

  // small screens: the sidebar slides in
  $('[data-sidebar-toggle]').on('click', function(){
    $('body').toggleClass('dgtx-sidebar-open');
  });

  // sortable lists: drag a row by its handle, the new order is posted as "{entity}[]=id"
  document.querySelectorAll('table.sortable tbody').forEach(function(tbody){
    var table = tbody.closest('table');

    Sortable.create(tbody, {
      handle: '.sortable-item',
      animation: 150,
      onEnd: function(){
        var body = new URLSearchParams();
        body.append('_token', table.dataset.sortableToken || '');
        tbody.querySelectorAll('tr[id]').forEach(function(row){
          var at = row.id.lastIndexOf('_');
          body.append(row.id.slice(0, at) + '[]', row.id.slice(at + 1));
        });

        fetch(table.dataset.sortableAction, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          body: body,
        })
          .then(function(response){ return response.json().then(function(data){ return { ok: response.ok && data.success, data: data }; }); })
          .then(function(result){
            if (!result.ok) {
              throw new Error(result.data.error || table.dataset.sortableError || 'Error');
            }
            notify(table.dataset.sortableSaved || 'OK', 'success', 2000);
          })
          .catch(function(error){ notify(error.message, 'error'); });
      },
    });
  });

});


function toRewriteUrl(str) {
  var encodedUrl = str.toString().toLowerCase(); // make the url lowercase
  encodedUrl = encodedUrl.normalize("NFD").replace(/[̀-ͯ]/g, "");
  encodedUrl = encodedUrl.split(/\&+/).join("-and-"); // replace & with and
  encodedUrl = encodedUrl.split(/[^a-z0-9]/).join("-"); // remove invalid characters
  encodedUrl = encodedUrl.split(/-+/).join("-"); // remove duplicates
  encodedUrl = encodedUrl.trim('-'); // trim leading & trailing characters

  return encodedUrl;
}

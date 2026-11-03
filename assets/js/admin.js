/*
 * Admin theme entry point: jQuery + Bootstrap, Tagify, SortableJS, the page
 * modules (translation editor, menu builder, notifications) and TinyMCE
 * loaded on demand (editor.js) for the pages that have a rich text field.
 */
import $ from 'jquery';
import 'bootstrap';
import Tagify from '@yaireo/tagify';
import Sortable from 'sortablejs';
import './notify';
import { notify } from './notify';
import { initTranslationEditor } from './translation';
import { initMenuBuilder } from './menu-builder';
import { initThemeSwitch } from './theme';

// jQuery exposed for the project scripts and inline snippets
window.$ = window.jQuery = $;

// where webpack loads the on-demand chunks from (the layout exposes the assets base URL)
if (document.body && document.body.dataset.assets) {
  __webpack_public_path__ = document.body.dataset.assets.replace(/\/?$/, '/');
}

$(document).ready(function(){

  // page modules first: they must not depend on the rest of this callback
  initTranslationEditor();
  initMenuBuilder();
  initThemeSwitch();

  // rich text editors, only when the page has one
  if (document.querySelector('textarea.tinymce')) {
    import(/* webpackChunkName: "editor" */ './editor').then(function(editor){ editor.initEditors(); });
  }

  // Menu
  $('.menu-items a[href^="#"]').click(function(e){
    e.preventDefault();
    $('.menu-items a[href^="#"]').removeClass('expand').parent().find('.sub-menu').slideUp('fast');

    if ($(this).parent().find('.sub-menu').is(':visible')) {
      $(this).removeClass('expand').parent().find('.sub-menu').slideUp('fast');
    } else {
      $(this).addClass('expand').parent().find('.sub-menu').slideDown('fast');
    }
  });

  // Tagify: the field keeps a plain comma separated list ("Symfony, PHP"), not Tagify's JSON
  $('.tag').each(function(){
    new Tagify(this, {
      pattern : /^.{0,30}$/,
      originalInputValueFormat: values => values.map(item => item.value).join(', '),
    });
  });

  // DGTX-switch
  $('.dgtx-switch').each(function(){
    const check = $('input[type="radio"]:checked', $(this));

    if (check.length && String(check.val()) === '1') {
      $(this).addClass('dgtx-switch-yes');
      check.next().addClass('checked');
    } else {
      $(this).addClass('dgtx-switch-no');
      $('input[value="0"]', $(this)).attr('checked','checked').next().addClass('checked');
    }
  });

  $('.dgtx-switch label').on('click', function(){

    const parent = $(this).parent().parent();

    $('label', parent).removeClass('checked');
    $(this).addClass('checked');

    if (parent.hasClass('dgtx-switch-no')) {
      parent.removeClass('dgtx-switch-no').addClass('dgtx-switch-yes');
    } else {
      parent.removeClass('dgtx-switch-yes').addClass('dgtx-switch-no');
    }
  });

  // FriendlyUrl
  $('input[id*="translatableRewrite"]').on('keyup blur', function(){
    $(this).val(toRewriteUrl($(this).val()));
  });

  // short codes (menus): same normalisation, with underscores
  $('input[id$="_shortCode"]').on('keyup blur', function(){
    $(this).val(toRewriteUrl($(this).val(), false));
  });

  $('input[id*="translatableName"]').on('blur', function(){
    const associateRewriteFieldId = $(this).prop('id').replace('Name', 'Rewrite');
    const associateRewriteField = $('#'+associateRewriteFieldId);

    if (associateRewriteField.val() === '') {
      associateRewriteField.val(toRewriteUrl($(this).val()));
    }
  });

  // permission matrix: a column header ticks the whole column, "all" dims the rest of its row
  $('.dgtx-permissions').on('change', '[data-permission-column]', function(){
    $(this).closest('table').find('tbody input[data-permission="' + $(this).data('permission-column') + '"]')
      .prop('checked', this.checked).trigger('change');
  });

  $('.dgtx-permissions').on('change', 'tbody input[data-permission]', function(){
    const row = $(this).closest('tr');
    row.toggleClass('is-all', row.find('input[data-permission="all"]').prop('checked'));
  });

  // small screens: the sidebar slides in
  $('[data-sidebar-toggle]').on('click', function(){
    $('body').toggleClass('dgtx-sidebar-open');
  });

  // embedded entries ("embed" fields): add from the prototype, remove, drag to reorder
  document.querySelectorAll('[data-embed]').forEach(initEmbed);

  // sortable lists: drag a row by its handle, the new order is posted as "{entity}[]=id"
  document.querySelectorAll('table.sortable tbody').forEach(function(tbody){
    const table = tbody.closest('table');

    Sortable.create(tbody, {
      handle: '.sortable-item',
      animation: 150,
      onEnd: function(){
        const body = new URLSearchParams();
        body.append('_token', table.dataset.sortableToken || '');
        tbody.querySelectorAll('tr[id]').forEach(function(row){
          const at = row.id.lastIndexOf('_');
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

// An "embed" field (EmbedFieldType). The entries are renumbered 0..n in the
// displayed order after each change: the form maps submitted keys to the
// existing records in order, so the values follow the new order.
function initEmbed(embed) {
  const list = embed.querySelector('[data-embed-list]');
  const prefix = embed.dataset.embedPrefix;
  const idPrefix = prefix.replace(/\]\[|\[|\]/g, '_').replace(/_$/, '');

  const renumber = function(){
    list.querySelectorAll(':scope > .dgtx-embed-entry').forEach(function(entry, index){
      entry.querySelectorAll('[name^="' + prefix + '["]').forEach(function(input){
        input.name = input.name.replace(new RegExp('^' + escapeRegExp(prefix) + '\\[[^\\]]+\\]'), prefix + '[' + index + ']');
      });
      entry.querySelectorAll('[id^="' + idPrefix + '_"]').forEach(function(element){
        element.id = element.id.replace(new RegExp('^' + escapeRegExp(idPrefix) + '_[^_]+'), idPrefix + '_' + index);
      });
    });
  };

  embed.querySelector('[data-embed-add]').addEventListener('click', function(){
    const template = document.createElement('template');
    template.innerHTML = embed.dataset.prototype.replace(/__name__/g, String(list.children.length)).trim();
    const entry = template.content.firstElementChild;
    list.appendChild(entry);
    renumber();
    entry.querySelector('input, textarea, select')?.focus();
  });

  list.addEventListener('click', function(e){
    const remove = e.target.closest('[data-embed-remove]');
    if (remove) {
      remove.closest('.dgtx-embed-entry').remove();
      renumber();
    }
  });

  Sortable.create(list, {
    handle: '.dgtx-embed-handle',
    animation: 150,
    onEnd: renumber,
  });
}

function escapeRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Lower case ASCII letters and digits, separated by dashes (rewrites) or,
// with dash = false, by underscores (short codes).
function toRewriteUrl(str, dash = true) {
  const sep = dash ? '-' : '_';

  return str.toString().toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, '') // strip the accents
    .replace(/&+/g, sep + 'and' + sep)
    .replace(/[^a-z0-9]+/g, sep) // invalid characters and runs of them become one separator
    .replace(new RegExp('^' + sep + '+|' + sep + '+$', 'g'), ''); // no leading or trailing separator
}

/*
 * Menu builder (templates/admin/helper/view/menu/view.html.twig).
 *
 * The tree is a flat, depth-first list: every <li.dgtx-menu-item> carries its
 * level in data-depth (and the --depth CSS variable). Dragging vertically
 * changes the order, dragging horizontally changes the level, bounded by the
 * previous item (at most one level deeper) and by the configured maximum;
 * the sub-items of a dragged item follow it. Indent / outdent buttons do the
 * same without a mouse. One "Save" posts the whole tree as JSON.
 */
import Sortable from 'sortablejs';
import { notify } from './notify';

const ICONS = { route: 'description', cms: 'article', link: 'link' };

export function initMenuBuilder() {
  const root = document.getElementById('dgtx-menu-builder');
  if (!root) {
    return;
  }

  const config = JSON.parse(root.dataset.config);
  const labels = config.labels;
  const tree = root.querySelector('.dgtx-menu-root');
  const saveButton = document.querySelector('[data-save]');
  const status = document.querySelector('[data-status]');
  const countElement = document.querySelector('[data-item-count]');
  const indentWidth = parseFloat(getComputedStyle(root).getPropertyValue('--dgtx-menu-indent')) || 36;
  let dirty = false;
  let sequence = 0;
  let drag = null; // {startDepth, startX, height, descendants, fromSource}
  let pointerX = 0;

  // --- flat tree helpers ------------------------------------------------------------

  const isItem = (node) => Boolean(node) && node.nodeType === 1 && node.matches('li.dgtx-menu-item');
  const depthOf = (li) => parseInt(li.dataset.depth || '0', 10);
  const items = () => Array.from(tree.children).filter(isItem);

  function setDepth(li, depth) {
    li.dataset.depth = depth;
    li.style.setProperty('--depth', depth);
    li.classList.toggle('is-nested', depth > 0);
  }

  /** Following items that are deeper than li: its sub-tree. */
  function descendants(li) {
    const out = [];
    const depth = depthOf(li);
    let node = li.nextElementSibling;
    while (isItem(node) && depthOf(node) > depth) {
      out.push(node);
      node = node.nextElementSibling;
    }
    return out;
  }

  const block = (li) => [li, ...descendants(li)];

  /** Number of levels used by the item and its sub-tree. */
  function blockHeight(li) {
    let deepest = depthOf(li);
    descendants(li).forEach((node) => { deepest = Math.max(deepest, depthOf(node)); });
    return deepest - depthOf(li) + 1;
  }

  function visibleBefore(node, skip) {
    let current = node.previousElementSibling;
    while (current && (!isItem(current) || current.hidden || current === skip)) {
      current = current.previousElementSibling;
    }
    return current;
  }

  function visibleAfter(node, skip) {
    let current = node.nextElementSibling;
    while (current && (!isItem(current) || current.hidden || current === skip)) {
      current = current.nextElementSibling;
    }
    return current;
  }

  /** Levels allowed for a block of the given height placed between prev and next, or null. */
  function allowedRange(prev, next, height) {
    const max = Math.min((prev ? depthOf(prev) : -1) + 1, config.maxDepth - height);
    const min = Math.max(0, (next ? depthOf(next) : 0) - 1);
    return min > max ? null : { min, max };
  }

  function clamp(value, range) {
    return Math.min(range.max, Math.max(range.min, value));
  }

  // --- drag & drop ------------------------------------------------------------------

  /**
   * Applies the level wanted by the pointer to the placeholder, within what
   * its neighbours allow. Returns false when the position itself is invalid.
   */
  function updateDepth(ghost, related, willInsertAfter) {
    if (!drag) {
      return true;
    }

    let prev;
    let next;
    if (related && related !== ghost && isItem(related)) {
      prev = willInsertAfter ? related : visibleBefore(related, ghost);
      next = willInsertAfter ? visibleAfter(related, ghost) : related;
    } else {
      prev = visibleBefore(ghost, ghost);
      next = visibleAfter(ghost, ghost);
    }

    const range = allowedRange(prev, next, drag.height);
    if (!range) {
      return false;
    }

    const wanted = drag.fromSource
      ? Math.round((pointerX - tree.getBoundingClientRect().left - indentWidth / 2) / indentWidth)
      : drag.startDepth + Math.round((pointerX - drag.startX) / indentWidth);

    setDepth(ghost, clamp(wanted, range));
    return true;
  }

  function startDrag(li, fromSource) {
    drag = {
      fromSource,
      startDepth: fromSource ? 0 : depthOf(li),
      startX: pointerX,
      height: fromSource ? 1 : blockHeight(li),
      descendants: fromSource ? [] : descendants(li),
    };
    drag.descendants.forEach((node) => { node.hidden = true; });
    root.classList.add('is-dragging');
  }

  function finishDrag(li) {
    if (!drag) {
      return;
    }
    const range = allowedRange(visibleBefore(li, li), visibleAfter(li, li), drag.height) || { min: 0, max: 0 };
    const depth = clamp(depthOf(li), range);
    const delta = depth - drag.startDepth;
    setDepth(li, depth);

    let anchor = li;
    drag.descendants.forEach((node) => {
      anchor.after(node);
      anchor = node;
      node.hidden = false;
      setDepth(node, depthOf(node) + delta);
    });

    drag = null;
    root.classList.remove('is-dragging');
    refreshEmptyState();
  }

  document.addEventListener('pointerdown', (event) => { pointerX = event.clientX; }, true);
  document.addEventListener('pointermove', (event) => {
    pointerX = event.clientX;
    if (drag) {
      const ghost = tree.querySelector(':scope > .dgtx-menu-ghost');
      if (ghost) {
        updateDepth(ghost, null, false);
      }
    }
  });

  Sortable.create(tree, {
    group: 'dgtx-menu',
    handle: '.dgtx-menu-handle',
    animation: 120,
    forceFallback: true,
    fallbackOnBody: true,
    fallbackTolerance: 4,
    ghostClass: 'dgtx-menu-ghost',
    chosenClass: 'dgtx-menu-chosen',
    dragClass: 'dgtx-menu-drag',
    onStart(event) {
      startDrag(event.item, false);
    },
    onMove(event) {
      return event.to !== tree || updateDepth(event.dragged, event.related, event.willInsertAfter);
    },
    onAdd(event) {
      // dropped from the sources: turn the clone into a real item at the level it was given
      const li = buildItem(sourceData(event.item), depthOf(event.item));
      event.item.replaceWith(li);
      finishDrag(li);
      openPanel(li);
      markDirty();
    },
    onEnd(event) {
      const moved = event.oldIndex !== event.newIndex || depthOf(event.item) !== (drag ? drag.startDepth : depthOf(event.item));
      finishDrag(event.item);
      if (moved) {
        markDirty();
      }
    },
  });

  root.querySelectorAll('.dgtx-menu-source-list').forEach((list) => {
    Sortable.create(list, {
      group: { name: 'dgtx-menu', pull: 'clone', put: false },
      handle: '.dgtx-menu-handle',
      sort: false,
      forceFallback: true,
      fallbackOnBody: true,
      fallbackTolerance: 4,
      ghostClass: 'dgtx-menu-ghost',
      dragClass: 'dgtx-menu-drag',
      onStart(event) {
        startDrag(event.item, true);
      },
      onEnd() {
        // dropped outside the tree: nothing was added
        drag = null;
        root.classList.remove('is-dragging');
      },
    });
  });

  // --- indent / outdent ---------------------------------------------------------------

  /** One level deeper: becomes the last child of its previous sibling. */
  function indent(li) {
    const depth = depthOf(li);
    let previous = li.previousElementSibling;
    while (isItem(previous) && depthOf(previous) > depth) {
      previous = previous.previousElementSibling;
    }
    if (!isItem(previous) || depthOf(previous) !== depth) {
      return; // first of its siblings: nothing to nest under
    }
    if (depth + blockHeight(li) >= config.maxDepth) {
      notify(labels.tooDeep, 'warning');
      return;
    }
    block(li).forEach((node) => setDepth(node, depthOf(node) + 1));
    markDirty();
    li.querySelector('.dgtx-menu-indent').focus();
  }

  /** One level up: moves after its parent's sub-tree. */
  function outdent(li) {
    const depth = depthOf(li);
    if (depth === 0) {
      return;
    }
    const nodes = block(li);
    let anchor = nodes[nodes.length - 1];
    let node = anchor.nextElementSibling;
    while (isItem(node) && depthOf(node) >= depth) {
      anchor = node;
      node = node.nextElementSibling;
    }
    if (anchor !== nodes[nodes.length - 1]) {
      nodes.forEach((moved) => {
        anchor.after(moved);
        anchor = moved;
      });
    }
    nodes.forEach((moved) => setDepth(moved, depthOf(moved) - 1));
    markDirty();
    li.querySelector('.dgtx-menu-outdent').focus();
  }

  // --- items ------------------------------------------------------------------------

  function sourceData(element) {
    return {
      type: element.dataset.type,
      route: element.dataset.route || null,
      params: element.dataset.params ? JSON.parse(element.dataset.params) : {},
      idEntity: element.dataset.idEntity ? parseInt(element.dataset.idEntity, 10) : null,
      label: element.dataset.label,
      url: element.dataset.url || null,
      link: element.dataset.link || null,
      titles: {},
    };
  }

  function buildItem(data, depth = 0) {
    const li = document.createElement('li');
    li.className = 'dgtx-menu-item';
    li.dataset.key = `n${++sequence}`;
    li.dataset.id = '';
    li.dataset.type = data.type;
    li.dataset.route = data.route || '';
    li.dataset.params = JSON.stringify(data.params || {});
    li.dataset.idEntity = data.idEntity || '';
    li.dataset.label = data.label;
    li.dataset.url = data.url || '';

    const titleFields = config.locales.map((language) => `
      <div class="mb-2 dgtx-menu-locale-field" data-locale-field="${language.locale}" ${language.locale === root.dataset.locale ? '' : 'hidden'}>
        <label class="form-label small mb-1">${escape(labels.title)} <span class="badge text-bg-light">${escape(language.iso.toUpperCase())}</span></label>
        <input type="text" class="form-control form-control-sm dgtx-menu-field" data-field="title" data-locale="${language.locale}" value="${escape(data.titles[language.locale] || '')}" placeholder="${escape(data.label)}">
      </div>`).join('');

    const linkField = data.type === 'link' ? `
      <div class="mb-2">
        <label class="form-label small mb-1">${escape(labels.url)}</label>
        <input type="text" class="form-control form-control-sm dgtx-menu-field" data-field="link" value="${escape(data.link || '')}">
      </div>` : '';

    const warning = data.type !== 'link' && !data.url
      ? `<span class="dgtx-menu-warning material-icons text-warning" title="${escape(labels.noUrl)}">warning</span>`
      : '';

    li.innerHTML = `
      <div class="dgtx-menu-row">
        <span class="dgtx-menu-handle material-icons" title="${escape(labels.drag)}">drag_indicator</span>
        <span class="dgtx-menu-branch material-icons">subdirectory_arrow_right</span>
        <span class="dgtx-menu-type material-icons" title="${escape(labels.types[data.type])}">${ICONS[data.type]}</span>
        <span class="dgtx-menu-title">${escape(data.titles[root.dataset.locale] || data.label)}</span>
        ${warning}
        <span class="dgtx-menu-url text-muted small">${escape(data.url || data.link || data.route || '')}</span>
        <div class="dgtx-menu-actions">
          <button type="button" class="btn btn-link dgtx-menu-outdent" title="${escape(labels.outdent)}"><i class="material-icons">format_indent_decrease</i></button>
          <button type="button" class="btn btn-link dgtx-menu-indent" title="${escape(labels.indent)}"><i class="material-icons">format_indent_increase</i></button>
          <label class="form-check form-switch m-0" title="${escape(labels.visible)}"><input class="form-check-input dgtx-menu-active" type="checkbox" checked></label>
          <button type="button" class="btn btn-link dgtx-menu-toggle" title="${escape(labels.edit)}" aria-expanded="false"><i class="material-icons">expand_more</i></button>
          <button type="button" class="btn btn-link dgtx-menu-delete" title="${escape(labels.remove)}"><i class="material-icons">delete</i></button>
        </div>
      </div>
      <div class="dgtx-menu-panel" hidden>
        ${titleFields}
        ${linkField}
        <div class="row g-2 align-items-end">
          <div class="col-7">
            <label class="form-label small mb-1">${escape(labels.cssClass)}</label>
            <input type="text" class="form-control form-control-sm dgtx-menu-field" data-field="cssClass" value="">
          </div>
          <div class="col-5">
            <label class="form-check small mb-1"><input type="checkbox" class="form-check-input dgtx-menu-field" data-field="target"> ${escape(labels.newTab)}</label>
          </div>
        </div>
      </div>`;

    setDepth(li, depth);
    return li;
  }

  function addItem(data) {
    const li = buildItem(data);
    tree.append(li);
    openPanel(li);
    markDirty();
    li.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    const title = li.querySelector(`.dgtx-menu-field[data-field="title"][data-locale="${root.dataset.locale}"]`);
    if (title) {
      title.focus();
    }
  }

  function openPanel(li, open = true) {
    const panel = li.querySelector('.dgtx-menu-panel');
    const toggle = li.querySelector('.dgtx-menu-toggle');
    panel.hidden = !open;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.querySelector('i').textContent = open ? 'expand_less' : 'expand_more';
  }

  function panelField(li, field, locale) {
    return li.querySelector(locale
      ? `.dgtx-menu-field[data-field="${field}"][data-locale="${locale}"]`
      : `.dgtx-menu-field[data-field="${field}"]`);
  }

  function refreshTitle(li) {
    const field = panelField(li, 'title', root.dataset.locale);
    li.querySelector('.dgtx-menu-title').textContent = (field && field.value.trim()) || li.dataset.label;
  }

  function refreshUrl(li) {
    const link = panelField(li, 'link');
    if (link) {
      li.querySelector('.dgtx-menu-url').textContent = link.value;
    }
  }

  function refreshEmptyState() {
    tree.classList.toggle('is-empty', items().length === 0);
    if (countElement) {
      countElement.textContent = items().length;
    }
  }

  // --- language switch ------------------------------------------------------------

  function switchLocale(locale) {
    root.dataset.locale = locale;
    document.querySelectorAll('[data-locale-switch] [data-locale]').forEach((button) => {
      button.classList.toggle('active', button.dataset.locale === locale);
    });
    tree.querySelectorAll('.dgtx-menu-locale-field').forEach((field) => {
      field.hidden = field.dataset.localeField !== locale;
    });
    items().forEach(refreshTitle);
  }

  // --- serialisation and save -----------------------------------------------------

  function serialize() {
    const out = [];
    const stack = [];
    const positions = new Map();

    items().forEach((li) => {
      const depth = depthOf(li);
      stack.length = depth;
      const parent = depth > 0 ? stack[depth - 1] || null : null;
      const position = positions.get(parent) || 0;
      positions.set(parent, position + 1);

      const titles = {};
      li.querySelectorAll('.dgtx-menu-field[data-field="title"]').forEach((input) => {
        titles[input.dataset.locale] = input.value;
      });

      const item = {
        key: li.dataset.key,
        id: li.dataset.id ? parseInt(li.dataset.id, 10) : null,
        parent,
        position,
        type: li.dataset.type,
        route: li.dataset.route || null,
        params: li.dataset.params ? JSON.parse(li.dataset.params) : {},
        idEntity: li.dataset.idEntity ? parseInt(li.dataset.idEntity, 10) : null,
        link: panelField(li, 'link') ? panelField(li, 'link').value : null,
        cssClass: panelField(li, 'cssClass').value,
        target: panelField(li, 'target').checked ? '_blank' : null,
        active: li.querySelector('.dgtx-menu-active').checked,
        label: li.dataset.label,
        titles,
      };

      out.push(item);
      stack[depth] = item.key;
    });

    return out;
  }

  function setStatus(text, type) {
    status.hidden = false;
    status.textContent = text;
    status.className = `badge dgtx-menu-status text-bg-${type}`;
  }

  function markDirty() {
    dirty = true;
    saveButton.disabled = false;
    setStatus(labels.unsaved, 'warning');
    refreshEmptyState();
  }

  function markClean(savedAt) {
    dirty = false;
    saveButton.disabled = true;
    setStatus(labels.saved.replace('%time%', savedAt.toLocaleTimeString()), 'success');
  }

  function save() {
    if (saveButton.disabled) {
      return;
    }
    saveButton.disabled = true;
    saveButton.classList.add('is-saving');

    fetch(config.saveUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ items: serialize(), _token: config.token }),
    })
      .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
      .then(({ ok, data }) => {
        if (!ok) {
          throw new Error(data.error || labels.saveError);
        }
        Object.entries(data.ids).forEach(([key, id]) => {
          const li = tree.querySelector(`li.dgtx-menu-item[data-key="${key}"]`);
          if (li) {
            li.dataset.id = id;
          }
        });
        markClean(new Date(data.saved_at));
        notify(labels.savedToast, 'success');
      })
      .catch((error) => {
        saveButton.disabled = false;
        setStatus(labels.unsaved, 'warning');
        notify(error.message || labels.saveError, 'error');
      })
      .finally(() => saveButton.classList.remove('is-saving'));
  }

  // --- events -----------------------------------------------------------------------

  tree.addEventListener('click', (event) => {
    const button = event.target.closest('button');
    if (!button) {
      return;
    }
    const li = button.closest('li.dgtx-menu-item');

    if (button.classList.contains('dgtx-menu-toggle')) {
      openPanel(li, li.querySelector('.dgtx-menu-panel').hidden);
    } else if (button.classList.contains('dgtx-menu-outdent')) {
      outdent(li);
    } else if (button.classList.contains('dgtx-menu-indent')) {
      indent(li);
    } else if (button.classList.contains('dgtx-menu-delete')) {
      const children = descendants(li);
      const message = children.length ? labels.confirmRemoveChildren.replace('%count%', children.length) : labels.confirmRemove;
      if (window.confirm(message)) {
        children.forEach((node) => node.remove());
        li.remove();
        markDirty();
      }
    }
  });

  tree.addEventListener('input', (event) => {
    const field = event.target.closest('.dgtx-menu-field, .dgtx-menu-active');
    if (!field) {
      return;
    }
    const li = field.closest('li.dgtx-menu-item');
    if (field.dataset.field === 'title') {
      refreshTitle(li);
    } else if (field.dataset.field === 'link') {
      refreshUrl(li);
    } else if (field.classList.contains('dgtx-menu-active')) {
      li.classList.toggle('is-inactive', !field.checked);
    }
    markDirty();
  });

  root.addEventListener('click', (event) => {
    const add = event.target.closest('.dgtx-menu-add');
    if (add) {
      addItem(sourceData(add.closest('.dgtx-menu-source')));
    }
  });

  root.querySelectorAll('[data-source-filter]').forEach((input) => {
    const pane = document.querySelector(input.dataset.sourceFilter);
    input.addEventListener('input', () => {
      const needle = input.value.trim().toLowerCase();
      pane.querySelectorAll('.dgtx-menu-source').forEach((source) => {
        source.hidden = needle !== '' && !source.dataset.label.toLowerCase().includes(needle);
      });
    });
  });

  const linkForm = root.querySelector('[data-link-form]');
  if (linkForm) {
    linkForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const title = linkForm.elements.title.value.trim();
      const url = linkForm.elements.url.value.trim();
      if (!title || !url) {
        return;
      }
      const titles = {};
      config.locales.forEach((language) => { titles[language.locale] = title; });
      addItem({ type: 'link', route: null, params: {}, idEntity: null, label: title, url, link: url, titles });
      linkForm.reset();
    });
  }

  document.querySelectorAll('[data-locale-switch] [data-locale]').forEach((button) => {
    button.addEventListener('click', () => switchLocale(button.dataset.locale));
  });

  saveButton.addEventListener('click', save);
  document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
      event.preventDefault();
      save();
    }
  });
  window.addEventListener('beforeunload', (event) => {
    if (dirty) {
      event.preventDefault();
      event.returnValue = '';
    }
  });

  items().forEach((li) => setDepth(li, depthOf(li)));
  refreshEmptyState();

  function escape(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }
}

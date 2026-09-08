/*
 * Translation editor: inline edition of a translation cell
 * (templates/admin/helper/list/translation.twig).
 *
 * Click (or Enter) on a cell opens a textarea; Ctrl+Enter or the save button
 * posts {value, _token} as JSON to the cell's data-url; Esc cancels.
 * Cells flagged data-html="1" (or switched with the "code" button) are edited
 * with TinyMCE.
 */
import tinymce from 'tinymce';

const RICH_EDITOR = {
	menubar: false,
	plugins: 'link lists code paste autolink',
	toolbar: 'bold italic underline | link unlink | bullist numlist | removeformat | code',
	toolbar_mode: 'wrap',
	height: 220,
	skin: false,
	content_css: false,
	branding: false,
	statusbar: false,
	convert_urls: false,
	entity_encoding: 'raw',
};

export function initTranslationEditor() {
	const table = document.querySelector('.dgtx-trans-table');
	if (!table) {
		return;
	}

	const labels = {
		placeholder: table.dataset.placeholder,
		save: table.dataset.saveLabel,
		cancel: table.dataset.cancelLabel,
		html: table.dataset.htmlLabel,
		error: table.dataset.error,
	};
	let sequence = 0;

	table.addEventListener('click', (event) => {
		const cell = event.target.closest('.dgtx-trans-value.editable');
		if (cell && !cell.classList.contains('editing')) {
			open(cell);
		}
	});

	table.addEventListener('keydown', (event) => {
		const cell = event.target;
		if (event.key === 'Enter' && cell.classList && cell.classList.contains('editable') && !cell.classList.contains('editing')) {
			event.preventDefault();
			open(cell);
		}
	});

	function open(cell) {
		const current = cell.dataset.value || '';
		const state = { cell, editor: null };

		cell.classList.add('editing');
		cell.dataset.display = cell.innerHTML;
		cell.innerHTML = '';

		const textarea = document.createElement('textarea');
		textarea.className = 'form-control';
		textarea.id = `dgtx-trans-editor-${++sequence}`;
		textarea.rows = Math.min(10, Math.max(2, current.split('\n').length + 1));
		textarea.value = current;
		state.textarea = textarea;

		const actions = document.createElement('div');
		actions.className = 'dgtx-trans-actions';
		const saveButton = button('save', 'btn btn-primary btn-sm', labels.save);
		const cancelButton = button('close', 'btn btn-light btn-sm', labels.cancel);
		const htmlButton = button('code', 'btn btn-light btn-sm', labels.html);
		actions.append(saveButton, cancelButton, htmlButton);

		cell.append(textarea, actions);

		saveButton.addEventListener('click', () => save(state));
		cancelButton.addEventListener('click', () => cancel(state));
		htmlButton.addEventListener('click', () => (state.editor ? plainMode(state, htmlButton) : richMode(state, htmlButton)));
		textarea.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				event.preventDefault();
				cancel(state);
			} else if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
				event.preventDefault();
				save(state);
			}
		});

		if (cell.dataset.html === '1') {
			richMode(state, htmlButton);
		} else {
			textarea.focus();
			textarea.setSelectionRange(textarea.value.length, textarea.value.length);
		}
	}

	function richMode(state, toggle) {
		toggle.classList.add('active');
		tinymce.init({
			...RICH_EDITOR,
			target: state.textarea,
			setup(editor) {
				state.editor = editor;
				editor.on('init', () => editor.focus());
				editor.on('keydown', (event) => {
					if (event.key === 'Escape') {
						cancel(state);
					} else if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
						event.preventDefault();
						save(state);
					}
				});
			},
		});
	}

	function plainMode(state, toggle) {
		toggle.classList.remove('active');
		state.textarea.value = state.editor.getContent();
		destroyEditor(state);
		state.textarea.focus();
	}

	function destroyEditor(state) {
		if (state.editor) {
			tinymce.remove(state.editor);
			state.editor = null;
		}
	}

	function currentValue(state) {
		return state.editor ? state.editor.getContent() : state.textarea.value;
	}

	function cancel(state) {
		destroyEditor(state);
		state.cell.innerHTML = state.cell.dataset.display;
		state.cell.classList.remove('editing');
		state.cell.focus();
	}

	function save(state) {
		const { cell } = state;
		const value = currentValue(state);
		cell.classList.add('saving');

		fetch(cell.dataset.url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				Accept: 'application/json',
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: JSON.stringify({ value, _token: table.dataset.token }),
		})
			.then((response) => response.json().then((data) => ({ ok: response.ok, data })))
			.then(({ ok, data }) => {
				if (!ok) {
					throw new Error(data.error || labels.error);
				}
				destroyEditor(state);
				render(cell, data);
			})
			.catch((error) => {
				cell.classList.remove('saving');
				window.alert(error.message || labels.error);
			});
	}

	function render(cell, data) {
		const html = /<[a-z][^>]*>/i.test(`${cell.closest('tr').querySelector('.dgtx-trans-key').textContent} ${data.value || ''}`);

		cell.classList.remove('editing', 'saving');
		cell.classList.toggle('dgtx-trans-html', html);
		cell.dataset.value = data.value || '';
		cell.dataset.html = html ? '1' : '0';
		cell.innerHTML = '';

		if (!data.value) {
			const empty = document.createElement('span');
			empty.className = 'text-muted fst-italic dgtx-trans-empty';
			empty.textContent = labels.placeholder;
			cell.append(empty);
		} else if (html) {
			cell.innerHTML = data.value;
		} else {
			cell.textContent = data.value;
		}

		const row = cell.closest('tr');
		row.className = `dgtx-trans-row dgtx-trans-row-${data.status}`;
		const badge = row.querySelector('.dgtx-status');
		if (badge) {
			badge.className = `badge dgtx-status dgtx-status-${data.status}`;
			badge.textContent = data.statusLabel;
		}

		const tab = document.querySelector(`.dgtx-trans-tabs [data-locale="${data.locale}"]`);
		if (tab && data.counts) {
			tab.querySelector('[data-count="translated"]').textContent = data.counts.translated;
			tab.querySelector('[data-count="total"]').textContent = data.counts.total;
			const percent = data.counts.total > 0 ? Math.round((100 * data.counts.translated) / data.counts.total) : 0;
			const bar = tab.querySelector('[data-progress]');
			bar.style.width = `${percent}%`;
			bar.classList.toggle('bg-success', percent === 100);
		}

		cell.classList.remove('saved');
		void cell.offsetWidth; // restart the animation
		cell.classList.add('saved');
		cell.focus();
	}

	function button(icon, className, title) {
		const element = document.createElement('button');
		element.type = 'button';
		element.className = className;
		element.title = title;
		element.innerHTML = `<i class="material-icons">${icon}</i>`;
		return element;
	}
}

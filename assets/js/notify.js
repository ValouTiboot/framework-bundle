/*
 * Toast notifications, top right, for the AJAX screens of the admin.
 *
 *   notify('Saved.');                       green
 *   notify('Something broke.', 'error');    red, stays longer
 *   notify('Unsaved changes', 'warning');   yellow
 *   notify('FYI', 'info');                  blue
 *
 * Also exposed as window.dgtxNotify for project scripts.
 */
const ICONS = { success: 'check_circle', error: 'error', warning: 'warning', info: 'info' };

export function notify(message, type = 'success', timeout = 4000) {
  let container = document.getElementById('dgtx-toasts');
  if (!container) {
    container = document.createElement('div');
    container.id = 'dgtx-toasts';
    container.className = 'dgtx-toasts';
    container.setAttribute('aria-live', 'polite');
    document.body.append(container);
  }

  const toast = document.createElement('div');
  toast.className = `dgtx-toast dgtx-toast-${type}`;
  toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
  toast.innerHTML = `<i class="material-icons dgtx-toast-icon">${ICONS[type] || ICONS.info}</i>
    <span class="dgtx-toast-text"></span>
    <button type="button" class="dgtx-toast-close" aria-label="close"><i class="material-icons">close</i></button>`;
  toast.querySelector('.dgtx-toast-text').textContent = message;

  container.append(toast);
  requestAnimationFrame(() => toast.classList.add('is-visible'));

  let closed = false;
  const close = () => {
    if (closed) {
      return;
    }
    closed = true;
    toast.classList.remove('is-visible');
    setTimeout(() => toast.remove(), 250);
  };

  toast.querySelector('.dgtx-toast-close').addEventListener('click', close);
  if (timeout) {
    setTimeout(close, type === 'error' ? Math.max(timeout, 8000) : timeout);
  }

  return { element: toast, close };
}

window.dgtxNotify = notify;

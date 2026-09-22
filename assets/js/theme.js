/*
 * Colour mode switch of the user menu (templates/admin/layout.html.twig).
 *
 * The stored choice lives in <html data-theme="system|light|dark">; the inline
 * script of the layout turns it into data-bs-theme before the first paint and
 * exposes window.dgtxApplyTheme() to redo it. A click applies the new mode at
 * once and stores it (Configuration "adminTheme") through the tools action.
 */
import { notify } from './notify';

export function initThemeSwitch() {
  const container = document.querySelector('[data-theme-switch]');
  if (!container) {
    return;
  }

  const root = document.documentElement;
  const buttons = Array.from(container.querySelectorAll('[data-theme-choice]'));

  const apply = (theme) => {
    root.setAttribute('data-theme', theme);
    if (typeof window.dgtxApplyTheme === 'function') {
      window.dgtxApplyTheme();
    }
    buttons.forEach((button) => button.classList.toggle('active', button.dataset.themeChoice === theme));
  };

  buttons.forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation(); // keep the dropdown open
      const previous = root.getAttribute('data-theme');
      const theme = button.dataset.themeChoice;
      if (theme === previous) {
        return;
      }
      apply(theme);

      fetch(container.dataset.url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ theme, _token: container.dataset.token }),
      })
        .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
          if (!ok) {
            throw new Error(data.error || container.dataset.error);
          }
        })
        .catch((error) => {
          apply(previous);
          notify(error.message || container.dataset.error, 'error');
        });
    });
  });
}

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import '@fortawesome/fontawesome-free/js/all.js';
import Menu from './class/Menu';

document.addEventListener('DOMContentLoaded', () => {
  const menu = new Menu(
    document.getElementById('side-menu'),
    {
      open: document.getElementById('side-menu-open-handle'),
      close: document.getElementById('side-menu-close-handle')
    },
    document.getElementById('shadow-on-page')
  );

  const languagePicker: HTMLSelectElement = document.querySelector('select#display-language-picker');
  languagePicker.onchange = () => {
    window.location.href = window.location.pathname.replace(/(^\/)([a-z]*)(\/|$)/gm, `$1${languagePicker.value}$3`);
  }
})
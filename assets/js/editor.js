/*
 * TinyMCE 7, loaded on demand (dynamic import from admin.js and
 * translation.js) so that pages without a rich text editor never download
 * it. The skin and the content stylesheet are fetched by TinyMCE itself from
 * the copied "tinymce/skins" directory (see webpack.config.js), under the
 * assets base URL exposed by the layout in <body data-assets="...">.
 */
import tinymce from 'tinymce';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';
import 'tinymce/icons/default';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/emoticons';
import 'tinymce/plugins/emoticons/js/emojis';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/help';
import 'tinymce/plugins/help/js/i18n/keynav/en';
import 'tinymce/plugins/image';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/nonbreaking';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/quickbars';
import 'tinymce/plugins/save';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/table';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/wordcount';

export { tinymce };

const assetsBase = () => (document.body.dataset.assets || '/bundles/digitixframework/assets/').replace(/\/?$/, '/');

/** Options shared by every editor: licence, skin location, content look. */
export function baseConfig() {
  return {
    license_key: 'gpl',
    promotion: false,
    branding: false,
    skin: 'oxide',
    skin_url: `${assetsBase()}tinymce/skins/ui/oxide`,
    content_css: `${assetsBase()}tinymce/skins/content/default/content.min.css`,
    content_style: 'body { font-family: "Inter Variable", Inter, system-ui, sans-serif; font-size: 14px; line-height: 1.5; margin: 1rem; } img { max-width: 100%; height: auto; }',
  };
}

/** Compact configuration for a single field (translation editor). */
export function richConfig() {
  return {
    ...baseConfig(),
    menubar: false,
    plugins: 'link lists code autolink',
    toolbar: 'bold italic underline | link unlink | bullist numlist | removeformat | code',
    toolbar_mode: 'wrap',
    height: 220,
    statusbar: false,
    convert_urls: false,
    entity_encoding: 'raw',
  };
}

/** Full editors on every "textarea.tinymce" of the page. */
export function initEditors() {
  return tinymce.init({
    ...baseConfig(),
    selector: 'textarea.tinymce',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons',
    menubar: 'file edit view insert format tools table help',
    toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save | image media link anchor codesample | ltr rtl',
    toolbar_sticky: true,
    toolbar_mode: 'sliding',
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: '{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    image_advtab: true,
    image_caption: true,
    image_title: true,
    automatic_uploads: true,
    importcss_append: true,
    relative_urls: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    contextmenu: 'link image table',
    height: 500,
    file_picker_callback(callback, value, meta) {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');

      input.onchange = function () {
        const file = this.files[0];
        const reader = new FileReader();

        reader.onload = () => {
          // register the file in TinyMCE's blob registry, then hand its URI to the dialog
          const id = `blobid${Date.now()}`;
          const blobCache = tinymce.activeEditor.editorUpload.blobCache;
          const base64 = reader.result.split(',')[1];
          const blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);

          callback(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };

      input.click();
    },
  });
}

(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["admin"],{

/***/ "./js/admin.js":
/*!*********************!*\
  !*** ./js/admin.js ***!
  \*********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_date_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.date.to-string.js */ "./node_modules/core-js/modules/es.date.to-string.js");
/* harmony import */ var core_js_modules_es_date_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_date_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.string.split.js */ "./node_modules/core-js/modules/es.string.split.js");
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.regexp.to-string.js */ "./node_modules/core-js/modules/es.regexp.to-string.js");
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var bootstrap_dist_js_bootstrap_min__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! bootstrap/dist/js/bootstrap.min */ "./node_modules/bootstrap/dist/js/bootstrap.min.js");
/* harmony import */ var bootstrap_dist_js_bootstrap_min__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(bootstrap_dist_js_bootstrap_min__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! jquery-ui/ui/widgets/sortable */ "./node_modules/jquery-ui/ui/widgets/sortable.js");
/* harmony import */ var jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _yaireo_tagify__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! @yaireo/tagify */ "./node_modules/@yaireo/tagify/dist/tagify.min.js");
/* harmony import */ var _yaireo_tagify__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_yaireo_tagify__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var tinymce_tinymce_min__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! tinymce/tinymce.min */ "./node_modules/tinymce/tinymce.min.js");
/* harmony import */ var tinymce_tinymce_min__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(tinymce_tinymce_min__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! tinymce/icons/default/icons.min */ "./node_modules/tinymce/icons/default/icons.min.js");
/* harmony import */ var tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! tinymce/themes/silver/theme.min */ "./node_modules/tinymce/themes/silver/theme.min.js");
/* harmony import */ var tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! tinymce/plugins/print/plugin.min */ "./node_modules/tinymce/plugins/print/plugin.min.js");
/* harmony import */ var tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! tinymce/plugins/preview/plugin.min */ "./node_modules/tinymce/plugins/preview/plugin.min.js");
/* harmony import */ var tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! tinymce/plugins/paste/plugin.min */ "./node_modules/tinymce/plugins/paste/plugin.min.js");
/* harmony import */ var tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_19___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_19__);
/* harmony import */ var tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! tinymce/plugins/importcss/plugin.min */ "./node_modules/tinymce/plugins/importcss/plugin.min.js");
/* harmony import */ var tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_20___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_20__);
/* harmony import */ var tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! tinymce/plugins/searchreplace/plugin.min */ "./node_modules/tinymce/plugins/searchreplace/plugin.min.js");
/* harmony import */ var tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_21___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_21__);
/* harmony import */ var tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! tinymce/plugins/autolink/plugin.min */ "./node_modules/tinymce/plugins/autolink/plugin.min.js");
/* harmony import */ var tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_22___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_22__);
/* harmony import */ var tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! tinymce/plugins/autosave/plugin.min */ "./node_modules/tinymce/plugins/autosave/plugin.min.js");
/* harmony import */ var tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_23___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_23__);
/* harmony import */ var tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! tinymce/plugins/save/plugin.min */ "./node_modules/tinymce/plugins/save/plugin.min.js");
/* harmony import */ var tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_24___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_24__);
/* harmony import */ var tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! tinymce/plugins/directionality/plugin.min */ "./node_modules/tinymce/plugins/directionality/plugin.min.js");
/* harmony import */ var tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_25___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_25__);
/* harmony import */ var tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! tinymce/plugins/code/plugin.min */ "./node_modules/tinymce/plugins/code/plugin.min.js");
/* harmony import */ var tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_26___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_26__);
/* harmony import */ var tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! tinymce/plugins/visualblocks/plugin.min */ "./node_modules/tinymce/plugins/visualblocks/plugin.min.js");
/* harmony import */ var tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_27___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_27__);
/* harmony import */ var tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! tinymce/plugins/visualchars/plugin.min */ "./node_modules/tinymce/plugins/visualchars/plugin.min.js");
/* harmony import */ var tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_28___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_28__);
/* harmony import */ var tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! tinymce/plugins/fullscreen/plugin.min */ "./node_modules/tinymce/plugins/fullscreen/plugin.min.js");
/* harmony import */ var tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_29___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_29__);
/* harmony import */ var tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! tinymce/plugins/image/plugin.min */ "./node_modules/tinymce/plugins/image/plugin.min.js");
/* harmony import */ var tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_30___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_30__);
/* harmony import */ var tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! tinymce/plugins/link/plugin.min */ "./node_modules/tinymce/plugins/link/plugin.min.js");
/* harmony import */ var tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_31___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_31__);
/* harmony import */ var tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! tinymce/plugins/media/plugin.min */ "./node_modules/tinymce/plugins/media/plugin.min.js");
/* harmony import */ var tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_32___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_32__);
/* harmony import */ var tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! tinymce/plugins/template/plugin.min */ "./node_modules/tinymce/plugins/template/plugin.min.js");
/* harmony import */ var tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_33___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_33__);
/* harmony import */ var tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! tinymce/plugins/codesample/plugin.min */ "./node_modules/tinymce/plugins/codesample/plugin.min.js");
/* harmony import */ var tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_34___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_34__);
/* harmony import */ var tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! tinymce/plugins/table/plugin.min */ "./node_modules/tinymce/plugins/table/plugin.min.js");
/* harmony import */ var tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_35___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_35__);
/* harmony import */ var tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! tinymce/plugins/charmap/plugin.min */ "./node_modules/tinymce/plugins/charmap/plugin.min.js");
/* harmony import */ var tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_36___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_36__);
/* harmony import */ var tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! tinymce/plugins/hr/plugin.min */ "./node_modules/tinymce/plugins/hr/plugin.min.js");
/* harmony import */ var tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_37___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_37__);
/* harmony import */ var tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! tinymce/plugins/pagebreak/plugin.min */ "./node_modules/tinymce/plugins/pagebreak/plugin.min.js");
/* harmony import */ var tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_38___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_38__);
/* harmony import */ var tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! tinymce/plugins/nonbreaking/plugin.min */ "./node_modules/tinymce/plugins/nonbreaking/plugin.min.js");
/* harmony import */ var tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_39___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_39__);
/* harmony import */ var tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! tinymce/plugins/anchor/plugin.min */ "./node_modules/tinymce/plugins/anchor/plugin.min.js");
/* harmony import */ var tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_40___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_40__);
/* harmony import */ var tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! tinymce/plugins/toc/plugin.min */ "./node_modules/tinymce/plugins/toc/plugin.min.js");
/* harmony import */ var tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_41___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_41__);
/* harmony import */ var tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! tinymce/plugins/insertdatetime/plugin.min */ "./node_modules/tinymce/plugins/insertdatetime/plugin.min.js");
/* harmony import */ var tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_42___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_42__);
/* harmony import */ var tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! tinymce/plugins/advlist/plugin.min */ "./node_modules/tinymce/plugins/advlist/plugin.min.js");
/* harmony import */ var tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_43___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_43__);
/* harmony import */ var tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! tinymce/plugins/lists/plugin.min */ "./node_modules/tinymce/plugins/lists/plugin.min.js");
/* harmony import */ var tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_44___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_44__);
/* harmony import */ var tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! tinymce/plugins/wordcount/plugin.min */ "./node_modules/tinymce/plugins/wordcount/plugin.min.js");
/* harmony import */ var tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_45___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_45__);
/* harmony import */ var tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! tinymce/plugins/imagetools/plugin.min */ "./node_modules/tinymce/plugins/imagetools/plugin.min.js");
/* harmony import */ var tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_46___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_46__);
/* harmony import */ var tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! tinymce/plugins/textpattern/plugin.min */ "./node_modules/tinymce/plugins/textpattern/plugin.min.js");
/* harmony import */ var tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_47___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_47__);
/* harmony import */ var tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! tinymce/plugins/noneditable/plugin.min */ "./node_modules/tinymce/plugins/noneditable/plugin.min.js");
/* harmony import */ var tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_48___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_48__);
/* harmony import */ var tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! tinymce/plugins/help/plugin.min */ "./node_modules/tinymce/plugins/help/plugin.min.js");
/* harmony import */ var tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_49___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_49__);
/* harmony import */ var tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! tinymce/plugins/quickbars/plugin.min */ "./node_modules/tinymce/plugins/quickbars/plugin.min.js");
/* harmony import */ var tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_50___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_50__);
/* harmony import */ var tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! tinymce/plugins/emoticons/plugin.min */ "./node_modules/tinymce/plugins/emoticons/plugin.min.js");
/* harmony import */ var tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_51___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_51__);
/* harmony import */ var tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! tinymce/plugins/emoticons/js/emojiimages.min */ "./node_modules/tinymce/plugins/emoticons/js/emojiimages.min.js");
/* harmony import */ var tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_52___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_52__);
/* harmony import */ var tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! tinymce/plugins/emoticons/js/emojis.min */ "./node_modules/tinymce/plugins/emoticons/js/emojis.min.js");
/* harmony import */ var tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_53___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_53__);











/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
// import './css/style.scss';



 // import '@yaireo/tagify/dist/tagify.min';
// import '@yaireo/tagify/dist/jQuery.tagify.min';
// Import TinyMCE

 // Default icons are required for TinyMCE 5.3 or above

 // A theme is also required

 // Any plugins you want to use has to be imported







































console.log('Hello Webpack Encore! Edit me in asset/admin/_dev/admin.js');
jquery__WEBPACK_IMPORTED_MODULE_10___default()(document).ready(function () {
  // Menu
  jquery__WEBPACK_IMPORTED_MODULE_10___default()('.menu-items a[href^="#"]').click(function (e) {
    e.preventDefault();
    jquery__WEBPACK_IMPORTED_MODULE_10___default()('.menu-items a[href^="#"]').removeClass('expand').parent().find('.sub-menu').slideUp('fast');
    if (jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).parent().find('.sub-menu').is(':visible')) jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).removeClass('expand').parent().find('.sub-menu').slideUp('fast');else jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).addClass('expand').parent().find('.sub-menu').slideDown('fast');
  }); // Tagify

  jquery__WEBPACK_IMPORTED_MODULE_10___default()('.tag').each(function () {
    var input = jquery__WEBPACK_IMPORTED_MODULE_10___default()(this);
    new _yaireo_tagify__WEBPACK_IMPORTED_MODULE_13___default.a(input[0], {
      pattern: /^.{0,30}$/
    });
  }); // DGTX-switch 

  jquery__WEBPACK_IMPORTED_MODULE_10___default()('.dgtx-switch').each(function () {
    var check = jquery__WEBPACK_IMPORTED_MODULE_10___default()('input[type="radio"]:checked', jquery__WEBPACK_IMPORTED_MODULE_10___default()(this));

    if (typeof check !== 'undefined' && check.val() == 1) {
      jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).addClass('dgtx-switch-yes');
      check.next().addClass('checked');
    } else {
      jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).addClass('dgtx-switch-no');
      jquery__WEBPACK_IMPORTED_MODULE_10___default()('input[value="0"]', jquery__WEBPACK_IMPORTED_MODULE_10___default()(this)).attr('checked', 'checked').next().addClass('checked');
    }
  });
  jquery__WEBPACK_IMPORTED_MODULE_10___default()('.dgtx-switch label').on('click', function () {
    var parent = jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).parent().parent();
    jquery__WEBPACK_IMPORTED_MODULE_10___default()('label', parent).removeClass('checked');
    jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).addClass('checked');
    if (parent.hasClass('dgtx-switch-no')) parent.removeClass('dgtx-switch-no').addClass('dgtx-switch-yes');else parent.removeClass('dgtx-switch-yes').addClass('dgtx-switch-no');
  }); // FriendlyUrl

  jquery__WEBPACK_IMPORTED_MODULE_10___default()('input[id*="translatableRewrite"]').on('keyup blur', function () {
    jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).val(toRewriteUrl(jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).val()));
  });
  jquery__WEBPACK_IMPORTED_MODULE_10___default()('input[id*="translatableName"]').on('blur', function () {
    var associateRewriteFieldId = jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).prop('id').replace('Name', 'Rewrite');
    var associateRewriteField = jquery__WEBPACK_IMPORTED_MODULE_10___default()('#' + associateRewriteFieldId);
    if (associateRewriteField.val() == '') associateRewriteField.val(toRewriteUrl(jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).val()));
  });
  jquery__WEBPACK_IMPORTED_MODULE_10___default()('.item-delete').on('click', function (e) {
    if (!confirm("Confirm deletion?")) e.preventDefault();
  }); // sortable

  jquery__WEBPACK_IMPORTED_MODULE_10___default()('table.sortable tbody').sortable({
    handle: '.sortable-item',
    items: 'tr',
    axis: 'y',
    update: function update(event, ui) {
      jquery__WEBPACK_IMPORTED_MODULE_10___default.a.post(jquery__WEBPACK_IMPORTED_MODULE_10___default()('table.sortable').data('sortable-action'), jquery__WEBPACK_IMPORTED_MODULE_10___default()(this).sortable("serialize"));
    }
  }); // copyright 

  jquery__WEBPACK_IMPORTED_MODULE_10___default()(window).on('scroll', showfooter);
  showfooter(); // TinyMce

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
    file_picker_callback: function file_picker_callback(cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file'); // input.setAttribute('accept', 'image/*');

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
          var id = 'blobid' + new Date().getTime();
          var blobCache = tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          /* call the callback and populate the Title field with the file name */

          cb(blobInfo.blobUri(), {
            title: file.name
          });
        };

        reader.readAsDataURL(file);
      };

      input.click();
    },
    templates: [{
      title: 'New Table',
      description: 'creates a new table',
      content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
    }, {
      title: 'Starting my story',
      description: 'A cure for writers block',
      content: 'Once upon a time...'
    }, {
      title: 'New list with dates',
      description: 'New List with dates',
      content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
    }],
    template_cdate_format: '[Date Created (CDATE): %d/%m/%Y : %H:%M:%S]',
    template_mdate_format: '[Date Modified (MDATE): %d/%m/%Y : %H:%M:%S]',
    height: 500,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_noneditable_class: 'mceNonEditable',
    toolbar_mode: 'sliding',
    contextmenu: 'link image imagetools table',
    skin: 'oxide',
    content_css: 'default' // content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'

  });
});

function toRewriteUrl(str) {
  var encodedUrl = str.toString().toLowerCase(); // make the url lowercase

  encodedUrl = encodedUrl.split(/\&+/).join("-and-"); // replace & with and

  encodedUrl = encodedUrl.split(/[^a-z0-9]/).join("-"); // remove invalid characters 

  encodedUrl = encodedUrl.split(/-+/).join("-"); // remove duplicates 

  encodedUrl = encodedUrl.trim('-'); // trim leading & trailing characters 

  return encodedUrl;
}

function showfooter() {
  var tHeight = jquery__WEBPACK_IMPORTED_MODULE_10___default()('body').height();
  var currentHeight = jquery__WEBPACK_IMPORTED_MODULE_10___default()(window).height() + jquery__WEBPACK_IMPORTED_MODULE_10___default()(window).scrollTop() + 10;
  if (currentHeight > tHeight) jquery__WEBPACK_IMPORTED_MODULE_10___default()('#footer').addClass('open');else if (jquery__WEBPACK_IMPORTED_MODULE_10___default()('#footer').hasClass('open')) jquery__WEBPACK_IMPORTED_MODULE_10___default()('#footer').removeClass('open');
}

/***/ })

},[["./js/admin.js","runtime","vendors~admin"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9qcy9hZG1pbi5qcyJdLCJuYW1lcyI6WyJjb25zb2xlIiwibG9nIiwiJCIsImRvY3VtZW50IiwicmVhZHkiLCJjbGljayIsImUiLCJwcmV2ZW50RGVmYXVsdCIsInJlbW92ZUNsYXNzIiwicGFyZW50IiwiZmluZCIsInNsaWRlVXAiLCJpcyIsImFkZENsYXNzIiwic2xpZGVEb3duIiwiZWFjaCIsImlucHV0IiwiVGFnaWZ5IiwicGF0dGVybiIsImNoZWNrIiwidmFsIiwibmV4dCIsImF0dHIiLCJvbiIsImhhc0NsYXNzIiwidG9SZXdyaXRlVXJsIiwiYXNzb2NpYXRlUmV3cml0ZUZpZWxkSWQiLCJwcm9wIiwicmVwbGFjZSIsImFzc29jaWF0ZVJld3JpdGVGaWVsZCIsImNvbmZpcm0iLCJzb3J0YWJsZSIsImhhbmRsZSIsIml0ZW1zIiwiYXhpcyIsInVwZGF0ZSIsImV2ZW50IiwidWkiLCJwb3N0IiwiZGF0YSIsIndpbmRvdyIsInNob3dmb290ZXIiLCJ0aW55bWNlIiwiaW5pdCIsInNlbGVjdG9yIiwicGx1Z2lucyIsImltYWdldG9vbHNfY29yc19ob3N0cyIsIm1lbnViYXIiLCJ0b29sYmFyIiwidG9vbGJhcl9zdGlja3kiLCJhdXRvc2F2ZV9hc2tfYmVmb3JlX3VubG9hZCIsImF1dG9zYXZlX2ludGVydmFsIiwiYXV0b3NhdmVfcHJlZml4IiwiYXV0b3NhdmVfcmVzdG9yZV93aGVuX2VtcHR5IiwiYXV0b3NhdmVfcmV0ZW50aW9uIiwiaW1hZ2VfYWR2dGFiIiwibGlua19saXN0IiwiaW1hZ2VfbGlzdCIsImltYWdlX2NsYXNzX2xpc3QiLCJpbXBvcnRjc3NfYXBwZW5kIiwicmVsYXRpdmVfdXJscyIsImltYWdlX3RpdGxlIiwiYXV0b21hdGljX3VwbG9hZHMiLCJmaWxlX3BpY2tlcl9jYWxsYmFjayIsImNiIiwidmFsdWUiLCJtZXRhIiwiY3JlYXRlRWxlbWVudCIsInNldEF0dHJpYnV0ZSIsIm9uY2hhbmdlIiwiZmlsZSIsImZpbGVzIiwicmVhZGVyIiwiRmlsZVJlYWRlciIsIm9ubG9hZCIsImlkIiwiRGF0ZSIsImdldFRpbWUiLCJibG9iQ2FjaGUiLCJhY3RpdmVFZGl0b3IiLCJlZGl0b3JVcGxvYWQiLCJiYXNlNjQiLCJyZXN1bHQiLCJzcGxpdCIsImJsb2JJbmZvIiwiY3JlYXRlIiwiYWRkIiwiYmxvYlVyaSIsInRpdGxlIiwibmFtZSIsInJlYWRBc0RhdGFVUkwiLCJ0ZW1wbGF0ZXMiLCJkZXNjcmlwdGlvbiIsImNvbnRlbnQiLCJ0ZW1wbGF0ZV9jZGF0ZV9mb3JtYXQiLCJ0ZW1wbGF0ZV9tZGF0ZV9mb3JtYXQiLCJoZWlnaHQiLCJpbWFnZV9jYXB0aW9uIiwicXVpY2tiYXJzX3NlbGVjdGlvbl90b29sYmFyIiwibm9uZWRpdGFibGVfbm9uZWRpdGFibGVfY2xhc3MiLCJ0b29sYmFyX21vZGUiLCJjb250ZXh0bWVudSIsInNraW4iLCJjb250ZW50X2NzcyIsInN0ciIsImVuY29kZWRVcmwiLCJ0b1N0cmluZyIsInRvTG93ZXJDYXNlIiwiam9pbiIsInRyaW0iLCJ0SGVpZ2h0IiwiY3VycmVudEhlaWdodCIsInNjcm9sbFRvcCJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7Q0FFQTtBQUNBO0FBRUE7O0NBRUE7O0NBRUE7O0NBRUE7O0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFD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SXFFLE9BQU8sR0FBRzVHLDhDQUFDLENBQUMsTUFBRCxDQUFELENBQVU4RixNQUFWLEVBQWQ7QUFDQSxNQUFJZSxhQUFhLEdBQUc3Ryw4Q0FBQyxDQUFDc0MsTUFBRCxDQUFELENBQVV3RCxNQUFWLEtBQW1COUYsOENBQUMsQ0FBQ3NDLE1BQUQsQ0FBRCxDQUFVd0UsU0FBVixFQUFuQixHQUF5QyxFQUE3RDtBQUVBLE1BQUlELGFBQWEsR0FBR0QsT0FBcEIsRUFDQzVHLDhDQUFDLENBQUMsU0FBRCxDQUFELENBQWFXLFFBQWIsQ0FBc0IsTUFBdEIsRUFERCxLQUVLLElBQUlYLDhDQUFDLENBQUMsU0FBRCxDQUFELENBQWFzQixRQUFiLENBQXNCLE1BQXRCLENBQUosRUFDSnRCLDhDQUFDLENBQUMsU0FBRCxDQUFELENBQWFNLFdBQWIsQ0FBeUIsTUFBekI7QUFFRCxDIiwiZmlsZSI6ImFkbWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLypcbiAqIFdlbGNvbWUgdG8geW91ciBhcHAncyBtYWluIEphdmFTY3JpcHQgZmlsZSFcbiAqXG4gKiBXZSByZWNvbW1lbmQgaW5jbHVkaW5nIHRoZSBidWlsdCB2ZXJzaW9uIG9mIHRoaXMgSmF2YVNjcmlwdCBmaWxlXG4gKiAoYW5kIGl0cyBDU1MgZmlsZSkgaW4geW91ciBiYXNlIGxheW91dCAoYmFzZS5odG1sLnR3aWcpLlxuICovXG5cbi8vIGFueSBDU1MgeW91IGltcG9ydCB3aWxsIG91dHB1dCBpbnRvIGEgc2luZ2xlIGNzcyBmaWxlIChhcHAuY3NzIGluIHRoaXMgY2FzZSlcbi8vIGltcG9ydCAnLi9jc3Mvc3R5bGUuc2Nzcyc7XG5cbmltcG9ydCAkIGZyb20gXCJqcXVlcnlcIjtcbmltcG9ydCAnYm9vdHN0cmFwL2Rpc3QvanMvYm9vdHN0cmFwLm1pbic7XG5pbXBvcnQgJ2pxdWVyeS11aS91aS93aWRnZXRzL3NvcnRhYmxlJztcbmltcG9ydCBUYWdpZnkgZnJvbSAnQHlhaXJlby90YWdpZnknO1xuLy8gaW1wb3J0ICdAeWFpcmVvL3RhZ2lmeS9kaXN0L3RhZ2lmeS5taW4nO1xuLy8gaW1wb3J0ICdAeWFpcmVvL3RhZ2lmeS9kaXN0L2pRdWVyeS50YWdpZnkubWluJztcblxuLy8gSW1wb3J0IFRpbnlNQ0VcbmltcG9ydCAndGlueW1jZS90aW55bWNlLm1pbic7XG4vLyBEZWZhdWx0IGljb25zIGFyZSByZXF1aXJlZCBmb3IgVGlueU1DRSA1LjMgb3IgYWJvdmVcbmltcG9ydCAndGlueW1jZS9pY29ucy9kZWZhdWx0L2ljb25zLm1pbic7XG4vLyBBIHRoZW1lIGlzIGFsc28gcmVxdWlyZWRcbmltcG9ydCAndGlueW1jZS90aGVtZXMvc2lsdmVyL3RoZW1lLm1pbic7XG4vLyBBbnkgcGx1Z2lucyB5b3Ugd2FudCB0byB1c2UgaGFzIHRvIGJlIGltcG9ydGVkXG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9wcmludC9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3ByZXZpZXcvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9wYXN0ZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2ltcG9ydGNzcy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3NlYXJjaHJlcGxhY2UvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9hdXRvbGluay9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2F1dG9zYXZlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvc2F2ZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2RpcmVjdGlvbmFsaXR5L3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvY29kZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3Zpc3VhbGJsb2Nrcy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3Zpc3VhbGNoYXJzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvZnVsbHNjcmVlbi9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2ltYWdlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvbGluay9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL21lZGlhL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdGVtcGxhdGUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9jb2Rlc2FtcGxlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdGFibGUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9jaGFybWFwL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvaHIvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9wYWdlYnJlYWsvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9ub25icmVha2luZy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2FuY2hvci9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3RvYy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2luc2VydGRhdGV0aW1lL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvYWR2bGlzdC9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2xpc3RzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvd29yZGNvdW50L3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvaW1hZ2V0b29scy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3RleHRwYXR0ZXJuL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvbm9uZWRpdGFibGUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9oZWxwL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvY2hhcm1hcC9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3F1aWNrYmFycy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2Vtb3RpY29ucy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2Vtb3RpY29ucy9qcy9lbW9qaWltYWdlcy5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvZW1vdGljb25zL2pzL2Vtb2ppcy5taW4nO1xuXG5jb25zb2xlLmxvZygnSGVsbG8gV2VicGFjayBFbmNvcmUhIEVkaXQgbWUgaW4gYXNzZXQvYWRtaW4vX2Rldi9hZG1pbi5qcycpO1xuXG4kKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpe1xuXG5cdC8vIE1lbnVcblx0JCgnLm1lbnUtaXRlbXMgYVtocmVmXj1cIiNcIl0nKS5jbGljayhmdW5jdGlvbihlKXtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0JCgnLm1lbnUtaXRlbXMgYVtocmVmXj1cIiNcIl0nKS5yZW1vdmVDbGFzcygnZXhwYW5kJykucGFyZW50KCkuZmluZCgnLnN1Yi1tZW51Jykuc2xpZGVVcCgnZmFzdCcpO1xuXHRcdFxuXHRcdGlmICgkKHRoaXMpLnBhcmVudCgpLmZpbmQoJy5zdWItbWVudScpLmlzKCc6dmlzaWJsZScpKVxuXHRcdFx0JCh0aGlzKS5yZW1vdmVDbGFzcygnZXhwYW5kJykucGFyZW50KCkuZmluZCgnLnN1Yi1tZW51Jykuc2xpZGVVcCgnZmFzdCcpO1xuXHRcdGVsc2Vcblx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ2V4cGFuZCcpLnBhcmVudCgpLmZpbmQoJy5zdWItbWVudScpLnNsaWRlRG93bignZmFzdCcpO1xuXHR9KTtcblxuXHQvLyBUYWdpZnlcblx0JCgnLnRhZycpLmVhY2goZnVuY3Rpb24oKXtcblx0XHR2YXIgaW5wdXQgPSAkKHRoaXMpO1xuXHRcdG5ldyBUYWdpZnkoaW5wdXRbMF0sIHtcblx0XHRcdHBhdHRlcm4gOiAvXi57MCwzMH0kLyxcblx0XHR9KTtcblx0fSk7XG5cblx0Ly8gREdUWC1zd2l0Y2ggXG5cdCQoJy5kZ3R4LXN3aXRjaCcpLmVhY2goZnVuY3Rpb24oKXtcblx0XHR2YXIgY2hlY2sgPSAkKCdpbnB1dFt0eXBlPVwicmFkaW9cIl06Y2hlY2tlZCcsICQodGhpcykpO1xuXG5cdFx0aWYgKHR5cGVvZiBjaGVjayAhPT0gJ3VuZGVmaW5lZCcgJiYgY2hlY2sudmFsKCkgPT0gMSkge1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnZGd0eC1zd2l0Y2gteWVzJyk7XG5cdFx0XHRjaGVjay5uZXh0KCkuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnZGd0eC1zd2l0Y2gtbm8nKTtcblx0XHRcdCQoJ2lucHV0W3ZhbHVlPVwiMFwiXScsICQodGhpcykpLmF0dHIoJ2NoZWNrZWQnLCdjaGVja2VkJykubmV4dCgpLmFkZENsYXNzKCdjaGVja2VkJyk7XG5cdFx0fVxuXHR9KTtcblxuXHQkKCcuZGd0eC1zd2l0Y2ggbGFiZWwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpe1xuXHRcdFxuXHRcdHZhciBwYXJlbnQgPSAkKHRoaXMpLnBhcmVudCgpLnBhcmVudCgpO1xuXHRcdFxuXHRcdCQoJ2xhYmVsJywgcGFyZW50KS5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdCQodGhpcykuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblxuXHRcdGlmIChwYXJlbnQuaGFzQ2xhc3MoJ2RndHgtc3dpdGNoLW5vJykpXG5cdFx0XHRwYXJlbnQucmVtb3ZlQ2xhc3MoJ2RndHgtc3dpdGNoLW5vJykuYWRkQ2xhc3MoJ2RndHgtc3dpdGNoLXllcycpO1xuXHRcdGVsc2Vcblx0XHRcdHBhcmVudC5yZW1vdmVDbGFzcygnZGd0eC1zd2l0Y2gteWVzJykuYWRkQ2xhc3MoJ2RndHgtc3dpdGNoLW5vJyk7XG5cdH0pO1xuXG5cdC8vIEZyaWVuZGx5VXJsXG5cdCQoJ2lucHV0W2lkKj1cInRyYW5zbGF0YWJsZVJld3JpdGVcIl0nKS5vbigna2V5dXAgYmx1cicsIGZ1bmN0aW9uKCl7XG5cdFx0JCh0aGlzKS52YWwodG9SZXdyaXRlVXJsKCQodGhpcykudmFsKCkpKTtcblx0fSk7XG5cblx0JCgnaW5wdXRbaWQqPVwidHJhbnNsYXRhYmxlTmFtZVwiXScpLm9uKCdibHVyJywgZnVuY3Rpb24oKXtcblx0XHR2YXIgYXNzb2NpYXRlUmV3cml0ZUZpZWxkSWQgPSAkKHRoaXMpLnByb3AoJ2lkJykucmVwbGFjZSgnTmFtZScsICdSZXdyaXRlJyk7XG5cdFx0dmFyIGFzc29jaWF0ZVJld3JpdGVGaWVsZCA9ICQoJyMnK2Fzc29jaWF0ZVJld3JpdGVGaWVsZElkKTtcblxuXHRcdGlmIChhc3NvY2lhdGVSZXdyaXRlRmllbGQudmFsKCkgPT0gJycpXG5cdFx0XHRhc3NvY2lhdGVSZXdyaXRlRmllbGQudmFsKHRvUmV3cml0ZVVybCgkKHRoaXMpLnZhbCgpKSk7XG5cdH0pO1xuXG5cdCQoJy5pdGVtLWRlbGV0ZScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpe1xuXHRcdGlmICghY29uZmlybShcIkNvbmZpcm0gZGVsZXRpb24/XCIpKVxuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHR9KTtcblxuXHQvLyBzb3J0YWJsZVxuXHQkKCd0YWJsZS5zb3J0YWJsZSB0Ym9keScpLnNvcnRhYmxlKHtcblx0XHRoYW5kbGU6Jy5zb3J0YWJsZS1pdGVtJyxcblx0XHRpdGVtczondHInLFxuXHRcdGF4aXM6ICd5Jyxcblx0XHR1cGRhdGU6IGZ1bmN0aW9uKGV2ZW50LHVpKSB7XG5cdFx0XHQkLnBvc3QoJCgndGFibGUuc29ydGFibGUnKS5kYXRhKCdzb3J0YWJsZS1hY3Rpb24nKSwgJCh0aGlzKS5zb3J0YWJsZShcInNlcmlhbGl6ZVwiKSk7XG5cdFx0fVxuXHR9KTtcblxuXHQvLyBjb3B5cmlnaHQgXG5cdCQod2luZG93KS5vbignc2Nyb2xsJywgc2hvd2Zvb3Rlcik7XG5cdHNob3dmb290ZXIoKTtcblxuXHQvLyBUaW55TWNlXG5cdHRpbnltY2UuaW5pdCh7XG5cdFx0c2VsZWN0b3I6ICd0ZXh0YXJlYS50aW55bWNlJyxcblx0XHRwbHVnaW5zOiAncHJpbnQgcHJldmlldyBwYXN0ZSBpbXBvcnRjc3Mgc2VhcmNocmVwbGFjZSBhdXRvbGluayBhdXRvc2F2ZSBzYXZlIGRpcmVjdGlvbmFsaXR5IGNvZGUgdmlzdWFsYmxvY2tzIHZpc3VhbGNoYXJzIGZ1bGxzY3JlZW4gaW1hZ2UgbGluayBtZWRpYSB0ZW1wbGF0ZSBjb2Rlc2FtcGxlIHRhYmxlIGNoYXJtYXAgaHIgcGFnZWJyZWFrIG5vbmJyZWFraW5nIGFuY2hvciB0b2MgaW5zZXJ0ZGF0ZXRpbWUgYWR2bGlzdCBsaXN0cyB3b3JkY291bnQgaW1hZ2V0b29scyB0ZXh0cGF0dGVybiBub25lZGl0YWJsZSBoZWxwIGNoYXJtYXAgcXVpY2tiYXJzIGVtb3RpY29ucycsXG5cdFx0aW1hZ2V0b29sc19jb3JzX2hvc3RzOiBbJ3BpY3N1bS5waG90b3MnXSxcblx0XHRtZW51YmFyOiAnZmlsZSBlZGl0IHZpZXcgaW5zZXJ0IGZvcm1hdCB0b29scyB0YWJsZSBoZWxwJyxcblx0XHR0b29sYmFyOiAndW5kbyByZWRvIHwgYm9sZCBpdGFsaWMgdW5kZXJsaW5lIHN0cmlrZXRocm91Z2ggfCBmb250c2VsZWN0IGZvbnRzaXplc2VsZWN0IGZvcm1hdHNlbGVjdCB8IGFsaWdubGVmdCBhbGlnbmNlbnRlciBhbGlnbnJpZ2h0IGFsaWduanVzdGlmeSB8IG91dGRlbnQgaW5kZW50IHwgbnVtbGlzdCBidWxsaXN0IHwgZm9yZWNvbG9yIGJhY2tjb2xvciByZW1vdmVmb3JtYXQgfCBwYWdlYnJlYWsgfCBjaGFybWFwIGVtb3RpY29ucyB8IGZ1bGxzY3JlZW4gIHByZXZpZXcgc2F2ZSBwcmludCB8IGluc2VydGZpbGUgaW1hZ2UgbWVkaWEgdGVtcGxhdGUgbGluayBhbmNob3IgY29kZXNhbXBsZSB8IGx0ciBydGwnLFxuXHRcdHRvb2xiYXJfc3RpY2t5OiB0cnVlLFxuXHRcdGF1dG9zYXZlX2Fza19iZWZvcmVfdW5sb2FkOiB0cnVlLFxuXHRcdGF1dG9zYXZlX2ludGVydmFsOiAnMzBzJyxcblx0XHRhdXRvc2F2ZV9wcmVmaXg6ICd7cGF0aH17cXVlcnl9LXtpZH0tJyxcblx0XHRhdXRvc2F2ZV9yZXN0b3JlX3doZW5fZW1wdHk6IGZhbHNlLFxuXHRcdGF1dG9zYXZlX3JldGVudGlvbjogJzJtJyxcblx0XHRpbWFnZV9hZHZ0YWI6IHRydWUsXG5cdFx0bGlua19saXN0OiBbXSxcblx0XHRpbWFnZV9saXN0OiBbXSxcblx0XHRpbWFnZV9jbGFzc19saXN0OiBbXSxcblx0XHRpbXBvcnRjc3NfYXBwZW5kOiB0cnVlLFxuXHRcdHJlbGF0aXZlX3VybHM6IHRydWUsXG5cdFx0aW1hZ2VfdGl0bGU6IHRydWUsXG4gIFx0XHRhdXRvbWF0aWNfdXBsb2FkczogdHJ1ZSxcblx0XHRmaWxlX3BpY2tlcl9jYWxsYmFjazogZnVuY3Rpb24gKGNiLCB2YWx1ZSwgbWV0YSkge1xuXHRcdCAgICB2YXIgaW5wdXQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuXHRcdCAgICBpbnB1dC5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnZmlsZScpO1xuXHRcdCAgICAvLyBpbnB1dC5zZXRBdHRyaWJ1dGUoJ2FjY2VwdCcsICdpbWFnZS8qJyk7XG5cblx0XHQgICAgLypcblx0XHQgICAgICBOb3RlOiBJbiBtb2Rlcm4gYnJvd3NlcnMgaW5wdXRbdHlwZT1cImZpbGVcIl0gaXMgZnVuY3Rpb25hbCB3aXRob3V0XG5cdFx0ICAgICAgZXZlbiBhZGRpbmcgaXQgdG8gdGhlIERPTSwgYnV0IHRoYXQgbWlnaHQgbm90IGJlIHRoZSBjYXNlIGluIHNvbWUgb2xkZXJcblx0XHQgICAgICBvciBxdWlya3kgYnJvd3NlcnMgbGlrZSBJRSwgc28geW91IG1pZ2h0IHdhbnQgdG8gYWRkIGl0IHRvIHRoZSBET01cblx0XHQgICAgICBqdXN0IGluIGNhc2UsIGFuZCB2aXN1YWxseSBoaWRlIGl0LiBBbmQgZG8gbm90IGZvcmdldCBkbyByZW1vdmUgaXRcblx0XHQgICAgICBvbmNlIHlvdSBkbyBub3QgbmVlZCBpdCBhbnltb3JlLlxuXHRcdCAgICAqL1xuXG5cdFx0ICAgIGlucHV0Lm9uY2hhbmdlID0gZnVuY3Rpb24gKCkge1xuXHRcdCAgICAgIHZhciBmaWxlID0gdGhpcy5maWxlc1swXTtcblxuXHRcdCAgICAgIHZhciByZWFkZXIgPSBuZXcgRmlsZVJlYWRlcigpO1xuXHRcdCAgICAgIHJlYWRlci5vbmxvYWQgPSBmdW5jdGlvbiAoKSB7XG5cdFx0ICAgICAgICAvKlxuXHRcdCAgICAgICAgICBOb3RlOiBOb3cgd2UgbmVlZCB0byByZWdpc3RlciB0aGUgYmxvYiBpbiBUaW55TUNFcyBpbWFnZSBibG9iXG5cdFx0ICAgICAgICAgIHJlZ2lzdHJ5LiBJbiB0aGUgbmV4dCByZWxlYXNlIHRoaXMgcGFydCBob3BlZnVsbHkgd29uJ3QgYmVcblx0XHQgICAgICAgICAgbmVjZXNzYXJ5LCBhcyB3ZSBhcmUgbG9va2luZyB0byBoYW5kbGUgaXQgaW50ZXJuYWxseS5cblx0XHQgICAgICAgICovXG5cdFx0ICAgICAgICB2YXIgaWQgPSAnYmxvYmlkJyArIChuZXcgRGF0ZSgpKS5nZXRUaW1lKCk7XG5cdFx0ICAgICAgICB2YXIgYmxvYkNhY2hlID0gIHRpbnltY2UuYWN0aXZlRWRpdG9yLmVkaXRvclVwbG9hZC5ibG9iQ2FjaGU7XG5cdFx0ICAgICAgICB2YXIgYmFzZTY0ID0gcmVhZGVyLnJlc3VsdC5zcGxpdCgnLCcpWzFdO1xuXHRcdCAgICAgICAgdmFyIGJsb2JJbmZvID0gYmxvYkNhY2hlLmNyZWF0ZShpZCwgZmlsZSwgYmFzZTY0KTtcblx0XHQgICAgICAgIGJsb2JDYWNoZS5hZGQoYmxvYkluZm8pO1xuXG5cdFx0ICAgICAgICAvKiBjYWxsIHRoZSBjYWxsYmFjayBhbmQgcG9wdWxhdGUgdGhlIFRpdGxlIGZpZWxkIHdpdGggdGhlIGZpbGUgbmFtZSAqL1xuXHRcdCAgICAgICAgY2IoYmxvYkluZm8uYmxvYlVyaSgpLCB7IHRpdGxlOiBmaWxlLm5hbWUgfSk7XG5cdFx0ICAgICAgfTtcblx0XHQgICAgICByZWFkZXIucmVhZEFzRGF0YVVSTChmaWxlKTtcblx0XHQgICAgfTtcblxuXHRcdCAgICBpbnB1dC5jbGljaygpO1xuXHRcdH0sXG5cdFx0dGVtcGxhdGVzOiBbXG5cdFx0ICAgIHsgdGl0bGU6ICdOZXcgVGFibGUnLCBkZXNjcmlwdGlvbjogJ2NyZWF0ZXMgYSBuZXcgdGFibGUnLCBjb250ZW50OiAnPGRpdiBjbGFzcz1cIm1jZVRtcGxcIj48dGFibGUgd2lkdGg9XCI5OCUlXCIgIGJvcmRlcj1cIjBcIiBjZWxsc3BhY2luZz1cIjBcIiBjZWxscGFkZGluZz1cIjBcIj48dHI+PHRoIHNjb3BlPVwiY29sXCI+IDwvdGg+PHRoIHNjb3BlPVwiY29sXCI+IDwvdGg+PC90cj48dHI+PHRkPiA8L3RkPjx0ZD4gPC90ZD48L3RyPjwvdGFibGU+PC9kaXY+JyB9LFxuXHRcdFx0eyB0aXRsZTogJ1N0YXJ0aW5nIG15IHN0b3J5JywgZGVzY3JpcHRpb246ICdBIGN1cmUgZm9yIHdyaXRlcnMgYmxvY2snLCBjb250ZW50OiAnT25jZSB1cG9uIGEgdGltZS4uLicgfSxcblx0XHRcdHsgdGl0bGU6ICdOZXcgbGlzdCB3aXRoIGRhdGVzJywgZGVzY3JpcHRpb246ICdOZXcgTGlzdCB3aXRoIGRhdGVzJywgY29udGVudDogJzxkaXYgY2xhc3M9XCJtY2VUbXBsXCI+PHNwYW4gY2xhc3M9XCJjZGF0ZVwiPmNkYXRlPC9zcGFuPjxiciAvPjxzcGFuIGNsYXNzPVwibWRhdGVcIj5tZGF0ZTwvc3Bhbj48aDI+TXkgTGlzdDwvaDI+PHVsPjxsaT48L2xpPjxsaT48L2xpPjwvdWw+PC9kaXY+JyB9XG5cdFx0XSxcblx0XHR0ZW1wbGF0ZV9jZGF0ZV9mb3JtYXQ6ICdbRGF0ZSBDcmVhdGVkIChDREFURSk6ICVkLyVtLyVZIDogJUg6JU06JVNdJyxcblx0XHR0ZW1wbGF0ZV9tZGF0ZV9mb3JtYXQ6ICdbRGF0ZSBNb2RpZmllZCAoTURBVEUpOiAlZC8lbS8lWSA6ICVIOiVNOiVTXScsXG5cdFx0aGVpZ2h0OiA1MDAsXG5cdFx0aW1hZ2VfY2FwdGlvbjogdHJ1ZSxcblx0XHRxdWlja2JhcnNfc2VsZWN0aW9uX3Rvb2xiYXI6ICdib2xkIGl0YWxpYyB8IHF1aWNrbGluayBoMiBoMyBibG9ja3F1b3RlIHF1aWNraW1hZ2UgcXVpY2t0YWJsZScsXG5cdFx0bm9uZWRpdGFibGVfbm9uZWRpdGFibGVfY2xhc3M6ICdtY2VOb25FZGl0YWJsZScsXG5cdFx0dG9vbGJhcl9tb2RlOiAnc2xpZGluZycsXG5cdFx0Y29udGV4dG1lbnU6ICdsaW5rIGltYWdlIGltYWdldG9vbHMgdGFibGUnLFxuXHRcdHNraW46ICdveGlkZScsXG5cdFx0Y29udGVudF9jc3M6ICdkZWZhdWx0Jyxcblx0XHQvLyBjb250ZW50X3N0eWxlOiAnYm9keSB7IGZvbnQtZmFtaWx5OkhlbHZldGljYSxBcmlhbCxzYW5zLXNlcmlmOyBmb250LXNpemU6MTRweCB9J1xuXHR9KTtcblxufSk7XG5cblxuZnVuY3Rpb24gdG9SZXdyaXRlVXJsKHN0cikge1xuICB2YXIgZW5jb2RlZFVybCA9IHN0ci50b1N0cmluZygpLnRvTG93ZXJDYXNlKCk7IC8vIG1ha2UgdGhlIHVybCBsb3dlcmNhc2VcbiAgZW5jb2RlZFVybCA9IGVuY29kZWRVcmwuc3BsaXQoL1xcJisvKS5qb2luKFwiLWFuZC1cIik7IC8vIHJlcGxhY2UgJiB3aXRoIGFuZFxuICBlbmNvZGVkVXJsID0gZW5jb2RlZFVybC5zcGxpdCgvW15hLXowLTldLykuam9pbihcIi1cIik7IC8vIHJlbW92ZSBpbnZhbGlkIGNoYXJhY3RlcnMgXG4gIGVuY29kZWRVcmwgPSBlbmNvZGVkVXJsLnNwbGl0KC8tKy8pLmpvaW4oXCItXCIpOyAvLyByZW1vdmUgZHVwbGljYXRlcyBcbiAgZW5jb2RlZFVybCA9IGVuY29kZWRVcmwudHJpbSgnLScpOyAvLyB0cmltIGxlYWRpbmcgJiB0cmFpbGluZyBjaGFyYWN0ZXJzIFxuXG4gIHJldHVybiBlbmNvZGVkVXJsOyBcbn1cblxuZnVuY3Rpb24gc2hvd2Zvb3RlcigpIHtcblx0dmFyIHRIZWlnaHQgPSAkKCdib2R5JykuaGVpZ2h0KCk7XG5cdHZhciBjdXJyZW50SGVpZ2h0ID0gJCh3aW5kb3cpLmhlaWdodCgpKyQod2luZG93KS5zY3JvbGxUb3AoKSsxMDtcblxuXHRpZiAoY3VycmVudEhlaWdodCA+IHRIZWlnaHQpXG5cdFx0JCgnI2Zvb3RlcicpLmFkZENsYXNzKCdvcGVuJyk7XG5cdGVsc2UgaWYgKCQoJyNmb290ZXInKS5oYXNDbGFzcygnb3BlbicpKVxuXHRcdCQoJyNmb290ZXInKS5yZW1vdmVDbGFzcygnb3BlbicpO1xuXG59XG4iXSwic291cmNlUm9vdCI6IiJ9
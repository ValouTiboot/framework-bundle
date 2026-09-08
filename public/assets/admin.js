/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"admin": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/bundles/digitixframework/assets/";
/******/
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./js/admin.js","vendors~admin"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/admin.js":
/*!*********************!*\
  !*** ./js/admin.js ***!
  \*********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_join__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.join */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.date.to-string */ "./node_modules/core-js/modules/es.date.to-string.js");
/* harmony import */ var core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_date_to_string__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_function_name__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.function.name */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_to_string__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.to-string */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.regexp.exec */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_regexp_to_string__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.regexp.to-string */ "./node_modules/core-js/modules/es.regexp.to-string.js");
/* harmony import */ var core_js_modules_es_regexp_to_string__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_to_string__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.string.replace */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_string_split__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.string.split */ "./node_modules/core-js/modules/es.string.split.js");
/* harmony import */ var core_js_modules_es_string_split__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_split__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_string_trim__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.string.trim */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! jquery-ui/ui/widgets/sortable */ "./node_modules/jquery-ui/ui/widgets/sortable.js");
/* harmony import */ var jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_sortable__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _yaireo_tagify__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @yaireo/tagify */ "./node_modules/@yaireo/tagify/dist/tagify.min.js");
/* harmony import */ var _yaireo_tagify__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_yaireo_tagify__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var tinymce__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tinymce */ "./node_modules/tinymce/tinymce.js");
/* harmony import */ var tinymce__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tinymce__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! tinymce/icons/default/icons.min */ "./node_modules/tinymce/icons/default/icons.min.js");
/* harmony import */ var tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(tinymce_icons_default_icons_min__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! tinymce/themes/silver/theme.min */ "./node_modules/tinymce/themes/silver/theme.min.js");
/* harmony import */ var tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(tinymce_themes_silver_theme_min__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! tinymce/plugins/print/plugin.min */ "./node_modules/tinymce/plugins/print/plugin.min.js");
/* harmony import */ var tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_print_plugin_min__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! tinymce/plugins/preview/plugin.min */ "./node_modules/tinymce/plugins/preview/plugin.min.js");
/* harmony import */ var tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_preview_plugin_min__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! tinymce/plugins/paste/plugin.min */ "./node_modules/tinymce/plugins/paste/plugin.min.js");
/* harmony import */ var tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_paste_plugin_min__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! tinymce/plugins/importcss/plugin.min */ "./node_modules/tinymce/plugins/importcss/plugin.min.js");
/* harmony import */ var tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_importcss_plugin_min__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! tinymce/plugins/searchreplace/plugin.min */ "./node_modules/tinymce/plugins/searchreplace/plugin.min.js");
/* harmony import */ var tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_19___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_searchreplace_plugin_min__WEBPACK_IMPORTED_MODULE_19__);
/* harmony import */ var tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! tinymce/plugins/autolink/plugin.min */ "./node_modules/tinymce/plugins/autolink/plugin.min.js");
/* harmony import */ var tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_20___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_autolink_plugin_min__WEBPACK_IMPORTED_MODULE_20__);
/* harmony import */ var tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! tinymce/plugins/autosave/plugin.min */ "./node_modules/tinymce/plugins/autosave/plugin.min.js");
/* harmony import */ var tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_21___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_autosave_plugin_min__WEBPACK_IMPORTED_MODULE_21__);
/* harmony import */ var tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! tinymce/plugins/save/plugin.min */ "./node_modules/tinymce/plugins/save/plugin.min.js");
/* harmony import */ var tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_22___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_save_plugin_min__WEBPACK_IMPORTED_MODULE_22__);
/* harmony import */ var tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! tinymce/plugins/directionality/plugin.min */ "./node_modules/tinymce/plugins/directionality/plugin.min.js");
/* harmony import */ var tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_23___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_directionality_plugin_min__WEBPACK_IMPORTED_MODULE_23__);
/* harmony import */ var tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! tinymce/plugins/code/plugin.min */ "./node_modules/tinymce/plugins/code/plugin.min.js");
/* harmony import */ var tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_24___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_code_plugin_min__WEBPACK_IMPORTED_MODULE_24__);
/* harmony import */ var tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! tinymce/plugins/visualblocks/plugin.min */ "./node_modules/tinymce/plugins/visualblocks/plugin.min.js");
/* harmony import */ var tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_25___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_visualblocks_plugin_min__WEBPACK_IMPORTED_MODULE_25__);
/* harmony import */ var tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! tinymce/plugins/visualchars/plugin.min */ "./node_modules/tinymce/plugins/visualchars/plugin.min.js");
/* harmony import */ var tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_26___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_visualchars_plugin_min__WEBPACK_IMPORTED_MODULE_26__);
/* harmony import */ var tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! tinymce/plugins/fullscreen/plugin.min */ "./node_modules/tinymce/plugins/fullscreen/plugin.min.js");
/* harmony import */ var tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_27___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_fullscreen_plugin_min__WEBPACK_IMPORTED_MODULE_27__);
/* harmony import */ var tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! tinymce/plugins/image/plugin.min */ "./node_modules/tinymce/plugins/image/plugin.min.js");
/* harmony import */ var tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_28___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_image_plugin_min__WEBPACK_IMPORTED_MODULE_28__);
/* harmony import */ var tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! tinymce/plugins/link/plugin.min */ "./node_modules/tinymce/plugins/link/plugin.min.js");
/* harmony import */ var tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_29___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_link_plugin_min__WEBPACK_IMPORTED_MODULE_29__);
/* harmony import */ var tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! tinymce/plugins/media/plugin.min */ "./node_modules/tinymce/plugins/media/plugin.min.js");
/* harmony import */ var tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_30___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_media_plugin_min__WEBPACK_IMPORTED_MODULE_30__);
/* harmony import */ var tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! tinymce/plugins/template/plugin.min */ "./node_modules/tinymce/plugins/template/plugin.min.js");
/* harmony import */ var tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_31___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_template_plugin_min__WEBPACK_IMPORTED_MODULE_31__);
/* harmony import */ var tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! tinymce/plugins/codesample/plugin.min */ "./node_modules/tinymce/plugins/codesample/plugin.min.js");
/* harmony import */ var tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_32___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_codesample_plugin_min__WEBPACK_IMPORTED_MODULE_32__);
/* harmony import */ var tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! tinymce/plugins/table/plugin.min */ "./node_modules/tinymce/plugins/table/plugin.min.js");
/* harmony import */ var tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_33___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_table_plugin_min__WEBPACK_IMPORTED_MODULE_33__);
/* harmony import */ var tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! tinymce/plugins/charmap/plugin.min */ "./node_modules/tinymce/plugins/charmap/plugin.min.js");
/* harmony import */ var tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_34___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_charmap_plugin_min__WEBPACK_IMPORTED_MODULE_34__);
/* harmony import */ var tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! tinymce/plugins/hr/plugin.min */ "./node_modules/tinymce/plugins/hr/plugin.min.js");
/* harmony import */ var tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_35___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_hr_plugin_min__WEBPACK_IMPORTED_MODULE_35__);
/* harmony import */ var tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! tinymce/plugins/pagebreak/plugin.min */ "./node_modules/tinymce/plugins/pagebreak/plugin.min.js");
/* harmony import */ var tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_36___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_pagebreak_plugin_min__WEBPACK_IMPORTED_MODULE_36__);
/* harmony import */ var tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! tinymce/plugins/nonbreaking/plugin.min */ "./node_modules/tinymce/plugins/nonbreaking/plugin.min.js");
/* harmony import */ var tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_37___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_nonbreaking_plugin_min__WEBPACK_IMPORTED_MODULE_37__);
/* harmony import */ var tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! tinymce/plugins/anchor/plugin.min */ "./node_modules/tinymce/plugins/anchor/plugin.min.js");
/* harmony import */ var tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_38___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_anchor_plugin_min__WEBPACK_IMPORTED_MODULE_38__);
/* harmony import */ var tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! tinymce/plugins/toc/plugin.min */ "./node_modules/tinymce/plugins/toc/plugin.min.js");
/* harmony import */ var tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_39___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_toc_plugin_min__WEBPACK_IMPORTED_MODULE_39__);
/* harmony import */ var tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! tinymce/plugins/insertdatetime/plugin.min */ "./node_modules/tinymce/plugins/insertdatetime/plugin.min.js");
/* harmony import */ var tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_40___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_insertdatetime_plugin_min__WEBPACK_IMPORTED_MODULE_40__);
/* harmony import */ var tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! tinymce/plugins/advlist/plugin.min */ "./node_modules/tinymce/plugins/advlist/plugin.min.js");
/* harmony import */ var tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_41___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_advlist_plugin_min__WEBPACK_IMPORTED_MODULE_41__);
/* harmony import */ var tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! tinymce/plugins/lists/plugin.min */ "./node_modules/tinymce/plugins/lists/plugin.min.js");
/* harmony import */ var tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_42___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_lists_plugin_min__WEBPACK_IMPORTED_MODULE_42__);
/* harmony import */ var tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! tinymce/plugins/wordcount/plugin.min */ "./node_modules/tinymce/plugins/wordcount/plugin.min.js");
/* harmony import */ var tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_43___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_wordcount_plugin_min__WEBPACK_IMPORTED_MODULE_43__);
/* harmony import */ var tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! tinymce/plugins/imagetools/plugin.min */ "./node_modules/tinymce/plugins/imagetools/plugin.min.js");
/* harmony import */ var tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_44___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_imagetools_plugin_min__WEBPACK_IMPORTED_MODULE_44__);
/* harmony import */ var tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! tinymce/plugins/textpattern/plugin.min */ "./node_modules/tinymce/plugins/textpattern/plugin.min.js");
/* harmony import */ var tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_45___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_textpattern_plugin_min__WEBPACK_IMPORTED_MODULE_45__);
/* harmony import */ var tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! tinymce/plugins/noneditable/plugin.min */ "./node_modules/tinymce/plugins/noneditable/plugin.min.js");
/* harmony import */ var tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_46___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_noneditable_plugin_min__WEBPACK_IMPORTED_MODULE_46__);
/* harmony import */ var tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! tinymce/plugins/help/plugin.min */ "./node_modules/tinymce/plugins/help/plugin.min.js");
/* harmony import */ var tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_47___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_help_plugin_min__WEBPACK_IMPORTED_MODULE_47__);
/* harmony import */ var tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! tinymce/plugins/quickbars/plugin.min */ "./node_modules/tinymce/plugins/quickbars/plugin.min.js");
/* harmony import */ var tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_48___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_quickbars_plugin_min__WEBPACK_IMPORTED_MODULE_48__);
/* harmony import */ var tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! tinymce/plugins/emoticons/plugin.min */ "./node_modules/tinymce/plugins/emoticons/plugin.min.js");
/* harmony import */ var tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_49___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_plugin_min__WEBPACK_IMPORTED_MODULE_49__);
/* harmony import */ var tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! tinymce/plugins/emoticons/js/emojiimages.min */ "./node_modules/tinymce/plugins/emoticons/js/emojiimages.min.js");
/* harmony import */ var tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_50___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_js_emojiimages_min__WEBPACK_IMPORTED_MODULE_50__);
/* harmony import */ var tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! tinymce/plugins/emoticons/js/emojis.min */ "./node_modules/tinymce/plugins/emoticons/js/emojis.min.js");
/* harmony import */ var tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_51___default = /*#__PURE__*/__webpack_require__.n(tinymce_plugins_emoticons_js_emojis_min__WEBPACK_IMPORTED_MODULE_51__);











/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
// import './css/style.scss';
var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

__webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.esm.js"); // import $ from "jquery";
// import 'bootstrap/dist/js/bootstrap.min';



 // import '@yaireo/tagify/dist/tagify.min';
// import '@yaireo/tagify/dist/jQuery.tagify.min';
// Import TinyMCE

 // Default icons are required for TinyMCE 5.3 or above

 // A theme is also required

 // Any plugins you want to use has to be imported







































console.log('Hello Webpack Encore! Edit me in asset/admin/_dev/admin.js');
$(document).ready(function () {
  // Menu
  $('.menu-items a[href^="#"]').click(function (e) {
    e.preventDefault();
    $('.menu-items a[href^="#"]').removeClass('expand').parent().find('.sub-menu').slideUp('fast');
    if ($(this).parent().find('.sub-menu').is(':visible')) $(this).removeClass('expand').parent().find('.sub-menu').slideUp('fast');else $(this).addClass('expand').parent().find('.sub-menu').slideDown('fast');
  }); // Tagify

  $('.tag').each(function () {
    var input = $(this);
    new _yaireo_tagify__WEBPACK_IMPORTED_MODULE_11___default.a(input[0], {
      pattern: /^.{0,30}$/
    });
  }); // DGTX-switch

  $('.dgtx-switch').each(function () {
    var check = $('input[type="radio"]:checked', $(this));

    if (typeof check !== 'undefined' && check.val() == 1) {
      $(this).addClass('dgtx-switch-yes');
      check.next().addClass('checked');
    } else {
      $(this).addClass('dgtx-switch-no');
      $('input[value="0"]', $(this)).attr('checked', 'checked').next().addClass('checked');
    }
  });
  $('.dgtx-switch label').on('click', function () {
    var parent = $(this).parent().parent();
    $('label', parent).removeClass('checked');
    $(this).addClass('checked');
    if (parent.hasClass('dgtx-switch-no')) parent.removeClass('dgtx-switch-no').addClass('dgtx-switch-yes');else parent.removeClass('dgtx-switch-yes').addClass('dgtx-switch-no');
  }); // FriendlyUrl

  $('input[id*="translatableRewrite"]').on('keyup blur', function () {
    $(this).val(toRewriteUrl($(this).val()));
  });
  $('input[id*="translatableName"]').on('blur', function () {
    var associateRewriteFieldId = $(this).prop('id').replace('Name', 'Rewrite');
    var associateRewriteField = $('#' + associateRewriteFieldId);
    if (associateRewriteField.val() == '') associateRewriteField.val(toRewriteUrl($(this).val()));
  });
  $('.item-delete').on('click', function (e) {
    if (!confirm("Confirm deletion?")) e.preventDefault();
  }); // sortable

  $('table.sortable tbody').sortable({
    handle: '.sortable-item',
    items: 'tr',
    axis: 'y',
    update: function update(event, ui) {
      $.post($('table.sortable').data('sortable-action'), $(this).sortable("serialize"));
    }
  }); // copyright

  $(window).on('scroll', showfooter);
  showfooter(); // TinyMce

  tinymce__WEBPACK_IMPORTED_MODULE_12___default.a.init({
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
          var blobCache = tinymce__WEBPACK_IMPORTED_MODULE_12___default.a.activeEditor.editorUpload.blobCache;
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
    skin: false,
    content_css: false // content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'

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
  var tHeight = $('body').height();
  var currentHeight = $(window).height() + $(window).scrollTop() + 10;
  if (currentHeight > tHeight) $('#footer').addClass('open');else if ($('#footer').hasClass('open')) $('#footer').removeClass('open');
}

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vanMvYWRtaW4uanMiXSwibmFtZXMiOlsiJCIsInJlcXVpcmUiLCJjb25zb2xlIiwibG9nIiwiZG9jdW1lbnQiLCJyZWFkeSIsImNsaWNrIiwiZSIsInByZXZlbnREZWZhdWx0IiwicmVtb3ZlQ2xhc3MiLCJwYXJlbnQiLCJmaW5kIiwic2xpZGVVcCIsImlzIiwiYWRkQ2xhc3MiLCJzbGlkZURvd24iLCJlYWNoIiwiaW5wdXQiLCJUYWdpZnkiLCJwYXR0ZXJuIiwiY2hlY2siLCJ2YWwiLCJuZXh0IiwiYXR0ciIsIm9uIiwiaGFzQ2xhc3MiLCJ0b1Jld3JpdGVVcmwiLCJhc3NvY2lhdGVSZXdyaXRlRmllbGRJZCIsInByb3AiLCJyZXBsYWNlIiwiYXNzb2NpYXRlUmV3cml0ZUZpZWxkIiwiY29uZmlybSIsInNvcnRhYmxlIiwiaGFuZGxlIiwiaXRlbXMiLCJheGlzIiwidXBkYXRlIiwiZXZlbnQiLCJ1aSIsInBvc3QiLCJkYXRhIiwid2luZG93Iiwic2hvd2Zvb3RlciIsInRpbnltY2UiLCJpbml0Iiwic2VsZWN0b3IiLCJwbHVnaW5zIiwiaW1hZ2V0b29sc19jb3JzX2hvc3RzIiwibWVudWJhciIsInRvb2xiYXIiLCJ0b29sYmFyX3N0aWNreSIsImF1dG9zYXZlX2Fza19iZWZvcmVfdW5sb2FkIiwiYXV0b3NhdmVfaW50ZXJ2YWwiLCJhdXRvc2F2ZV9wcmVmaXgiLCJhdXRvc2F2ZV9yZXN0b3JlX3doZW5fZW1wdHkiLCJhdXRvc2F2ZV9yZXRlbnRpb24iLCJpbWFnZV9hZHZ0YWIiLCJsaW5rX2xpc3QiLCJpbWFnZV9saXN0IiwiaW1hZ2VfY2xhc3NfbGlzdCIsImltcG9ydGNzc19hcHBlbmQiLCJyZWxhdGl2ZV91cmxzIiwiaW1hZ2VfdGl0bGUiLCJhdXRvbWF0aWNfdXBsb2FkcyIsImZpbGVfcGlja2VyX2NhbGxiYWNrIiwiY2IiLCJ2YWx1ZSIsIm1ldGEiLCJjcmVhdGVFbGVtZW50Iiwic2V0QXR0cmlidXRlIiwib25jaGFuZ2UiLCJmaWxlIiwiZmlsZXMiLCJyZWFkZXIiLCJGaWxlUmVhZGVyIiwib25sb2FkIiwiaWQiLCJEYXRlIiwiZ2V0VGltZSIsImJsb2JDYWNoZSIsImFjdGl2ZUVkaXRvciIsImVkaXRvclVwbG9hZCIsImJhc2U2NCIsInJlc3VsdCIsInNwbGl0IiwiYmxvYkluZm8iLCJjcmVhdGUiLCJhZGQiLCJibG9iVXJpIiwidGl0bGUiLCJuYW1lIiwicmVhZEFzRGF0YVVSTCIsInRlbXBsYXRlcyIsImRlc2NyaXB0aW9uIiwiY29udGVudCIsInRlbXBsYXRlX2NkYXRlX2Zvcm1hdCIsInRlbXBsYXRlX21kYXRlX2Zvcm1hdCIsImhlaWdodCIsImltYWdlX2NhcHRpb24iLCJxdWlja2JhcnNfc2VsZWN0aW9uX3Rvb2xiYXIiLCJub25lZGl0YWJsZV9ub25lZGl0YWJsZV9jbGFzcyIsInRvb2xiYXJfbW9kZSIsImNvbnRleHRtZW51Iiwic2tpbiIsImNvbnRlbnRfY3NzIiwic3RyIiwiZW5jb2RlZFVybCIsInRvU3RyaW5nIiwidG9Mb3dlckNhc2UiLCJqb2luIiwidHJpbSIsInRIZWlnaHQiLCJjdXJyZW50SGVpZ2h0Iiwic2Nyb2xsVG9wIl0sIm1hcHBpbmdzIjoiO1FBQUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSxRQUFRLG9CQUFvQjtRQUM1QjtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBLGlCQUFpQiw0QkFBNEI7UUFDN0M7UUFDQTtRQUNBLGtCQUFrQiwyQkFBMkI7UUFDN0M7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBOztRQUVBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTs7O1FBR0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLDBDQUEwQyxnQ0FBZ0M7UUFDMUU7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSx3REFBd0Qsa0JBQWtCO1FBQzFFO1FBQ0EsaURBQWlELGNBQWM7UUFDL0Q7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBLHlDQUF5QyxpQ0FBaUM7UUFDMUUsZ0hBQWdILG1CQUFtQixFQUFFO1FBQ3JJO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMkJBQTJCLDBCQUEwQixFQUFFO1FBQ3ZELGlDQUFpQyxlQUFlO1FBQ2hEO1FBQ0E7UUFDQTs7UUFFQTtRQUNBLHNEQUFzRCwrREFBK0Q7O1FBRXJIO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0E7UUFDQSxnQkFBZ0IsdUJBQXVCO1FBQ3ZDOzs7UUFHQTtRQUNBO1FBQ0E7UUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDdkpBOzs7Ozs7QUFPQTtBQUNBO0FBRUEsSUFBTUEsQ0FBQyxHQUFHQyxtQkFBTyxDQUFDLG9EQUFELENBQWpCOztBQUNBQSxtQkFBTyxDQUFDLG9FQUFELENBQVAsQyxDQUNBO0FBQ0E7OztBQUNBO0NBRUE7QUFDQTtBQUVBOztDQUVBOztDQUVBOztDQUVBOztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQUMsT0FBTyxDQUFDQyxHQUFSLENBQVksNERBQVo7QUFFQUgsQ0FBQyxDQUFDSSxRQUFELENBQUQsQ0FBWUMsS0FBWixDQUFrQixZQUFVO0FBRTNCO0FBQ0FMLEdBQUMsQ0FBQywwQkFBRCxDQUFELENBQThCTSxLQUE5QixDQUFvQyxVQUFTQyxDQUFULEVBQVc7QUFDOUNBLEtBQUMsQ0FBQ0MsY0FBRjtBQUNBUixLQUFDLENBQUMsMEJBQUQsQ0FBRCxDQUE4QlMsV0FBOUIsQ0FBMEMsUUFBMUMsRUFBb0RDLE1BQXBELEdBQTZEQyxJQUE3RCxDQUFrRSxXQUFsRSxFQUErRUMsT0FBL0UsQ0FBdUYsTUFBdkY7QUFFQSxRQUFJWixDQUFDLENBQUMsSUFBRCxDQUFELENBQVFVLE1BQVIsR0FBaUJDLElBQWpCLENBQXNCLFdBQXRCLEVBQW1DRSxFQUFuQyxDQUFzQyxVQUF0QyxDQUFKLEVBQ0NiLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUVMsV0FBUixDQUFvQixRQUFwQixFQUE4QkMsTUFBOUIsR0FBdUNDLElBQXZDLENBQTRDLFdBQTVDLEVBQXlEQyxPQUF6RCxDQUFpRSxNQUFqRSxFQURELEtBR0NaLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUWMsUUFBUixDQUFpQixRQUFqQixFQUEyQkosTUFBM0IsR0FBb0NDLElBQXBDLENBQXlDLFdBQXpDLEVBQXNESSxTQUF0RCxDQUFnRSxNQUFoRTtBQUNELEdBUkQsRUFIMkIsQ0FhM0I7O0FBQ0FmLEdBQUMsQ0FBQyxNQUFELENBQUQsQ0FBVWdCLElBQVYsQ0FBZSxZQUFVO0FBQ3hCLFFBQUlDLEtBQUssR0FBR2pCLENBQUMsQ0FBQyxJQUFELENBQWI7QUFDQSxRQUFJa0Isc0RBQUosQ0FBV0QsS0FBSyxDQUFDLENBQUQsQ0FBaEIsRUFBcUI7QUFDcEJFLGFBQU8sRUFBRztBQURVLEtBQXJCO0FBR0EsR0FMRCxFQWQyQixDQXFCM0I7O0FBQ0FuQixHQUFDLENBQUMsY0FBRCxDQUFELENBQWtCZ0IsSUFBbEIsQ0FBdUIsWUFBVTtBQUNoQyxRQUFJSSxLQUFLLEdBQUdwQixDQUFDLENBQUMsNkJBQUQsRUFBZ0NBLENBQUMsQ0FBQyxJQUFELENBQWpDLENBQWI7O0FBRUEsUUFBSSxPQUFPb0IsS0FBUCxLQUFpQixXQUFqQixJQUFnQ0EsS0FBSyxDQUFDQyxHQUFOLE1BQWUsQ0FBbkQsRUFBc0Q7QUFDckRyQixPQUFDLENBQUMsSUFBRCxDQUFELENBQVFjLFFBQVIsQ0FBaUIsaUJBQWpCO0FBQ0FNLFdBQUssQ0FBQ0UsSUFBTixHQUFhUixRQUFiLENBQXNCLFNBQXRCO0FBQ0EsS0FIRCxNQUdPO0FBQ05kLE9BQUMsQ0FBQyxJQUFELENBQUQsQ0FBUWMsUUFBUixDQUFpQixnQkFBakI7QUFDQWQsT0FBQyxDQUFDLGtCQUFELEVBQXFCQSxDQUFDLENBQUMsSUFBRCxDQUF0QixDQUFELENBQStCdUIsSUFBL0IsQ0FBb0MsU0FBcEMsRUFBOEMsU0FBOUMsRUFBeURELElBQXpELEdBQWdFUixRQUFoRSxDQUF5RSxTQUF6RTtBQUNBO0FBQ0QsR0FWRDtBQVlBZCxHQUFDLENBQUMsb0JBQUQsQ0FBRCxDQUF3QndCLEVBQXhCLENBQTJCLE9BQTNCLEVBQW9DLFlBQVU7QUFFN0MsUUFBSWQsTUFBTSxHQUFHVixDQUFDLENBQUMsSUFBRCxDQUFELENBQVFVLE1BQVIsR0FBaUJBLE1BQWpCLEVBQWI7QUFFQVYsS0FBQyxDQUFDLE9BQUQsRUFBVVUsTUFBVixDQUFELENBQW1CRCxXQUFuQixDQUErQixTQUEvQjtBQUNBVCxLQUFDLENBQUMsSUFBRCxDQUFELENBQVFjLFFBQVIsQ0FBaUIsU0FBakI7QUFFQSxRQUFJSixNQUFNLENBQUNlLFFBQVAsQ0FBZ0IsZ0JBQWhCLENBQUosRUFDQ2YsTUFBTSxDQUFDRCxXQUFQLENBQW1CLGdCQUFuQixFQUFxQ0ssUUFBckMsQ0FBOEMsaUJBQTlDLEVBREQsS0FHQ0osTUFBTSxDQUFDRCxXQUFQLENBQW1CLGlCQUFuQixFQUFzQ0ssUUFBdEMsQ0FBK0MsZ0JBQS9DO0FBQ0QsR0FYRCxFQWxDMkIsQ0ErQzNCOztBQUNBZCxHQUFDLENBQUMsa0NBQUQsQ0FBRCxDQUFzQ3dCLEVBQXRDLENBQXlDLFlBQXpDLEVBQXVELFlBQVU7QUFDaEV4QixLQUFDLENBQUMsSUFBRCxDQUFELENBQVFxQixHQUFSLENBQVlLLFlBQVksQ0FBQzFCLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUXFCLEdBQVIsRUFBRCxDQUF4QjtBQUNBLEdBRkQ7QUFJQXJCLEdBQUMsQ0FBQywrQkFBRCxDQUFELENBQW1Dd0IsRUFBbkMsQ0FBc0MsTUFBdEMsRUFBOEMsWUFBVTtBQUN2RCxRQUFJRyx1QkFBdUIsR0FBRzNCLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUTRCLElBQVIsQ0FBYSxJQUFiLEVBQW1CQyxPQUFuQixDQUEyQixNQUEzQixFQUFtQyxTQUFuQyxDQUE5QjtBQUNBLFFBQUlDLHFCQUFxQixHQUFHOUIsQ0FBQyxDQUFDLE1BQUkyQix1QkFBTCxDQUE3QjtBQUVBLFFBQUlHLHFCQUFxQixDQUFDVCxHQUF0QixNQUErQixFQUFuQyxFQUNDUyxxQkFBcUIsQ0FBQ1QsR0FBdEIsQ0FBMEJLLFlBQVksQ0FBQzFCLENBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUXFCLEdBQVIsRUFBRCxDQUF0QztBQUNELEdBTkQ7QUFRQXJCLEdBQUMsQ0FBQyxjQUFELENBQUQsQ0FBa0J3QixFQUFsQixDQUFxQixPQUFyQixFQUE4QixVQUFTakIsQ0FBVCxFQUFXO0FBQ3hDLFFBQUksQ0FBQ3dCLE9BQU8sQ0FBQyxtQkFBRCxDQUFaLEVBQ0N4QixDQUFDLENBQUNDLGNBQUY7QUFDRCxHQUhELEVBNUQyQixDQWlFM0I7O0FBQ0FSLEdBQUMsQ0FBQyxzQkFBRCxDQUFELENBQTBCZ0MsUUFBMUIsQ0FBbUM7QUFDbENDLFVBQU0sRUFBQyxnQkFEMkI7QUFFbENDLFNBQUssRUFBQyxJQUY0QjtBQUdsQ0MsUUFBSSxFQUFFLEdBSDRCO0FBSWxDQyxVQUFNLEVBQUUsZ0JBQVNDLEtBQVQsRUFBZUMsRUFBZixFQUFtQjtBQUMxQnRDLE9BQUMsQ0FBQ3VDLElBQUYsQ0FBT3ZDLENBQUMsQ0FBQyxnQkFBRCxDQUFELENBQW9Cd0MsSUFBcEIsQ0FBeUIsaUJBQXpCLENBQVAsRUFBb0R4QyxDQUFDLENBQUMsSUFBRCxDQUFELENBQVFnQyxRQUFSLENBQWlCLFdBQWpCLENBQXBEO0FBQ0E7QUFOaUMsR0FBbkMsRUFsRTJCLENBMkUzQjs7QUFDQWhDLEdBQUMsQ0FBQ3lDLE1BQUQsQ0FBRCxDQUFVakIsRUFBVixDQUFhLFFBQWIsRUFBdUJrQixVQUF2QjtBQUNBQSxZQUFVLEdBN0VpQixDQStFM0I7O0FBQ0FDLGlEQUFPLENBQUNDLElBQVIsQ0FBYTtBQUNaQyxZQUFRLEVBQUUsa0JBREU7QUFFWkMsV0FBTyxFQUFFLDhUQUZHO0FBR1pDLHlCQUFxQixFQUFFLENBQUMsZUFBRCxDQUhYO0FBSVpDLFdBQU8sRUFBRSwrQ0FKRztBQUtaQyxXQUFPLEVBQUUsb1ZBTEc7QUFNWkMsa0JBQWMsRUFBRSxJQU5KO0FBT1pDLDhCQUEwQixFQUFFLElBUGhCO0FBUVpDLHFCQUFpQixFQUFFLEtBUlA7QUFTWkMsbUJBQWUsRUFBRSxxQkFUTDtBQVVaQywrQkFBMkIsRUFBRSxLQVZqQjtBQVdaQyxzQkFBa0IsRUFBRSxJQVhSO0FBWVpDLGdCQUFZLEVBQUUsSUFaRjtBQWFaQyxhQUFTLEVBQUUsRUFiQztBQWNaQyxjQUFVLEVBQUUsRUFkQTtBQWVaQyxvQkFBZ0IsRUFBRSxFQWZOO0FBZ0JaQyxvQkFBZ0IsRUFBRSxJQWhCTjtBQWlCWkMsaUJBQWEsRUFBRSxJQWpCSDtBQWtCWkMsZUFBVyxFQUFFLElBbEJEO0FBbUJWQyxxQkFBaUIsRUFBRSxJQW5CVDtBQW9CWkMsd0JBQW9CLEVBQUUsOEJBQVVDLEVBQVYsRUFBY0MsS0FBZCxFQUFxQkMsSUFBckIsRUFBMkI7QUFDN0MsVUFBSWxELEtBQUssR0FBR2IsUUFBUSxDQUFDZ0UsYUFBVCxDQUF1QixPQUF2QixDQUFaO0FBQ0FuRCxXQUFLLENBQUNvRCxZQUFOLENBQW1CLE1BQW5CLEVBQTJCLE1BQTNCLEVBRjZDLENBRzdDOztBQUVBOzs7Ozs7OztBQVFBcEQsV0FBSyxDQUFDcUQsUUFBTixHQUFpQixZQUFZO0FBQzNCLFlBQUlDLElBQUksR0FBRyxLQUFLQyxLQUFMLENBQVcsQ0FBWCxDQUFYO0FBRUEsWUFBSUMsTUFBTSxHQUFHLElBQUlDLFVBQUosRUFBYjs7QUFDQUQsY0FBTSxDQUFDRSxNQUFQLEdBQWdCLFlBQVk7QUFDMUI7Ozs7O0FBS0EsY0FBSUMsRUFBRSxHQUFHLFdBQVksSUFBSUMsSUFBSixFQUFELENBQWFDLE9BQWIsRUFBcEI7QUFDQSxjQUFJQyxTQUFTLEdBQUlwQywrQ0FBTyxDQUFDcUMsWUFBUixDQUFxQkMsWUFBckIsQ0FBa0NGLFNBQW5EO0FBQ0EsY0FBSUcsTUFBTSxHQUFHVCxNQUFNLENBQUNVLE1BQVAsQ0FBY0MsS0FBZCxDQUFvQixHQUFwQixFQUF5QixDQUF6QixDQUFiO0FBQ0EsY0FBSUMsUUFBUSxHQUFHTixTQUFTLENBQUNPLE1BQVYsQ0FBaUJWLEVBQWpCLEVBQXFCTCxJQUFyQixFQUEyQlcsTUFBM0IsQ0FBZjtBQUNBSCxtQkFBUyxDQUFDUSxHQUFWLENBQWNGLFFBQWQ7QUFFQTs7QUFDQXBCLFlBQUUsQ0FBQ29CLFFBQVEsQ0FBQ0csT0FBVCxFQUFELEVBQXFCO0FBQUVDLGlCQUFLLEVBQUVsQixJQUFJLENBQUNtQjtBQUFkLFdBQXJCLENBQUY7QUFDRCxTQWREOztBQWVBakIsY0FBTSxDQUFDa0IsYUFBUCxDQUFxQnBCLElBQXJCO0FBQ0QsT0FwQkQ7O0FBc0JBdEQsV0FBSyxDQUFDWCxLQUFOO0FBQ0gsS0F4RFc7QUF5RFpzRixhQUFTLEVBQUUsQ0FDUDtBQUFFSCxXQUFLLEVBQUUsV0FBVDtBQUFzQkksaUJBQVcsRUFBRSxxQkFBbkM7QUFBMERDLGFBQU8sRUFBRTtBQUFuRSxLQURPLEVBRVY7QUFBRUwsV0FBSyxFQUFFLG1CQUFUO0FBQThCSSxpQkFBVyxFQUFFLDBCQUEzQztBQUF1RUMsYUFBTyxFQUFFO0FBQWhGLEtBRlUsRUFHVjtBQUFFTCxXQUFLLEVBQUUscUJBQVQ7QUFBZ0NJLGlCQUFXLEVBQUUscUJBQTdDO0FBQW9FQyxhQUFPLEVBQUU7QUFBN0UsS0FIVSxDQXpEQztBQThEWkMseUJBQXFCLEVBQUUsNkNBOURYO0FBK0RaQyx5QkFBcUIsRUFBRSw4Q0EvRFg7QUFnRVpDLFVBQU0sRUFBRSxHQWhFSTtBQWlFWkMsaUJBQWEsRUFBRSxJQWpFSDtBQWtFWkMsK0JBQTJCLEVBQUUsZ0VBbEVqQjtBQW1FWkMsaUNBQTZCLEVBQUUsZ0JBbkVuQjtBQW9FWkMsZ0JBQVksRUFBRSxTQXBFRjtBQXFFWkMsZUFBVyxFQUFFLDZCQXJFRDtBQXNFWkMsUUFBSSxFQUFFLEtBdEVNO0FBdUVaQyxlQUFXLEVBQUUsS0F2RUQsQ0F3RVo7O0FBeEVZLEdBQWI7QUEyRUEsQ0EzSkQ7O0FBOEpBLFNBQVM5RSxZQUFULENBQXNCK0UsR0FBdEIsRUFBMkI7QUFDekIsTUFBSUMsVUFBVSxHQUFHRCxHQUFHLENBQUNFLFFBQUosR0FBZUMsV0FBZixFQUFqQixDQUR5QixDQUNzQjs7QUFDL0NGLFlBQVUsR0FBR0EsVUFBVSxDQUFDdEIsS0FBWCxDQUFpQixLQUFqQixFQUF3QnlCLElBQXhCLENBQTZCLE9BQTdCLENBQWIsQ0FGeUIsQ0FFMkI7O0FBQ3BESCxZQUFVLEdBQUdBLFVBQVUsQ0FBQ3RCLEtBQVgsQ0FBaUIsV0FBakIsRUFBOEJ5QixJQUE5QixDQUFtQyxHQUFuQyxDQUFiLENBSHlCLENBRzZCOztBQUN0REgsWUFBVSxHQUFHQSxVQUFVLENBQUN0QixLQUFYLENBQWlCLElBQWpCLEVBQXVCeUIsSUFBdkIsQ0FBNEIsR0FBNUIsQ0FBYixDQUp5QixDQUlzQjs7QUFDL0NILFlBQVUsR0FBR0EsVUFBVSxDQUFDSSxJQUFYLENBQWdCLEdBQWhCLENBQWIsQ0FMeUIsQ0FLVTs7QUFFbkMsU0FBT0osVUFBUDtBQUNEOztBQUVELFNBQVNoRSxVQUFULEdBQXNCO0FBQ3JCLE1BQUlxRSxPQUFPLEdBQUcvRyxDQUFDLENBQUMsTUFBRCxDQUFELENBQVVpRyxNQUFWLEVBQWQ7QUFDQSxNQUFJZSxhQUFhLEdBQUdoSCxDQUFDLENBQUN5QyxNQUFELENBQUQsQ0FBVXdELE1BQVYsS0FBbUJqRyxDQUFDLENBQUN5QyxNQUFELENBQUQsQ0FBVXdFLFNBQVYsRUFBbkIsR0FBeUMsRUFBN0Q7QUFFQSxNQUFJRCxhQUFhLEdBQUdELE9BQXBCLEVBQ0MvRyxDQUFDLENBQUMsU0FBRCxDQUFELENBQWFjLFFBQWIsQ0FBc0IsTUFBdEIsRUFERCxLQUVLLElBQUlkLENBQUMsQ0FBQyxTQUFELENBQUQsQ0FBYXlCLFFBQWIsQ0FBc0IsTUFBdEIsQ0FBSixFQUNKekIsQ0FBQyxDQUFDLFNBQUQsQ0FBRCxDQUFhUyxXQUFiLENBQXlCLE1BQXpCO0FBRUQsQyIsImZpbGUiOiJhZG1pbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIGluc3RhbGwgYSBKU09OUCBjYWxsYmFjayBmb3IgY2h1bmsgbG9hZGluZ1xuIFx0ZnVuY3Rpb24gd2VicGFja0pzb25wQ2FsbGJhY2soZGF0YSkge1xuIFx0XHR2YXIgY2h1bmtJZHMgPSBkYXRhWzBdO1xuIFx0XHR2YXIgbW9yZU1vZHVsZXMgPSBkYXRhWzFdO1xuIFx0XHR2YXIgZXhlY3V0ZU1vZHVsZXMgPSBkYXRhWzJdO1xuXG4gXHRcdC8vIGFkZCBcIm1vcmVNb2R1bGVzXCIgdG8gdGhlIG1vZHVsZXMgb2JqZWN0LFxuIFx0XHQvLyB0aGVuIGZsYWcgYWxsIFwiY2h1bmtJZHNcIiBhcyBsb2FkZWQgYW5kIGZpcmUgY2FsbGJhY2tcbiBcdFx0dmFyIG1vZHVsZUlkLCBjaHVua0lkLCBpID0gMCwgcmVzb2x2ZXMgPSBbXTtcbiBcdFx0Zm9yKDtpIDwgY2h1bmtJZHMubGVuZ3RoOyBpKyspIHtcbiBcdFx0XHRjaHVua0lkID0gY2h1bmtJZHNbaV07XG4gXHRcdFx0aWYoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKGluc3RhbGxlZENodW5rcywgY2h1bmtJZCkgJiYgaW5zdGFsbGVkQ2h1bmtzW2NodW5rSWRdKSB7XG4gXHRcdFx0XHRyZXNvbHZlcy5wdXNoKGluc3RhbGxlZENodW5rc1tjaHVua0lkXVswXSk7XG4gXHRcdFx0fVxuIFx0XHRcdGluc3RhbGxlZENodW5rc1tjaHVua0lkXSA9IDA7XG4gXHRcdH1cbiBcdFx0Zm9yKG1vZHVsZUlkIGluIG1vcmVNb2R1bGVzKSB7XG4gXHRcdFx0aWYoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG1vcmVNb2R1bGVzLCBtb2R1bGVJZCkpIHtcbiBcdFx0XHRcdG1vZHVsZXNbbW9kdWxlSWRdID0gbW9yZU1vZHVsZXNbbW9kdWxlSWRdO1xuIFx0XHRcdH1cbiBcdFx0fVxuIFx0XHRpZihwYXJlbnRKc29ucEZ1bmN0aW9uKSBwYXJlbnRKc29ucEZ1bmN0aW9uKGRhdGEpO1xuXG4gXHRcdHdoaWxlKHJlc29sdmVzLmxlbmd0aCkge1xuIFx0XHRcdHJlc29sdmVzLnNoaWZ0KCkoKTtcbiBcdFx0fVxuXG4gXHRcdC8vIGFkZCBlbnRyeSBtb2R1bGVzIGZyb20gbG9hZGVkIGNodW5rIHRvIGRlZmVycmVkIGxpc3RcbiBcdFx0ZGVmZXJyZWRNb2R1bGVzLnB1c2guYXBwbHkoZGVmZXJyZWRNb2R1bGVzLCBleGVjdXRlTW9kdWxlcyB8fCBbXSk7XG5cbiBcdFx0Ly8gcnVuIGRlZmVycmVkIG1vZHVsZXMgd2hlbiBhbGwgY2h1bmtzIHJlYWR5XG4gXHRcdHJldHVybiBjaGVja0RlZmVycmVkTW9kdWxlcygpO1xuIFx0fTtcbiBcdGZ1bmN0aW9uIGNoZWNrRGVmZXJyZWRNb2R1bGVzKCkge1xuIFx0XHR2YXIgcmVzdWx0O1xuIFx0XHRmb3IodmFyIGkgPSAwOyBpIDwgZGVmZXJyZWRNb2R1bGVzLmxlbmd0aDsgaSsrKSB7XG4gXHRcdFx0dmFyIGRlZmVycmVkTW9kdWxlID0gZGVmZXJyZWRNb2R1bGVzW2ldO1xuIFx0XHRcdHZhciBmdWxmaWxsZWQgPSB0cnVlO1xuIFx0XHRcdGZvcih2YXIgaiA9IDE7IGogPCBkZWZlcnJlZE1vZHVsZS5sZW5ndGg7IGorKykge1xuIFx0XHRcdFx0dmFyIGRlcElkID0gZGVmZXJyZWRNb2R1bGVbal07XG4gXHRcdFx0XHRpZihpbnN0YWxsZWRDaHVua3NbZGVwSWRdICE9PSAwKSBmdWxmaWxsZWQgPSBmYWxzZTtcbiBcdFx0XHR9XG4gXHRcdFx0aWYoZnVsZmlsbGVkKSB7XG4gXHRcdFx0XHRkZWZlcnJlZE1vZHVsZXMuc3BsaWNlKGktLSwgMSk7XG4gXHRcdFx0XHRyZXN1bHQgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IGRlZmVycmVkTW9kdWxlWzBdKTtcbiBcdFx0XHR9XG4gXHRcdH1cblxuIFx0XHRyZXR1cm4gcmVzdWx0O1xuIFx0fVxuXG4gXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBvYmplY3QgdG8gc3RvcmUgbG9hZGVkIGFuZCBsb2FkaW5nIGNodW5rc1xuIFx0Ly8gdW5kZWZpbmVkID0gY2h1bmsgbm90IGxvYWRlZCwgbnVsbCA9IGNodW5rIHByZWxvYWRlZC9wcmVmZXRjaGVkXG4gXHQvLyBQcm9taXNlID0gY2h1bmsgbG9hZGluZywgMCA9IGNodW5rIGxvYWRlZFxuIFx0dmFyIGluc3RhbGxlZENodW5rcyA9IHtcbiBcdFx0XCJhZG1pblwiOiAwXG4gXHR9O1xuXG4gXHR2YXIgZGVmZXJyZWRNb2R1bGVzID0gW107XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiL2J1bmRsZXMvZGlnaXRpeGZyYW1ld29yay9hc3NldHMvXCI7XG5cbiBcdHZhciBqc29ucEFycmF5ID0gd2luZG93W1wid2VicGFja0pzb25wXCJdID0gd2luZG93W1wid2VicGFja0pzb25wXCJdIHx8IFtdO1xuIFx0dmFyIG9sZEpzb25wRnVuY3Rpb24gPSBqc29ucEFycmF5LnB1c2guYmluZChqc29ucEFycmF5KTtcbiBcdGpzb25wQXJyYXkucHVzaCA9IHdlYnBhY2tKc29ucENhbGxiYWNrO1xuIFx0anNvbnBBcnJheSA9IGpzb25wQXJyYXkuc2xpY2UoKTtcbiBcdGZvcih2YXIgaSA9IDA7IGkgPCBqc29ucEFycmF5Lmxlbmd0aDsgaSsrKSB3ZWJwYWNrSnNvbnBDYWxsYmFjayhqc29ucEFycmF5W2ldKTtcbiBcdHZhciBwYXJlbnRKc29ucEZ1bmN0aW9uID0gb2xkSnNvbnBGdW5jdGlvbjtcblxuXG4gXHQvLyBhZGQgZW50cnkgbW9kdWxlIHRvIGRlZmVycmVkIGxpc3RcbiBcdGRlZmVycmVkTW9kdWxlcy5wdXNoKFtcIi4vanMvYWRtaW4uanNcIixcInZlbmRvcnN+YWRtaW5cIl0pO1xuIFx0Ly8gcnVuIGRlZmVycmVkIG1vZHVsZXMgd2hlbiByZWFkeVxuIFx0cmV0dXJuIGNoZWNrRGVmZXJyZWRNb2R1bGVzKCk7XG4iLCIvKlxuICogV2VsY29tZSB0byB5b3VyIGFwcCdzIG1haW4gSmF2YVNjcmlwdCBmaWxlIVxuICpcbiAqIFdlIHJlY29tbWVuZCBpbmNsdWRpbmcgdGhlIGJ1aWx0IHZlcnNpb24gb2YgdGhpcyBKYXZhU2NyaXB0IGZpbGVcbiAqIChhbmQgaXRzIENTUyBmaWxlKSBpbiB5b3VyIGJhc2UgbGF5b3V0IChiYXNlLmh0bWwudHdpZykuXG4gKi9cblxuLy8gYW55IENTUyB5b3UgaW1wb3J0IHdpbGwgb3V0cHV0IGludG8gYSBzaW5nbGUgY3NzIGZpbGUgKGFwcC5jc3MgaW4gdGhpcyBjYXNlKVxuLy8gaW1wb3J0ICcuL2Nzcy9zdHlsZS5zY3NzJztcblxuY29uc3QgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xucmVxdWlyZSgnYm9vdHN0cmFwJyk7XG4vLyBpbXBvcnQgJCBmcm9tIFwianF1ZXJ5XCI7XG4vLyBpbXBvcnQgJ2Jvb3RzdHJhcC9kaXN0L2pzL2Jvb3RzdHJhcC5taW4nO1xuaW1wb3J0ICdqcXVlcnktdWkvdWkvd2lkZ2V0cy9zb3J0YWJsZSc7XG5pbXBvcnQgVGFnaWZ5IGZyb20gJ0B5YWlyZW8vdGFnaWZ5Jztcbi8vIGltcG9ydCAnQHlhaXJlby90YWdpZnkvZGlzdC90YWdpZnkubWluJztcbi8vIGltcG9ydCAnQHlhaXJlby90YWdpZnkvZGlzdC9qUXVlcnkudGFnaWZ5Lm1pbic7XG5cbi8vIEltcG9ydCBUaW55TUNFXG5pbXBvcnQgdGlueW1jZSBmcm9tICd0aW55bWNlJztcbi8vIERlZmF1bHQgaWNvbnMgYXJlIHJlcXVpcmVkIGZvciBUaW55TUNFIDUuMyBvciBhYm92ZVxuaW1wb3J0ICd0aW55bWNlL2ljb25zL2RlZmF1bHQvaWNvbnMubWluJztcbi8vIEEgdGhlbWUgaXMgYWxzbyByZXF1aXJlZFxuaW1wb3J0ICd0aW55bWNlL3RoZW1lcy9zaWx2ZXIvdGhlbWUubWluJztcbi8vIEFueSBwbHVnaW5zIHlvdSB3YW50IHRvIHVzZSBoYXMgdG8gYmUgaW1wb3J0ZWRcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3ByaW50L3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvcHJldmlldy9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3Bhc3RlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvaW1wb3J0Y3NzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvc2VhcmNocmVwbGFjZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2F1dG9saW5rL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvYXV0b3NhdmUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9zYXZlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvZGlyZWN0aW9uYWxpdHkvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9jb2RlL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdmlzdWFsYmxvY2tzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdmlzdWFsY2hhcnMvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9mdWxsc2NyZWVuL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvaW1hZ2UvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9saW5rL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvbWVkaWEvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy90ZW1wbGF0ZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2NvZGVzYW1wbGUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy90YWJsZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2NoYXJtYXAvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9oci9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL3BhZ2VicmVhay9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL25vbmJyZWFraW5nL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvYW5jaG9yL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdG9jL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvaW5zZXJ0ZGF0ZXRpbWUvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9hZHZsaXN0L3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvbGlzdHMvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy93b3JkY291bnQvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9pbWFnZXRvb2xzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvdGV4dHBhdHRlcm4vcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9ub25lZGl0YWJsZS9wbHVnaW4ubWluJztcbmltcG9ydCAndGlueW1jZS9wbHVnaW5zL2hlbHAvcGx1Z2luLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9jaGFybWFwL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvcXVpY2tiYXJzL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvZW1vdGljb25zL3BsdWdpbi5taW4nO1xuaW1wb3J0ICd0aW55bWNlL3BsdWdpbnMvZW1vdGljb25zL2pzL2Vtb2ppaW1hZ2VzLm1pbic7XG5pbXBvcnQgJ3RpbnltY2UvcGx1Z2lucy9lbW90aWNvbnMvanMvZW1vamlzLm1pbic7XG5cbmNvbnNvbGUubG9nKCdIZWxsbyBXZWJwYWNrIEVuY29yZSEgRWRpdCBtZSBpbiBhc3NldC9hZG1pbi9fZGV2L2FkbWluLmpzJyk7XG5cbiQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCl7XG5cblx0Ly8gTWVudVxuXHQkKCcubWVudS1pdGVtcyBhW2hyZWZePVwiI1wiXScpLmNsaWNrKGZ1bmN0aW9uKGUpe1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHQkKCcubWVudS1pdGVtcyBhW2hyZWZePVwiI1wiXScpLnJlbW92ZUNsYXNzKCdleHBhbmQnKS5wYXJlbnQoKS5maW5kKCcuc3ViLW1lbnUnKS5zbGlkZVVwKCdmYXN0Jyk7XG5cblx0XHRpZiAoJCh0aGlzKS5wYXJlbnQoKS5maW5kKCcuc3ViLW1lbnUnKS5pcygnOnZpc2libGUnKSlcblx0XHRcdCQodGhpcykucmVtb3ZlQ2xhc3MoJ2V4cGFuZCcpLnBhcmVudCgpLmZpbmQoJy5zdWItbWVudScpLnNsaWRlVXAoJ2Zhc3QnKTtcblx0XHRlbHNlXG5cdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdleHBhbmQnKS5wYXJlbnQoKS5maW5kKCcuc3ViLW1lbnUnKS5zbGlkZURvd24oJ2Zhc3QnKTtcblx0fSk7XG5cblx0Ly8gVGFnaWZ5XG5cdCQoJy50YWcnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0dmFyIGlucHV0ID0gJCh0aGlzKTtcblx0XHRuZXcgVGFnaWZ5KGlucHV0WzBdLCB7XG5cdFx0XHRwYXR0ZXJuIDogL14uezAsMzB9JC8sXG5cdFx0fSk7XG5cdH0pO1xuXG5cdC8vIERHVFgtc3dpdGNoXG5cdCQoJy5kZ3R4LXN3aXRjaCcpLmVhY2goZnVuY3Rpb24oKXtcblx0XHR2YXIgY2hlY2sgPSAkKCdpbnB1dFt0eXBlPVwicmFkaW9cIl06Y2hlY2tlZCcsICQodGhpcykpO1xuXG5cdFx0aWYgKHR5cGVvZiBjaGVjayAhPT0gJ3VuZGVmaW5lZCcgJiYgY2hlY2sudmFsKCkgPT0gMSkge1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnZGd0eC1zd2l0Y2gteWVzJyk7XG5cdFx0XHRjaGVjay5uZXh0KCkuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnZGd0eC1zd2l0Y2gtbm8nKTtcblx0XHRcdCQoJ2lucHV0W3ZhbHVlPVwiMFwiXScsICQodGhpcykpLmF0dHIoJ2NoZWNrZWQnLCdjaGVja2VkJykubmV4dCgpLmFkZENsYXNzKCdjaGVja2VkJyk7XG5cdFx0fVxuXHR9KTtcblxuXHQkKCcuZGd0eC1zd2l0Y2ggbGFiZWwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpe1xuXG5cdFx0dmFyIHBhcmVudCA9ICQodGhpcykucGFyZW50KCkucGFyZW50KCk7XG5cblx0XHQkKCdsYWJlbCcsIHBhcmVudCkucmVtb3ZlQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHQkKHRoaXMpLmFkZENsYXNzKCdjaGVja2VkJyk7XG5cblx0XHRpZiAocGFyZW50Lmhhc0NsYXNzKCdkZ3R4LXN3aXRjaC1ubycpKVxuXHRcdFx0cGFyZW50LnJlbW92ZUNsYXNzKCdkZ3R4LXN3aXRjaC1ubycpLmFkZENsYXNzKCdkZ3R4LXN3aXRjaC15ZXMnKTtcblx0XHRlbHNlXG5cdFx0XHRwYXJlbnQucmVtb3ZlQ2xhc3MoJ2RndHgtc3dpdGNoLXllcycpLmFkZENsYXNzKCdkZ3R4LXN3aXRjaC1ubycpO1xuXHR9KTtcblxuXHQvLyBGcmllbmRseVVybFxuXHQkKCdpbnB1dFtpZCo9XCJ0cmFuc2xhdGFibGVSZXdyaXRlXCJdJykub24oJ2tleXVwIGJsdXInLCBmdW5jdGlvbigpe1xuXHRcdCQodGhpcykudmFsKHRvUmV3cml0ZVVybCgkKHRoaXMpLnZhbCgpKSk7XG5cdH0pO1xuXG5cdCQoJ2lucHV0W2lkKj1cInRyYW5zbGF0YWJsZU5hbWVcIl0nKS5vbignYmx1cicsIGZ1bmN0aW9uKCl7XG5cdFx0dmFyIGFzc29jaWF0ZVJld3JpdGVGaWVsZElkID0gJCh0aGlzKS5wcm9wKCdpZCcpLnJlcGxhY2UoJ05hbWUnLCAnUmV3cml0ZScpO1xuXHRcdHZhciBhc3NvY2lhdGVSZXdyaXRlRmllbGQgPSAkKCcjJythc3NvY2lhdGVSZXdyaXRlRmllbGRJZCk7XG5cblx0XHRpZiAoYXNzb2NpYXRlUmV3cml0ZUZpZWxkLnZhbCgpID09ICcnKVxuXHRcdFx0YXNzb2NpYXRlUmV3cml0ZUZpZWxkLnZhbCh0b1Jld3JpdGVVcmwoJCh0aGlzKS52YWwoKSkpO1xuXHR9KTtcblxuXHQkKCcuaXRlbS1kZWxldGUnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKXtcblx0XHRpZiAoIWNvbmZpcm0oXCJDb25maXJtIGRlbGV0aW9uP1wiKSlcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0fSk7XG5cblx0Ly8gc29ydGFibGVcblx0JCgndGFibGUuc29ydGFibGUgdGJvZHknKS5zb3J0YWJsZSh7XG5cdFx0aGFuZGxlOicuc29ydGFibGUtaXRlbScsXG5cdFx0aXRlbXM6J3RyJyxcblx0XHRheGlzOiAneScsXG5cdFx0dXBkYXRlOiBmdW5jdGlvbihldmVudCx1aSkge1xuXHRcdFx0JC5wb3N0KCQoJ3RhYmxlLnNvcnRhYmxlJykuZGF0YSgnc29ydGFibGUtYWN0aW9uJyksICQodGhpcykuc29ydGFibGUoXCJzZXJpYWxpemVcIikpO1xuXHRcdH1cblx0fSk7XG5cblx0Ly8gY29weXJpZ2h0XG5cdCQod2luZG93KS5vbignc2Nyb2xsJywgc2hvd2Zvb3Rlcik7XG5cdHNob3dmb290ZXIoKTtcblxuXHQvLyBUaW55TWNlXG5cdHRpbnltY2UuaW5pdCh7XG5cdFx0c2VsZWN0b3I6ICd0ZXh0YXJlYS50aW55bWNlJyxcblx0XHRwbHVnaW5zOiAncHJpbnQgcHJldmlldyBwYXN0ZSBpbXBvcnRjc3Mgc2VhcmNocmVwbGFjZSBhdXRvbGluayBhdXRvc2F2ZSBzYXZlIGRpcmVjdGlvbmFsaXR5IGNvZGUgdmlzdWFsYmxvY2tzIHZpc3VhbGNoYXJzIGZ1bGxzY3JlZW4gaW1hZ2UgbGluayBtZWRpYSB0ZW1wbGF0ZSBjb2Rlc2FtcGxlIHRhYmxlIGNoYXJtYXAgaHIgcGFnZWJyZWFrIG5vbmJyZWFraW5nIGFuY2hvciB0b2MgaW5zZXJ0ZGF0ZXRpbWUgYWR2bGlzdCBsaXN0cyB3b3JkY291bnQgaW1hZ2V0b29scyB0ZXh0cGF0dGVybiBub25lZGl0YWJsZSBoZWxwIGNoYXJtYXAgcXVpY2tiYXJzIGVtb3RpY29ucycsXG5cdFx0aW1hZ2V0b29sc19jb3JzX2hvc3RzOiBbJ3BpY3N1bS5waG90b3MnXSxcblx0XHRtZW51YmFyOiAnZmlsZSBlZGl0IHZpZXcgaW5zZXJ0IGZvcm1hdCB0b29scyB0YWJsZSBoZWxwJyxcblx0XHR0b29sYmFyOiAndW5kbyByZWRvIHwgYm9sZCBpdGFsaWMgdW5kZXJsaW5lIHN0cmlrZXRocm91Z2ggfCBmb250c2VsZWN0IGZvbnRzaXplc2VsZWN0IGZvcm1hdHNlbGVjdCB8IGFsaWdubGVmdCBhbGlnbmNlbnRlciBhbGlnbnJpZ2h0IGFsaWduanVzdGlmeSB8IG91dGRlbnQgaW5kZW50IHwgbnVtbGlzdCBidWxsaXN0IHwgZm9yZWNvbG9yIGJhY2tjb2xvciByZW1vdmVmb3JtYXQgfCBwYWdlYnJlYWsgfCBjaGFybWFwIGVtb3RpY29ucyB8IGZ1bGxzY3JlZW4gIHByZXZpZXcgc2F2ZSBwcmludCB8IGluc2VydGZpbGUgaW1hZ2UgbWVkaWEgdGVtcGxhdGUgbGluayBhbmNob3IgY29kZXNhbXBsZSB8IGx0ciBydGwnLFxuXHRcdHRvb2xiYXJfc3RpY2t5OiB0cnVlLFxuXHRcdGF1dG9zYXZlX2Fza19iZWZvcmVfdW5sb2FkOiB0cnVlLFxuXHRcdGF1dG9zYXZlX2ludGVydmFsOiAnMzBzJyxcblx0XHRhdXRvc2F2ZV9wcmVmaXg6ICd7cGF0aH17cXVlcnl9LXtpZH0tJyxcblx0XHRhdXRvc2F2ZV9yZXN0b3JlX3doZW5fZW1wdHk6IGZhbHNlLFxuXHRcdGF1dG9zYXZlX3JldGVudGlvbjogJzJtJyxcblx0XHRpbWFnZV9hZHZ0YWI6IHRydWUsXG5cdFx0bGlua19saXN0OiBbXSxcblx0XHRpbWFnZV9saXN0OiBbXSxcblx0XHRpbWFnZV9jbGFzc19saXN0OiBbXSxcblx0XHRpbXBvcnRjc3NfYXBwZW5kOiB0cnVlLFxuXHRcdHJlbGF0aXZlX3VybHM6IHRydWUsXG5cdFx0aW1hZ2VfdGl0bGU6IHRydWUsXG4gIFx0XHRhdXRvbWF0aWNfdXBsb2FkczogdHJ1ZSxcblx0XHRmaWxlX3BpY2tlcl9jYWxsYmFjazogZnVuY3Rpb24gKGNiLCB2YWx1ZSwgbWV0YSkge1xuXHRcdCAgICB2YXIgaW5wdXQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuXHRcdCAgICBpbnB1dC5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnZmlsZScpO1xuXHRcdCAgICAvLyBpbnB1dC5zZXRBdHRyaWJ1dGUoJ2FjY2VwdCcsICdpbWFnZS8qJyk7XG5cblx0XHQgICAgLypcblx0XHQgICAgICBOb3RlOiBJbiBtb2Rlcm4gYnJvd3NlcnMgaW5wdXRbdHlwZT1cImZpbGVcIl0gaXMgZnVuY3Rpb25hbCB3aXRob3V0XG5cdFx0ICAgICAgZXZlbiBhZGRpbmcgaXQgdG8gdGhlIERPTSwgYnV0IHRoYXQgbWlnaHQgbm90IGJlIHRoZSBjYXNlIGluIHNvbWUgb2xkZXJcblx0XHQgICAgICBvciBxdWlya3kgYnJvd3NlcnMgbGlrZSBJRSwgc28geW91IG1pZ2h0IHdhbnQgdG8gYWRkIGl0IHRvIHRoZSBET01cblx0XHQgICAgICBqdXN0IGluIGNhc2UsIGFuZCB2aXN1YWxseSBoaWRlIGl0LiBBbmQgZG8gbm90IGZvcmdldCBkbyByZW1vdmUgaXRcblx0XHQgICAgICBvbmNlIHlvdSBkbyBub3QgbmVlZCBpdCBhbnltb3JlLlxuXHRcdCAgICAqL1xuXG5cdFx0ICAgIGlucHV0Lm9uY2hhbmdlID0gZnVuY3Rpb24gKCkge1xuXHRcdCAgICAgIHZhciBmaWxlID0gdGhpcy5maWxlc1swXTtcblxuXHRcdCAgICAgIHZhciByZWFkZXIgPSBuZXcgRmlsZVJlYWRlcigpO1xuXHRcdCAgICAgIHJlYWRlci5vbmxvYWQgPSBmdW5jdGlvbiAoKSB7XG5cdFx0ICAgICAgICAvKlxuXHRcdCAgICAgICAgICBOb3RlOiBOb3cgd2UgbmVlZCB0byByZWdpc3RlciB0aGUgYmxvYiBpbiBUaW55TUNFcyBpbWFnZSBibG9iXG5cdFx0ICAgICAgICAgIHJlZ2lzdHJ5LiBJbiB0aGUgbmV4dCByZWxlYXNlIHRoaXMgcGFydCBob3BlZnVsbHkgd29uJ3QgYmVcblx0XHQgICAgICAgICAgbmVjZXNzYXJ5LCBhcyB3ZSBhcmUgbG9va2luZyB0byBoYW5kbGUgaXQgaW50ZXJuYWxseS5cblx0XHQgICAgICAgICovXG5cdFx0ICAgICAgICB2YXIgaWQgPSAnYmxvYmlkJyArIChuZXcgRGF0ZSgpKS5nZXRUaW1lKCk7XG5cdFx0ICAgICAgICB2YXIgYmxvYkNhY2hlID0gIHRpbnltY2UuYWN0aXZlRWRpdG9yLmVkaXRvclVwbG9hZC5ibG9iQ2FjaGU7XG5cdFx0ICAgICAgICB2YXIgYmFzZTY0ID0gcmVhZGVyLnJlc3VsdC5zcGxpdCgnLCcpWzFdO1xuXHRcdCAgICAgICAgdmFyIGJsb2JJbmZvID0gYmxvYkNhY2hlLmNyZWF0ZShpZCwgZmlsZSwgYmFzZTY0KTtcblx0XHQgICAgICAgIGJsb2JDYWNoZS5hZGQoYmxvYkluZm8pO1xuXG5cdFx0ICAgICAgICAvKiBjYWxsIHRoZSBjYWxsYmFjayBhbmQgcG9wdWxhdGUgdGhlIFRpdGxlIGZpZWxkIHdpdGggdGhlIGZpbGUgbmFtZSAqL1xuXHRcdCAgICAgICAgY2IoYmxvYkluZm8uYmxvYlVyaSgpLCB7IHRpdGxlOiBmaWxlLm5hbWUgfSk7XG5cdFx0ICAgICAgfTtcblx0XHQgICAgICByZWFkZXIucmVhZEFzRGF0YVVSTChmaWxlKTtcblx0XHQgICAgfTtcblxuXHRcdCAgICBpbnB1dC5jbGljaygpO1xuXHRcdH0sXG5cdFx0dGVtcGxhdGVzOiBbXG5cdFx0ICAgIHsgdGl0bGU6ICdOZXcgVGFibGUnLCBkZXNjcmlwdGlvbjogJ2NyZWF0ZXMgYSBuZXcgdGFibGUnLCBjb250ZW50OiAnPGRpdiBjbGFzcz1cIm1jZVRtcGxcIj48dGFibGUgd2lkdGg9XCI5OCUlXCIgIGJvcmRlcj1cIjBcIiBjZWxsc3BhY2luZz1cIjBcIiBjZWxscGFkZGluZz1cIjBcIj48dHI+PHRoIHNjb3BlPVwiY29sXCI+IDwvdGg+PHRoIHNjb3BlPVwiY29sXCI+IDwvdGg+PC90cj48dHI+PHRkPiA8L3RkPjx0ZD4gPC90ZD48L3RyPjwvdGFibGU+PC9kaXY+JyB9LFxuXHRcdFx0eyB0aXRsZTogJ1N0YXJ0aW5nIG15IHN0b3J5JywgZGVzY3JpcHRpb246ICdBIGN1cmUgZm9yIHdyaXRlcnMgYmxvY2snLCBjb250ZW50OiAnT25jZSB1cG9uIGEgdGltZS4uLicgfSxcblx0XHRcdHsgdGl0bGU6ICdOZXcgbGlzdCB3aXRoIGRhdGVzJywgZGVzY3JpcHRpb246ICdOZXcgTGlzdCB3aXRoIGRhdGVzJywgY29udGVudDogJzxkaXYgY2xhc3M9XCJtY2VUbXBsXCI+PHNwYW4gY2xhc3M9XCJjZGF0ZVwiPmNkYXRlPC9zcGFuPjxiciAvPjxzcGFuIGNsYXNzPVwibWRhdGVcIj5tZGF0ZTwvc3Bhbj48aDI+TXkgTGlzdDwvaDI+PHVsPjxsaT48L2xpPjxsaT48L2xpPjwvdWw+PC9kaXY+JyB9XG5cdFx0XSxcblx0XHR0ZW1wbGF0ZV9jZGF0ZV9mb3JtYXQ6ICdbRGF0ZSBDcmVhdGVkIChDREFURSk6ICVkLyVtLyVZIDogJUg6JU06JVNdJyxcblx0XHR0ZW1wbGF0ZV9tZGF0ZV9mb3JtYXQ6ICdbRGF0ZSBNb2RpZmllZCAoTURBVEUpOiAlZC8lbS8lWSA6ICVIOiVNOiVTXScsXG5cdFx0aGVpZ2h0OiA1MDAsXG5cdFx0aW1hZ2VfY2FwdGlvbjogdHJ1ZSxcblx0XHRxdWlja2JhcnNfc2VsZWN0aW9uX3Rvb2xiYXI6ICdib2xkIGl0YWxpYyB8IHF1aWNrbGluayBoMiBoMyBibG9ja3F1b3RlIHF1aWNraW1hZ2UgcXVpY2t0YWJsZScsXG5cdFx0bm9uZWRpdGFibGVfbm9uZWRpdGFibGVfY2xhc3M6ICdtY2VOb25FZGl0YWJsZScsXG5cdFx0dG9vbGJhcl9tb2RlOiAnc2xpZGluZycsXG5cdFx0Y29udGV4dG1lbnU6ICdsaW5rIGltYWdlIGltYWdldG9vbHMgdGFibGUnLFxuXHRcdHNraW46IGZhbHNlLFxuXHRcdGNvbnRlbnRfY3NzOiBmYWxzZSxcblx0XHQvLyBjb250ZW50X3N0eWxlOiAnYm9keSB7IGZvbnQtZmFtaWx5OkhlbHZldGljYSxBcmlhbCxzYW5zLXNlcmlmOyBmb250LXNpemU6MTRweCB9J1xuXHR9KTtcblxufSk7XG5cblxuZnVuY3Rpb24gdG9SZXdyaXRlVXJsKHN0cikge1xuICB2YXIgZW5jb2RlZFVybCA9IHN0ci50b1N0cmluZygpLnRvTG93ZXJDYXNlKCk7IC8vIG1ha2UgdGhlIHVybCBsb3dlcmNhc2VcbiAgZW5jb2RlZFVybCA9IGVuY29kZWRVcmwuc3BsaXQoL1xcJisvKS5qb2luKFwiLWFuZC1cIik7IC8vIHJlcGxhY2UgJiB3aXRoIGFuZFxuICBlbmNvZGVkVXJsID0gZW5jb2RlZFVybC5zcGxpdCgvW15hLXowLTldLykuam9pbihcIi1cIik7IC8vIHJlbW92ZSBpbnZhbGlkIGNoYXJhY3RlcnNcbiAgZW5jb2RlZFVybCA9IGVuY29kZWRVcmwuc3BsaXQoLy0rLykuam9pbihcIi1cIik7IC8vIHJlbW92ZSBkdXBsaWNhdGVzXG4gIGVuY29kZWRVcmwgPSBlbmNvZGVkVXJsLnRyaW0oJy0nKTsgLy8gdHJpbSBsZWFkaW5nICYgdHJhaWxpbmcgY2hhcmFjdGVyc1xuXG4gIHJldHVybiBlbmNvZGVkVXJsO1xufVxuXG5mdW5jdGlvbiBzaG93Zm9vdGVyKCkge1xuXHR2YXIgdEhlaWdodCA9ICQoJ2JvZHknKS5oZWlnaHQoKTtcblx0dmFyIGN1cnJlbnRIZWlnaHQgPSAkKHdpbmRvdykuaGVpZ2h0KCkrJCh3aW5kb3cpLnNjcm9sbFRvcCgpKzEwO1xuXG5cdGlmIChjdXJyZW50SGVpZ2h0ID4gdEhlaWdodClcblx0XHQkKCcjZm9vdGVyJykuYWRkQ2xhc3MoJ29wZW4nKTtcblx0ZWxzZSBpZiAoJCgnI2Zvb3RlcicpLmhhc0NsYXNzKCdvcGVuJykpXG5cdFx0JCgnI2Zvb3RlcicpLnJlbW92ZUNsYXNzKCdvcGVuJyk7XG5cbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=
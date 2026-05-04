/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!***********************************************!*\
  !*** ./resources/assets/js/custom/helpers.js ***!
  \***********************************************/
__webpack_require__.r(__webpack_exports__);


window.listen = function (event, selector, callback) {
  $(document).on(event, selector, callback);
};
window.listenClick = function (selector, callback) {
  $(document).on('click', selector, callback);
};
window.listenSubmit = function (selector, callback) {
  $(document).on('submit', selector, callback);
};
window.listenHiddenBsModal = function (selector, callback) {
  $(document).on('hidden.bs.modal', selector, callback);
};
window.listenChange = function (selector, callback) {
  $(document).on('change', selector, callback);
};
window.listenKeyup = function (selector, callback) {
  $(document).on('keyup', selector, callback);
};
/******/ })()
;
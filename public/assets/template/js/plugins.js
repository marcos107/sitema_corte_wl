/*
Template Name: Nomzie - Admin & Dashboard Template
Author: Themesbrand
Version: 1.1.0
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Common Plugins Js File
*/

//Common plugins
if (document.querySelectorAll("[toast-list]").length > 0 || document.querySelectorAll('[data-choices]').length > 0 || document.querySelectorAll("[data-provider]").length > 0) {
  document.writeln("<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/toastify-js'></script>");
  document.writeln("<script type='text/javascript' src='assets/template/libs/choices.js/public/assets/scripts/choices.min.js'></script>");
  document.writeln("<script type='text/javascript' src='assets/template/libs/flatpickr/flatpickr.min.js'></script>");
}

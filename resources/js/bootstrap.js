window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.$ = window.jQuery = require('jquery');

window.bootstrap = require('bootstrap');
const flatpickr = require("flatpickr");

import "bootstrap-notify/bootstrap-notify.min.js";

import "select2/dist/js/select2.js";

import { createApp } from 'vue';

import Toastify from 'toastify-js';
window.Toastify = Toastify;
// assets/app.js
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
require('bootstrap');
import './bootstrap.js';
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/global.scss';
import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

$(function () {
    $('.js-friends-select').select2({
        placeholder: 'Choose friends',
        width: '100%',
        theme: "bootstrap-5",
        closeOnSelect: false,
    });
});

import { Popover } from 'bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach((popoverTriggerEl) => {
        new Popover(popoverTriggerEl);
    });
});

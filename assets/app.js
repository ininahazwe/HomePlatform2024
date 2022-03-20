/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

const $ = require('jquery');
require('bootstrap');

global.$ = global.jQuery = $;

/*$(function () {
    $('.select-tags').select2();
})*/

import { Tooltip, Toast,  Popover } from "bootstrap";
// start the Stimulus application
import './bootstrap';

import './js/projects/projects';
import './js/projects/teams';
import './js/projects/editions';

import './js/meanmenu.min';
import './js/dropzone.min';
import './js/form-validator.min';
import './js/jquery';
import './js/jquery-ui';
import './js/jquery.ajaxchimp.min';
import './js/jquery.appear.min';
import './js/jquery.magnific-popup.min';
import './js/metismenu.min';
import './js/odometer.min';
import './js/owl.carousel.min';
import './js/range-slider.min';
import './js/selectize.min';
import './js/simplebar.min';
import './js/tweenMax.min';
//import './js/select2';
import './js/sticky-sidebar.min';
import './js/custom';

/*$(document).ready(function() {
    $('.select-tags-edit').select2({
        theme: 'flat',
        tags: true,
        tokenSeparators: [',', ' ']
    }).on('change', function(e){
        let label = $(this).find("[data-select2-tag=true]");
        if(label.length && $.inArray(label.val(), $(this).val() ==! -1)){
            $.ajax({
                url: "/tag/new/ajax/"+label.val(),
                type: "POST"
            }).done(function(data){
                console.log(data)
                label.replaceWith(`<option selected value="${data.id}">${label.val()}</option>`);
            });
        }
    });
});*/

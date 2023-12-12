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

import Filter from "./js/filter"

new Filter(document.querySelector('.js-filter'))

import './js/projects/projects';
import './js/projects/teams';
import './js/projects/editions';
import './js/projects/partenaires';
import './js/projects/about';
import './js/projects/categories';

import './js/all';

import './js/validate'
import './js/form_validator'
import './js/form-validator-reset-password'

import 'datatables.net';
import 'datatables.net-bs4';

$(document).ready(function () {

    let tables = ['#datatable', '#datatable1', '#datatable2', '#datatable3'];
    tables.forEach(function (item) {
        if ($(item).attr('id')) {
            $(item).DataTable({
                "order": [[1, 'asc']],
                'columnDefs': [
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        }
                    }
                ],
                'select': {
                    'style': 'multi'
                },
            });
        }
    });

    window.setTimeout(function () {
        $(".message-box").fadeOut(1000);
    }, 7000);

});

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

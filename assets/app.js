/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

import { Tooltip, Toast,  Popover } from "bootstrap";

// start the Stimulus application
import './bootstrap';
import './js/all';
import TomSelect from "tom-select";

$(document).ready(function() {
    $('.select-tags').select2();
});

function bindSelect(select){
    new TomSelect(select, {
        hideSelected: true,
        closeAfterSelect: true,
        load: async (query, callback)=>{
            const url = `${select.dataset.remote}?q=${encodeURIComponent(query)}`
        }
    })
}
Array.from(document.querySelectorAll('select[multiple]')).map(bindSelect)


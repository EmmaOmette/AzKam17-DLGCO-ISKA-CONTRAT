const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#litige_fait').summernote()
    $('#litige_avisJuridique').summernote()
})


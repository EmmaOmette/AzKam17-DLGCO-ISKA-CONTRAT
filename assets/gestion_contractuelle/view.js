const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#contrat_view_clausesParticulieres').summernote()
    $('#contrat_view_libConditionModification').summernote()
})


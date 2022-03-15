const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#contrat_edit_clausesParticulieres').summernote()
    $('#contrat_edit_libConditionModification').summernote()
})


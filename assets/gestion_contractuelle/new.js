const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#contrat_new_clausesParticulieres').summernote()
    $('#contrat_new_libConditionModification').summernote()
})


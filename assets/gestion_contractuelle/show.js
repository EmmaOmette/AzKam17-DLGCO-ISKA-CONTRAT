const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#contrat_clausesParticulieres').summernote()
    $('#contrat_libConditionModification').summernote()
})


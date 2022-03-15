const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#traitement_clausesParticulieres').summernote()
    $('#traitement_libConditionModification').summernote()
})


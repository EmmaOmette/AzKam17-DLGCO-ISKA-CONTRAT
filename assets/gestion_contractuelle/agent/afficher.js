const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#afficher_clausesParticulieres').summernote()
    $('#afficher_libConditionModification').summernote()
})


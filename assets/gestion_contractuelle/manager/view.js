const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#contrat_manager_view_clausesParticulieres').summernote()
    $('#contrat_manager_view_libConditionModification').summernote()
})


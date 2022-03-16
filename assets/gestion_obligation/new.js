const $ = require('jquery');
global.$ = global.jQuery = $;


$(document).ready(function (){
    $('#obligation_sourceDisposition').summernote()
    $('#obligation_reference').summernote()
    $('#obligation_pointsAbordes').summernote()
    $('#obligation_obligation').summernote()
    $('#obligation_sanctions').summernote()
    $('#obligation_prevues').summernote()
    $('#obligation_impact').summernote()
})


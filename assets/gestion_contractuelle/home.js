const $ = require('jquery');
global.$ = global.jQuery = $;


$("#example1").DataTable({
    "autoWidth": false,
    "buttons": ["csv", "excel", "pdf"],
    "info": true,
    "lengthChange": false,
    "ordering": false,
    "paging": true,
    "pageLength": 6,
    "responsive": true,
    "searching": true,
    "oLanguage": {
        'sSearch': 'Recherche'
    }
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

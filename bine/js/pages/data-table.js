//[Data Table Javascript]



$(function () {
    "use strict";

    $('#example1').DataTable({
        "lengthMenu": [[10, 25, 50,100,500, -1], [10, 25, 50,100,500, "All"]]
    } );
    $('#example2').DataTable({
		"lengthMenu": [[10, 25, 50,100,500, -1], [10, 25, 50,100,500, "All"]],
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    });
	
	
	$('#example').DataTable( {
		dom: 'Bfrtip',
		buttons: [
			'copy', 'csv', 
			{
			    extend: 'excelHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer:true
			}
			, 'print',
			{
			    extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer:true
			}
		]
	} );
	
  }); // End of use strict
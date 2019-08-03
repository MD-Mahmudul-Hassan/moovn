$(document).ready(function() {
	$('#property_table').dataTable({"sPaginationType":"full_numbers","iDisplayLength": 50,"aoColumnDefs":[{"bSortable":false,"aTargets":[4]}],"aaSorting":[[3,'desc']]});
	$('#comment_table').dataTable({"sPaginationType":"full_numbers","iDisplayLength": 50,"aoColumnDefs":[{"bSortable":false,"aTargets":[3,4]}],"aaSorting":[[1,'desc']]});
	$('#draft_table').dataTable({"sPaginationType":"full_numbers","iDisplayLength": 50,"aoColumnDefs":[{"bSortable":false,"aTargets":[2,3]}],"aaSorting":[[1,'desc']]});
	$('#publish_table').dataTable({"sPaginationType":"full_numbers","iDisplayLength": 50,"aoColumnDefs":[{"bSortable":false,"aTargets":[2,3]}],"aaSorting":[[1,'desc']]});
});
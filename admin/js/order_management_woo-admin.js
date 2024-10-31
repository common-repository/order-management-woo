(function( $ ) {
	'use strict';
	let headers = '';
	let date = order_management_woo.date;
	let name_page = order_management_woo.name_page;
	//let messageTop = name_page;
	$(document).ready(function($){
		let header2 = '';
		let header_tags = $('.headers-tags');
		if(header_tags.length > 0){
			headers = JSON.parse(header_tags.attr('data-headers'));
			if(headers){
				header2 = [headers[0]];
			}
		}
		let table = $('#normalTable').DataTable({
			select: true,
			dom:'lBfrtpi',
			"aaSorting": [],
			"lengthMenu": [[10,20,30,40,50,100,200,300,400,500,1000,1500,-1], [10,20,30,40,50,100,200,300,400,500,1000,1500,"All"]],
			layout: {
		        topStart: 'buttons'
		    },
			buttons:[
				{
					extend: 'copyHtml5',
					title : name_page+' '+date,
					//messageTop: messageTop,
					filename: name_page+'-'+date
				},
				{
					extend: 'excelHtml5',
					title : name_page+' '+date,
					//messageTop: messageTop,
					filename: name_page+'-'+date
				},
				{
					extend: 'csvHtml5',
					title : name_page+' '+date,
					//messageTop: messageTop,
					filename: name_page+'-'+date
				},
				{
					extend: 'pdfHtml5',
					title : name_page+' '+date,
					//messageTop: messageTop,
					filename: name_page+'-'+date,
					customize: function (doc) {
						doc['pageSize'] = 'A2';
						doc['header']=(function(currentPage) {
							let header_name = header2[currentPage-1];
							return [
								{
									text:header_name,
									alignment:'center',
									margin:[10,10,10,10],
									style:'header',
									fontSize: 20
								}
							]
						});
					}
				}
			]
		});
		jQuery('.order_management_woo .order_date').datepicker({
			dateFormat: 'yy-mm-dd'
		});

		let separator = ' to ', dateFormat = 'YYYY-MM-DD';
		let options = {
			locale: {
				format: dateFormat,
				separator: separator,
			}
		};
		$('.order_management_woo #delivery_date_calendar').daterangepicker(options);
		let nonce = $('.order_management_woo #plugin_nonce').val();
		$('.order_management_woo select.select2').select2();
		$('.order_management_woo select.select2.product_ids').select2({
			placeholder: 'Search Product',
			ajax: {
				url: order_management_woo.ajax_url,
				dataType: 'json',
				delay: 250,
				type: 'POST',
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'order_management_woo_get_products',
						nonce:nonce
					};
				},
				processResults: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.product_id,
								text: item.product_name
							}
						})
					};
				},
				cache: true
			}
		});
	});
})( jQuery );

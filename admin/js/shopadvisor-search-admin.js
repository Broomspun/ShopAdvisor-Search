(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write $ code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(function() {
        function processResult(response) {
            $('button i.ajax_loading').removeClass('fa fa-refresh fa-spin fa-fw');

            $('#total_products').val(response.totals);
            $('.tablenav.top').removeClass('hidden').addClass('show');
            $('span.displaying-num').html(response.totals+' items');

            var page_number = parseInt($('#pg_number').val())+1;

            $('.current-page').val(page_number);
            $('.total-pages').html(response.pages);
            $('#total_pages').val(response.pages);

            if(page_number>1){
                $('.first_sign').hide();
                $('.prev_sign').hide();
                $('.first-page').show();
                $('.prev-page').show();
            }
            else {
                $('.first_sign').show();
                $('.prev_sign').show();
                $('.first-page').hide();
                $('.prev-page').hide();
            }

            if(page_number==response.pages){
                $('.next-page').hide();
                $('.last-page').hide();
                $('.last_sign').show();
                $('.next_sign').show();
            }
            else {
                $('.next-page').show();
                $('.last-page').show();
                $('.last_sign').hide();
                $('.next_sign').hide();
            }


            $('#shopadvisor_import_form #result').html('');
            var $html ='';
            $html += '<table class="table table-bordered"><thead><tr class="success"><th ><input id="cb-select-all-1" type="checkbox"><span style="margin-left: 10px">Product</span></th><th>Product Information</th><th>Price($)</th><th>Location Info</th><th>Category</th></tr></thead>';
            $.each(response.product, function(key, value){
                var $temp = '';
                var $single_uploading_btn='';//

                var $retailers='';
                if(value.retailer!==undefined){
                    $retailers += '<sp><span>id:'+value.retailer.id+'</span>&nbsp;&nbsp;';
                    $retailers += '<span>name:'+value.retailer.name+'</span>&nbsp;&nbsp;';
                    $retailers += '<span><img src="'+value.retailer.logo+'"/></span>';
                }

                $retailers +='<p><strong><em>Retailer assigned location id: </em></strong>'+value.retLocationId+'</p>'

                var $location = '<p><Strong><em>Timezone: </em></Strong>'+value.timezone+'</p>';
                $location += '<p><Strong><em>ShopAdvisor assigned location name: </em></Strong>'+value.locName+'</p>';
                $location +='<p><Strong><em>Location ID: </em></Strong>'+value.location_id+'</p>'
                $location += '<p><Strong><em>Location Latitude & Longitude: </em></Strong>'+value.location_lat_long+'</p>'
                $location += '<p><Strong><em>Hours: </em></Strong>'+value.hours+'</p>'

                var $loc_marks = ['Country','City','Address1', 'State', 'Postal'];

                if(value.address!=undefined){
                    $.each(value.address, function (index, vv) {
                        $location +='<p><strong><em>'+$loc_marks[index]+': </em></strong>'+vv+'</p>';
                    })
                }

                $html += '<tr>'
                    +'<td class="col-sm-1 center-block "><input type="checkbox" class="ss_ckb" name="ss_gi[]" value="'+key+'"><img class="center-block img-responsive" src="'+value['thumb_image']+'"/></td>'
                    +'<td class="col-sm-4">'
                    +'<p><strong><em>Product ID: </em></strong>'+key+'</p>'
                    +'<p><Strong><em>Sku: </em></Strong>'+value['sku']+'</p>'
                    +'<p><Strong><em>External Product ID: </em></Strong>'+value.externalproductid+'</p>'
                    +'<p><Strong><em>Barcode: </em></Strong>'+value.barcode+'</p>'
                    +'<p><Strong><em>Title: </em></Strong>'+value['title']+'</p>'
                    +'<p>'+'<strong><em>Description: </em></strong>'+value['descriptionShort']+'</p>'
                    +'<p>'+'<strong><em>Brand: </em></strong>'+value.brand+'</p>'
                    +'<p><Strong><em>Product Type: </em></Strong>'+value.productType+'</p>'
                    +'<p><strong><em>External Link: </em></strong><a target="_blank" href="'+value.url+'">'+value['url']+'</a></p>'
                    +'<p><Strong><em>Availability: </em></Strong>'+value['quantityText']+'</p>'
                    +'<p><Strong><em>Last Updated: </em></Strong>'+value.lastUpdated+'</p>'
                    +'<p><Strong><em>Retailer: </em></Strong>'+$retailers+'</p>'
                    +'<td><p>'+value['price']+'</p><p>'+value['currency']+'</p></td>'
                    +'<td class="col-sm-4">'+$location
                    +'<p><Strong><em>Location ID: </em></Strong>'+value.location_id+'</p>'
                    +'<p><Strong><em>Location Latitude & Longitude: </em></Strong>'+value.location_lat_long+'</p>'
                    +'</td>'
                    +'<td>'+'<p>'+value['productCategory']+'</p></td>'
                    +'</tr>';
            });
            $html += '</table>';
            $('#shopadvisor_import_form #result').append($html);
        }

        $("form#shopadvisor_import_form").submit(function (e) {
            e.preventDefault();
            $('#pg_number').val(0);

            var indicators = ['shopadvisor_pid','shopadvisor_q','shopadvisor_brand','shopadvisor_categories','shopadvisor_model','shopadvisor_name'];

            var validate_input = false;
            $.each(indicators,function (index, value) {
                var temp = $('#'+value).val();
                if(temp=='' || temp=='-1') return true;//continue
                validate_input = true;
                return false;//break;
            });

            if(!validate_input){
                alert('Enter any value among productId, keywords, brand, category, model, and name');
                return false;
            }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajax_parms.ajaxurl,
                data: {
                    action: 'shopadvisor_search_import_products',
					shopadvisor_ul: $('#shopadvisor_ul').val(),
                    shopadvisor_import_nonce: $('#shopadvisor_import_nonce').val(),
                    shopadvisor_apikey: $('#shopadvisor_apikey').val(),
                    shopadvisor_ppp: $('#shopadvisor_ppp').val(),
                    pg_number: $('#pg_number').val(),
                    shopadvisor_q: $('#shopadvisor_q').val(),
                    category: $('#shopadvisor_categories').val(),
                    shopadvisor_brand: $('#shopadvisor_brand').val(),
                    shopadvisor_model: $('#shopadvisor_model').val(),
                    shopadvisor_name: $('#shopadvisor_name').val(),
                    shopadvisor_pid: $('#shopadvisor_pid').val(),
                    maxPerRetailer: $('#maxPerRetailer').val(),
                    maxLocationsPerRetailer: $('#maxLocationsPerRetailer').val(),
                },
                beforeSend: function () {
                    $('button i.ajax_loading').addClass('fa fa-refresh fa-spin fa-fw');
                },
                success: (function (res) {
                    $('button i.ajax_loading').removeClass('fa fa-refresh fa-spin fa-fw');
                    processResult(res);
                    $('#shopadvisor-csv-file').fadeIn(1000);
                })

            });

        });

    });
})( jQuery );

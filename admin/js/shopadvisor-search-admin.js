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
            jQuery('button i.ajax_loading').removeClass('fa fa-refresh fa-spin fa-fw');

            jQuery('#total_products').val(response.totals);
            jQuery('.tablenav.top').removeClass('hidden').addClass('show');
            jQuery('span.displaying-num').html(response.totals+' items');

            var page_number = parseInt(jQuery('#pg_number').val())+1;

            jQuery('.current-page').val(page_number);
            jQuery('.total-pages').html(response.pages);
            jQuery('#total_pages').val(response.pages);

            if(page_number>1){
                jQuery('.first_sign').hide();
                jQuery('.prev_sign').hide();
                jQuery('.first-page').show();
                jQuery('.prev-page').show();
            }
            else {
                jQuery('.first_sign').show();
                jQuery('.prev_sign').show();
                jQuery('.first-page').hide();
                jQuery('.prev-page').hide();
            }

            if(page_number==response.pages){
                jQuery('.next-page').hide();
                jQuery('.last-page').hide();
                jQuery('.last_sign').show();
                jQuery('.next_sign').show();
            }
            else {
                jQuery('.next-page').show();
                jQuery('.last-page').show();
                jQuery('.last_sign').hide();
                jQuery('.next_sign').hide();
            }


            jQuery('#shopadvisor_import_form #result').html('');
            var $html ='';
            $html += '<table class="table table-bordered"><thead><tr class="success"><th ><input id="cb-select-all-1" type="checkbox"><span style="margin-left: 10px">Product</span></th><th>Information</th><th>Price($)</th><th>Sizes</th><th>Category</th><th>Gallery</th></tr></thead>';
            jQuery.each(response.product, function(key, value){
                var $temp = '';
                // var $single_uploading_btn='<p><button type="submit" class="single_product_uploading btn btn-default"><i class="ajax_loading_single"></i>Upload</button></p>';
                var $single_uploading_btn='';//

                var $images = '';
                if(value.small_gallery!==undefined) {
                    jQuery.each(value.small_gallery, function (index, image) {
                        $images += '<img style="padding: 5px; height: 100px;" src="' + image + '" />';
                    });
                }

                var $colors = '';
                if(value.color!==undefined) {
                    if (value.color.name !== undefined) {
                        jQuery.each(value.color.name, function (index, colorname) {
                            $colors += '<div class="color-name" style="float: left;"><span>' + colorname + '</span>';
                            if (value.color.image[index] != null)
                                $colors += '<img style="height: 60px;" src="' + value.color.image[index] + '" />';
                            $colors += '</div>';
                        });
                    }
                }

                var $sizes = '';
                if(value.size!==undefined) {
                    if (value.size.name !== undefined) {
                        jQuery.each(value.size.name, function (index, sizename) {
                            $sizes += '<p><strong><em>' + sizename + '</em></strong><br/>' + value.size.canonicalSize[index] + '</p>';

                        });
                    }
                }
                var $retailers='';
                if(value.retailer!==undefined){
                    $retailers += '<sp><span>id:'+value.retailer.id+'</span>&nbsp;&nbsp;';
                    $retailers += '<span>name:'+value.retailer.name+'</span>&nbsp;&nbsp;';
                    $retailers += '<span>score:'+value.retailer.score+'</span></p>';
                }

                $html += '<tr>'
                    +'<td class="col-sm-1 center-block "><input type="checkbox" class="ss_ckb" name="ss_gi[]" value="'+key+'"><img class="center-block img-responsive" src="'+value['thumbnail']+'"/></td>'
                    +'<td class="col-sm-6">'
                    +'<p><strong><em>Product ID: </em></strong>'+key+'</p>'
                    +'<p><Strong><em>Title: </em></Strong>'+value['title']+'</p>'
                    +'<p>'+'<strong><em>Description: </em></strong>'+value['shortdescription']+'</p>'
                    +'<p><strong><em>External Link: </em></strong><a target="_blank" href="'+value.link+'">'+value['link']+'</a></p>'
                    +'<p><Strong><em>Sku: </em></Strong>'+value['sku']+'</p>'
                    +'<p><Strong><em>Retailer: </em></Strong>'+$retailers+'</p>'
                    +'<p><Strong><em>Colors: </em></Strong>'+$colors+'</p>'
                    +$single_uploading_btn+'</td>'
                    +'<td>'+value['price']+'</td>'
                    +'<td style="width:80px;">'+$sizes+'</td>'
                    +'<td>'+'<p>'+value['categories_names']+'</p></td>'
                    +'<td>'+$images+'</td></tr>';
            });
            $html += '</table>';
            jQuery('#shopstyle_import_form #result').append($html);
        }

        $("form#shopadvisor_import_form").submit(function (e) {
            e.preventDefault();
            $('#pg_number').val(0);

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
                    category: $('#shopadvisor_categories').val()
                },
                beforeSend: function () {
                    $('button i.ajax_loading').addClass('fa fa-refresh fa-spin fa-fw');
                },
                success: (function (res) {
                    $('button i.ajax_loading').removeClass('fa fa-refresh fa-spin fa-fw');
                    //processResult(res);
                    $('#shopadvisor-csv-file').fadeIn(1000);
                })

            });

        });

    });
})( jQuery );

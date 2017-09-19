<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1 style="text-align: left;" class="col-sm-offset-2">ShopAdvisor Products Search API Setting & import Products</h1>
</div>
<p>
    Returns product information for retailers and store locations carrying products matching the search query
    criteria (keyword, barcode, etc.). All parameters are case insensitive, and many have abbreviated names as
    well as verbose names.
</p>
<p>
    At least one of the six product indicators (<strong><em>productId, keywords, brand, category, model, and name</em></strong>) is
    required when executing queries using the products endpoint.
</p>
<hr/>
<?php
//$settings = (array)get_option('shopadvisor-search-settings');
//$this->API_token = $settings['apiKey'];

$product_categories = array(
    'Animals and Pet Supplies', 'Apparel and Accessories','Arts and Entertainment','Baby & Toddler','Business and Industry',
'Camera and Optics','Electronics','Food, Beverage and Tobacco','Furniture','Hardware','Health and Beauty','Home and Garden',
'Luggage and Bags','Mature','Media','Office Supplies','Software','Sporting Good','Toys and Games','Vehicles and Parts'
)
?>
<div class="container-fluid shopadvisor-search">

    <form class="form-horizontal" type="POST" id="shopadvisor_import_form">
        <div class="col-sm-offset-1 col-sm-8">
            <input type="hidden" name="shopadvisor_import_nonce" id="shopadvisor_import_nonce"
                   value="<?php echo wp_create_nonce('shopadvisor_search_nonce'); ?>"/>
            <input type="hidden" name="pg_number" id="pg_number" value="0"/>
            <input type="hidden" name="total_products" id="total_products" value="0"/>
            <input type="hidden" name="total_pages" id="total_pages" value="0"/>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_apikey">API key<sup>*</sup></label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" id="shopadvisor_apikey"
                           name="shopadvisor_apikey" required
                           placeholder="your API key" value="<?php echo $this->apiKey; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_ul">User Locations<sup>*</sup></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control"  id="shopadvisor_ul" name="shopadvisor_ul" required
                           placeholder="01720">
                    <p>Allowable values include zip code or comma separated latitude,longitude pair.</p>
                </div>
            <label class="col-sm-1 control-label" for="shopadvisor_q">Keywords</label>
            <div class="col-sm-3">
                <input class="form-control" type="text" id="shopadvisor_q"
                       name="shopadvisor_q"
                       placeholder="computer+!mouse" value="">
                <p>Separate multiple keywords with a plus sign, comma, or space. Accepts "!" as a negator.</p>
            </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_categories">Category</label>
                <div class="col-sm-3">
                    <select class="form-control" type="text" id="shopadvisor_categories"
                            name="shopadvisor_categories">
                        <option value="-1">Choose any category</option>
                        <?php
                        foreach ($product_categories as $category){
                            echo '<option value="'.$category.'">'.$category.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <label class="col-sm-1 control-label" for="shopadvisor_categories">Brand</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" id="shopadvisor_brand" name="shopadvisor_brand"
                           placeholder="Logitech"">
<!--                    <select class="form-control" type="text" id="shopadvisor_brands" name="shopadvisor_brands">-->
<!--                        <option value="-1">Choose any Brand</option>-->
<!--                    </select>-->
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_categories">Product Model</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" id="shopadvisor_model" name="shopadvisor_model"
                           placeholder="M310"">
                    <p>Product model to search for.<em>Accepts "!" as a negator.</em></p>
                </div>
                <label class="col-sm-1 control-label" for="shopadvisor_name">Product Name</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" id="shopadvisor_name" name="shopadvisor_name"
                           placeholder="Logitech Mouse M310">
                    <p>Product name to search for.<em>Accepts "!" as a negator.</em></p>
                </div>

            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_ppp">Products per page(1~100)</label>
                <div class="col-sm-2">
                    <input class="form-control" type="text" id="shopadvisor_ppp" name="shopadvisor_ppp" required
                           placeholder="Pages per page" value="10">
                </div>
                <label class="col-sm-2 control-label" for="shopadvisor_pid">Product ID</label>
                <div class="col-sm-3">
                    <input class="form-control" type="text" id="shopadvisor_pid" name="shopadvisor_pid"
                           placeholder="00097855066237">
                </div>

            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="shopadvisor_submit"> </label>
                <div class="col-sm-1">
                    <button type="submit" id="shopadvisor_submit" class="btn btn-default"><i class="ajax_loading"></i>Import Products</button>
                </div>
                <div class="col-sm-2">
                <a id="shopadvisor-csv-file" class="btn btn-default"
                   href="http://shopfetti.com/wp-content/plugins/shopstyle-import/products.csv"
                   style="display: none; text-decoration: none;">Download products CSV </a>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <hr/>
        <div class="tablenav top hidden">

            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk
                    action</label><select name="action" id="bulk-action-selector-top">
                    <option value="-1">Bulk Actions</option>
                    <option value="import">Post to Woocommerce</option>
                </select>
                <button type="submit" id="doaction" class="button action" value="Apply"><i
                            class="ajax_loading1"></i><span class="button_title">Apply</span></button>
            </div>
            <div class="tablenav-pages"><span class="displaying-num">1,000 items</span>
                <span class="pagination-links">
                            <a class="first-page" href="#">
                                <span class="screen-reader-text">First page</span>
                                <span aria-hidden="true">«</span>
                            </a>
                            <a class="prev-page" href="#">
                                <span class="screen-reader-text">Previous page</span>
                                <span aria-hidden="true">‹</span>
                            </a>
                            <span class="first_sign tablenav-pages-navspan" aria-hidden="true">«</span>
                            <span class="prev_sign tablenav-pages-navspan" aria-hidden="true">‹</span>
                         <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="1"
                                   size="3">
                             <span class="tablenav-paging-text"> of <span class="total-pages">50</span></span>
                         </span>
                        <a class="next-page" href="#">
                            <span class="screen-reader-text">Next page </span>
                            <span aria-hidden="true">›</span>
                        </a>
                        <a class="last-page" href="#">
                            <span class="screen-reader-text">Last page</span>
                            <span aria-hidden="true">»</span>
                        </a>
                            <span class="next_sign tablenav-pages-navspan" aria-hidden="true">›</span>
                            <span class="last_sign tablenav-pages-navspan" aria-hidden="true">»</span>
                        </span>
            </div>
            <br class="clear">
        </div>

        <div id="result" class="row">

        </div>
    </form>
</div>

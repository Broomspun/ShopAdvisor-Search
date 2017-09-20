<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class ShopAdvisor_Search_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * key of ShopAdvisor Product Search API.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $apiKey;

    /**
     * key of ShopAdvisor Product Search API.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $totals;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->apiKey = 'zZBDgi_Zyl5_IMQNjuSn_OvL3XFS0S_4';
		$this->totals =0;

        $settings['apiKey'] = $this->apiKey;

        update_option('shopadvisor-search-settings', $settings);

	}
    /**
     * Create Admin menu for the admin area.
     *
     * @since    1.0.0
     */

	public function shopadvisor_search_admin_menu(){
        add_menu_page('ShopAdvisor Search Settings', 'ShopAdvisor Search API Setup', 'manage_options', 'shopadvisor_search_setting', array($this, 'shopadvisor_search_setting'));
    }

    /**
     * Get products from ShopAdvisor Search API.
     *
     * @since    1.0.0
     */
    public function getProductsFromShopAdvisor(){

        //Check nonce
        if (!isset($_POST['shopadvisor_import_nonce'])) {
            exit(1);
            return;
        }

        //Setting SESSION for saving products temporarily
        if (empty($_SESSION)) {
            session_start();
        } else {
            session_destroy();
            session_start();
        }

        $apikey = $_POST['shopadvisor_apikey'];
        $ul = $_POST['shopadvisor_ul'];

        $api_url = "https://api.shopadvisor.com/v3.0/products?";
        $page_number = intval($_POST['pg_number'])+1;

        $i=1;

        if($_POST['total_calculation']==1){
            do{
                $parameters = array(
                    "apikey"                    => $apikey,
                    "requestorid"               => 'c788a854-9d3c-11e7-87fc-1f63daee3ac0',
                    'userlocation'              => $ul,
                    'pageSize'                  => $_POST['shopadvisor_ppp'],
                    'page'                      => $page_number,
                    'maxPerRetailer'            => $_POST['maxPerRetailer'],
                    'maxLocationsPerRetailer'   => $_POST['maxLocationsPerRetailer'],

                );
                if(isset($_POST['shopadvisor_q']) && $_POST['shopadvisor_q']!=='')
                    $parameters['keywords'] = $_POST['shopadvisor_q'];

                if(isset($_POST['category']) && $_POST['category']!='-1')
                    $parameters['productCategory'] = $_POST['category'];

                if(isset($_POST['shopadvisor_brand']) && $_POST['shopadvisor_brand']!='')
                    $parameters['brand'] = $_POST['shopadvisor_brand'];

                if(isset($_POST['shopadvisor_model']) && $_POST['shopadvisor_model']!='')
                    $parameters['model'] = $_POST['shopadvisor_model'];

                if(isset($_POST['shopadvisor_name']) && $_POST['shopadvisor_name']!='')
                    $parameters['name'] = $_POST['shopadvisor_name'];

                if(isset($_POST['shopadvisor_pid']) && $_POST['shopadvisor_pid']!='')
                    $parameters['productId,'] = $_POST['shopadvisor_pid'];

                $getdata = http_build_query($parameters);

                $ch = curl_init();

                $url = $api_url . $getdata;

                curl_setopt($ch, CURLOPT_URL, $url);

                $options = array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_DNS_USE_GLOBAL_CACHE => 0
                );
                curl_setopt_array($ch, $options);
                $returnResult = curl_exec($ch);
                curl_close($ch);

                $all_data = json_decode($returnResult, true)['ShopAdvisorAPIResult'];

                $count = $all_data['count'];
                $this->totals  += $count;
                $page_number++;
                $i++;

            }while($count==$_POST['shopadvisor_ppp']);
        }

        $page_number = intval($_POST['pg_number'])+1;

        $parameters = array(
            "apikey"                    => $apikey,
            "requestorid"               => 'c788a854-9d3c-11e7-87fc-1f63daee3ac0',
            'userlocation'              => $ul,
            'pageSize'                  => $_POST['shopadvisor_ppp'],
            'page'                      => $page_number,
            'maxPerRetailer'            => $_POST['maxPerRetailer'],
            'maxLocationsPerRetailer'   => $_POST['maxLocationsPerRetailer'],

        );
        if(isset($_POST['shopadvisor_q']) && $_POST['shopadvisor_q']!=='')
            $parameters['keywords'] = $_POST['shopadvisor_q'];

        if(isset($_POST['category']) && $_POST['category']!='-1')
            $parameters['productCategory'] = $_POST['category'];

        if(isset($_POST['shopadvisor_brand']) && $_POST['shopadvisor_brand']!='')
            $parameters['brand'] = $_POST['shopadvisor_brand'];

        if(isset($_POST['shopadvisor_model']) && $_POST['shopadvisor_model']!='')
            $parameters['model'] = $_POST['shopadvisor_model'];

        if(isset($_POST['shopadvisor_name']) && $_POST['shopadvisor_name']!='')
            $parameters['name'] = $_POST['shopadvisor_name'];

        if(isset($_POST['shopadvisor_pid']) && $_POST['shopadvisor_pid']!='')
            $parameters['productId,'] = $_POST['shopadvisor_pid'];

        $getdata = http_build_query($parameters);

        $ch = curl_init();

        $url = $api_url . $getdata;

        curl_setopt($ch, CURLOPT_URL, $url);

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_DNS_USE_GLOBAL_CACHE => 0
        );
        curl_setopt_array($ch, $options);
        $returnResult = curl_exec($ch);
        curl_close($ch);

        $all_data = json_decode($returnResult, true)['ShopAdvisorAPIResult'];

        $data['totals'] = $this->totals;
        $pages = ceil($this->totals / $_POST['shopadvisor_ppp']);
        $data['pages'] = $pages;

        $all_results = $all_data['results'];
        if(isset($all_data['request']['productCategory']))
            $category = $all_data['request']['productCategory'];

        $items = array();

        foreach ($all_results as $result){
            $product = $result['SearchResult'];
            $id = $product['product']['id'];
            $items[] = $id;

            $data['product'][$id]['lastUpdated'] = $product['lastUpdated'];
            $data['product'][$id]['thumb_image'] = $product['product']['images'][0]['ImageInfo']['link'];
            $data['product'][$id]['large_image'] = $product['product']['images'][1]['ImageInfo']['link'];
            $data['product'][$id]['title'] = $product['product']['name'];
            $data['product'][$id]['descriptionLong'] = $product['product']['descriptionLong'];
            $data['product'][$id]['descriptionShort'] = $product['product']['descriptionShort'];
            $data['product'][$id]['url'] = $product['product']['url'];
            $data['product'][$id]['externalproductid'] = $product['product']['externalproductid'];
            $data['product'][$id]['sku'] = $product['product']['sku'];
            $data['product'][$id]['brand'] = $product['product']['brand'];
            $data['product'][$id]['manufacturerPartNumber'] = $product['product']['manufacturerPartNumber'];
            $data['product'][$id]['barcode'] = $product['product']['barcode'];
            $data['product'][$id]['productCategory'] = implode(',',$product['product']['productCategory']);
            $data['product'][$id]['productType'] = implode(',',$product['product']['productType']);
            $data['product'][$id]['msrpCurrency'] = $product['product']['msrpCurrency'];
            $data['product'][$id]['distance_from_use_location'] = $product['distance']['distance'];
            $data['product'][$id]['distance_unit'] = $product['distance']['units'];
            $data['product'][$id]['price'] = $product['price'];
            $data['product'][$id]['currency'] = $product['currency'];
            $data['product'][$id]['location_id'] = $product['location']['id'];
            $data['product'][$id]['address'] = array($product['location']['address']['country'],$product['location']['address']['city'],
                $product['location']['address']['address1'],$product['location']['address']['state'],$product['location']['address']['postal']);
            $data['product'][$id]['distance'] = $product['location']['distance']['distance'];
            $data['product'][$id]['hours'] = $product['location']['hours'];
            $data['product'][$id]['location_lat_long'] = implode(',', array($product['location']['location']['latitude'],
                                    $product['location']['location']['longitude'],));
            $data['product'][$id]['phone'] = $product['location']['phone'];
            $data['product'][$id]['retailer'] = array('id'=>$product['location']['retailer']['id'],
                'name'=>$product['location']['retailer']['name'],
                'logo'=>$product['location']['retailer']['logo'],
                'logosq'=>$product['location']['retailer']['logosq'],);
            $data['product'][$id]['timezone'] = $product['location']['timezone'];
            $data['product'][$id]['retLocationId'] = $product['location']['retLocationId'];
            $data['product'][$id]['quantityText'] = $product['quantityText'];
            $data['product'][$id]['locName'] = $product['locName'];
            $data['product'][$id]['availability_url'] = $product['availability'];

            if($data['product'][$id]['productCategory']==null)
                $data['product'][$id]['productCategory'] = $category;
        }

        //Write csv
        $csv_filename = plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . '../public/products.csv';
        $file = fopen($csv_filename, 'w');
        $header = $this->getHeader();
        // save the column headers
        fputcsv($file, $header);

        foreach ($items as $item) {
            $this->writeCSV($data['product'][$item], $file);
        }

        fclose($file);

        $_SESSION = $data;

        echo json_encode($data);
        wp_die();
    }
    /**
     * Display Seeting  for the admin area.
     *
     * @since    1.0.0
     */
    function shopadvisor_search_setting(){
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        require_once ('partials/shopadvisor-search-admin-display.php');
    }

    /**
     * Upload products into woocommerce store.
     *
     * @since    1.0.0
     */
    public function UploadProducts(){
        set_time_limit(0);
        session_start();

        $products = $_SESSION['product'];
        $items = $_POST['items'];

        $data = array();

        foreach ($items as $item) {
            $this->write_product($products[$item]);
            $data['input'] = $products[$item];
        }

        $data['message'] = 'Uploaded succesufully!';
        echo json_encode($data);
        wp_die();
    }

    private function write_product($product){
        $user_id = wp_get_current_user()->ID;
        $title = $product['title'];
        $post = array(
            'post_author' => $user_id,
            'post_content' => $product['descriptionLong'],
            'post_status' => "publish",
            'post_title' => $title,
            'post_parent' => '',
            'post_type' => "product",
            'post_excerpt' => $product['descriptionShort'],
        );

        // Write the product into the database
        $post_id = wp_insert_post($post, true);
        wp_set_object_terms($post_id, 'external', 'product_type');

        update_post_meta($post_id, '_visibility', 'visible');
        update_post_meta($post_id, '_stock_status', 'instock');
        update_post_meta($post_id, 'total_sales', '0');
        update_post_meta($post_id, '_downloadable', 'yes');
        update_post_meta($post_id, '_virtual', 'yes');
        update_post_meta($post_id, '_regular_price', $product['price']);

        update_post_meta($post_id, '_product_url', $product['url']);
        update_post_meta($post_id, '_sku', $product['sku']);

        if($product['sku']=='')
            update_post_meta($post_id, '_sku', $product['externalproductid']);

        update_post_meta($post_id, '_externalproductid', $product['externalproductid']);
        update_post_meta($post_id, '_brand', $product['descriptionLong']);
        update_post_meta($post_id, '_manufacturerPartNumber', $product['manufacturerPartNumber']);
        update_post_meta($post_id, '_barcode', $product['barcode']);
        update_post_meta($post_id, '_productType', $product['productType']);

        //Location info
        update_post_meta($post_id, '_location_id', $product['location_id']);
        update_post_meta($post_id, '_address', $product['address']);
        update_post_meta($post_id, '_distance_from_use_location', $product['distance_from_use_location']);
        update_post_meta($post_id, '_hours', $product['hours']);
        update_post_meta($post_id, '_location_lat_long', $product['location_lat_long']);
        update_post_meta($post_id, '_phone', $product['phone']);
        update_post_meta($post_id, '_timezone', $product['timezone']);
        update_post_meta($post_id, '_retLocationId', $product['retLocationId']);
        update_post_meta($post_id, '_quantityText', $product['quantityText']);
        update_post_meta($post_id, '_locName', $product['locName']);
        update_post_meta($post_id, '_availability', $product['availability']);

        //Retailer Info
        update_post_meta($post_id, '_retailer', $product['retailer']);

        //Create category and Assign
        if($product['productCategory']!=''){
            wp_set_object_terms($post_id, $product['productCategory'], 'product_cat');
        }

//===============  Upload Featured image ================
        //
        if (isset($product['large_image'])) {
            $image_url = $product['large_image'];
            $this->uploadImage($image_url, $user_id, $post_id, true);
        }
    }

    /**
     * Upload Feature image of a product.
     *
     * @since    1.0.0
     */
    private function uploadImage($image_url, $user_id, $post_id, $bFeatured = false){
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);

        $filename = basename($image_url);

        if (wp_mkdir_p($upload_dir['path']))
            $file = $upload_dir['path'] . '/' . $filename;
        else
            $file = $upload_dir['basedir'] . '/' . $filename;

        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);


        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => $filename,
            'post_content' => '',
            'post_status' => 'inherit',
            'post_author' => $user_id,
        );

        $attach_id = wp_insert_attachment($attachment, $file, $post_id);

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);
        return $attach_id;
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_register_style('font_awesome_css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('font_awesome_css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shopadvisor-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_script('jquery');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shopadvisor-search-admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script($this->plugin_name, 'ajax_parms', array('ajaxurl' => admin_url('admin-ajax.php')));

	}
    /**
     * Get header of csv file.
     *
     * @since    1.0.0
     */
	private function getHeader(){
        $header = array("sku","title","short_description","long_description","lastUpdated","thumbnail","large_image","price","external_url",
            "brand","externalproductid","location_id","manufacturerPartNumber", "barcode","Category","Product_Type","Currency","distance_from_use_location",
            "distance_unit","address", "hours","location_lat_long","phone","retailer","timezone", "retLocationId",
            "quantityText", "locName","availability_url"
        );
        return $header;
    }
    /**
     * Write csv file.
     *
     * @since    1.0.0
     */
    private function writeCSV($product,$fp){
        $items = array(
            "sku"                           => $product['sku'],
            "title"                         => $product['title'],
            "short_description"             => $product['descriptionShort'],
            "long_description"              => $product['descriptionLong'],
            "lastUpdated"                   => $product['lastUpdated'],
            "thumbnail"                     => $product['thumb_image'],
            "large_image"                   => $product['large_image'],
            "price"                         => $product['price'],"external_url",
            "brand"                         => $product['brand'],
            "externalproductid"             => $product['externalproductid'],
            "location_id"                   => $product['location_id'],
            "manufacturerPartNumber"        => $product['manufacturerPartNumber'],
            "barcode"                       => $product['barcode'],
            "Category"                      => $product['productCategory'],
            "Product_Type"                  => $product['productType'],
            "Currency"                      => $product['msrpCurrency'] ,
            "distance_from_use_location"    => $product['distance_from_use_location'],
            "distance_unit"                 => $product['distance_unit'],
            "address"                       => implode('|', $product['address']),
            "hours"                         => $product['hours'],
            "location_lat_long"             => $product['location_lat_long'],
            "phone"                         => $product['phone'],
            "retailer"                      => implode('|', $product['retailer']),
            "timezone"                      => $product['timezone'],
            "retLocationId"                 => $product['retLocationId'],
            "quantityText"                  => $product['quantityText'],
            "locName"                       => $product['locName'],
            "availability_url"              => $product['availability_url']
        );

        fputcsv($fp, $items);
    }
}

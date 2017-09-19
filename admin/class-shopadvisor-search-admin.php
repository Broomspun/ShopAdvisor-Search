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

        $parameters = array(
            "apikey" => $apikey,
            "requestorid" => 'c788a854-9d3c-11e7-87fc-1f63daee3ac0',
            'userlocation' => $ul,
        );
        if(isset($_POST['shopadvisor_q']) && $_POST['shopadvisor_q']!=='')
            $parameters['keywords'] = $_POST['shopadvisor_q'];

        if(isset($_POST['category']) && $_POST['category']!='-1')
            $parameters['productCategory'] = $_POST['category'];

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

        $data['count'] = $all_data['count'];
        $data['url'] = $url;

        $all_results = $all_data['results'];
        if(isset($all_data['request']['productCategory']))
            $category = $all_data['request']['productCategory'];

        $items = array();

        $data['raw'] = $all_results;

        foreach ($all_results as $result){
            $product = $result['SearchResult'];
            $id = $product['product']['id'];
            $items[] = $id;

            $data[$id]['lastUpdated'] = $product['lastUpdated'];
            $data[$id]['thumb_image'] = $product['product']['images'][0]['ImageInfo']['link'];
            $data[$id]['large_image'] = $product['product']['images'][1]['ImageInfo']['link'];
            $data[$id]['title'] = $product['product']['name'];
            $data[$id]['descriptionLong'] = $product['product']['descriptionLong'];
            $data[$id]['descriptionShort'] = $product['product']['descriptionShort'];
            $data[$id]['url'] = $product['product']['url'];
            $data[$id]['externalproductid'] = $product['product']['externalproductid'];
            $data[$id]['sku'] = $product['product']['sku'];
            $data[$id]['brand'] = $product['product']['brand'];
            $data[$id]['manufacturerPartNumber'] = $product['product']['manufacturerPartNumber'];
            $data[$id]['barcode'] = $product['product']['barcode'];
            $data[$id]['productCategory'] = implode(',',$product['product']['productCategory']);
            $data[$id]['productType'] = implode(',',$product['product']['productType']);
            $data[$id]['msrpCurrency'] = $product['product']['msrpCurrency'];
            $data[$id]['distance'] = $product['distance']['distance'];
            $data[$id]['distance_unit'] = $product['distance']['units'];
            $data[$id]['price'] = $product['price'];
            $data[$id]['currency'] = $product['currency'];
            $data[$id]['location_id'] = $product['location']['id'];
            $data[$id]['address'] = array($product['location']['address']['country'],$product['location']['address']['city'],
                $product['location']['address']['address1'],$product['location']['address']['state'],$product['location']['address']['postal']);
            $data[$id]['distance'] = $product['location']['distance']['distance'];
            $data[$id]['hours'] = $product['location']['hours'];
            $data[$id]['location_lat_long'] = implode(',', array($product['location']['location']['latitude'],
                                    $product['location']['location']['longitude'],));
            $data[$id]['phone'] = $product['location']['phone'];
            $data[$id]['retailer'] = array('id'=>$product['location']['retailer']['id'],
                'name'=>$product['location']['retailer']['name'],
                'logo'=>$product['location']['retailer']['logo'],
                'logosq'=>$product['location']['retailer']['logosq'],);
            $data[$id]['timezone'] = $product['location']['timezone'];
            $data[$id]['retLocationId'] = $product['location']['retLocationId'];
            $data[$id]['locName'] = $product['locName'];
            $data[$id]['locName'] = $product['locName'];
            $data[$id]['availability_url'] = $product['availability'];

            if($data[$id]['productCategory']==null)
                $data[$id]['productCategory'] = $category;
        }

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

}

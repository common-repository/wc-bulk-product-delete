<?php
/**
 * Plugin Name:       Bulk Product Delete For WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/wc-bulk-product-delete/
 * Description:       Bulk Product Delete For WooCommerce
 * Version:           0.3
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       wc-bulk-product-delete
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: @TODO
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
class WooCommerce_Bulk_Product_Delete {
	/**
	 * @var string
	 */
	public $version = '0.3';

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;
    protected static $_is_ajax_running = null;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() { 
        $this->define_constant();
        $this->load_required_files();
        $this->init_class();

		add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
        add_action( 'wp_ajax_wc_bpd_delete', array($this,'check_delete'));
        add_action( 'wp_ajax_wc_bpd_status', array($this,'check_status'));
    } 
    
    public function check_status(){
        $status = get_option('wc_bpd_running'); 
        $delete_status = get_option('wc_bpd_values');
        
        $delete_total = get_option('wc_bpd_total_values');
        $delete_status['total'] = $delete_total;
        
        if($status){ 
            $delete_status['status'] = 'running';
            die(json_encode($delete_status));
        }else{
            $delete_status['status'] = 'stop';
            $v = json_encode($delete_status);
            delete_option('wc_bpd_values');
            die($v);
        } 
    }
    
    public function check_delete(){
        set_time_limit(0);
        update_option('wc_bpd_values',array('success' =>0,'error' => 0,'error_ids' => array()));
        update_option('wc_bpd_running',true);
        
        $this->load_files(WC_BPD_PATH.'class-admin-ajax.php');
        $ins = new WooCommerce_Bulk_Product_Delete_Admin_Ajax;
        $ins->check_delete();
        delete_option('wc_bpd_running');
        
        $status = get_option('wc_bpd_running'); 
        $delete_status = get_option('wc_bpd_values');
        $delete_total = get_option('wc_bpd_total_values');
        $delete_status['total'] = $delete_total;
        
        die(json_encode($delete_status));
    }
    
    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){ 
       if($this->is_request('admin')){
           $this->load_files(WC_BPD_PATH.'class-*.php');
       } 
    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){ 
        if($this->is_request('admin')){
            $this->admin = new WooCommerce_Bulk_Product_Delete_Admin;
        }
    }

    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){

            if($type == 'require'){
                require_once( $files );
            } else if($type == 'include'){
                include_once( $files );
            }
            
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(WC_BPD_TEXT_DOMAIN, false, WC_BPD_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (WC_BPD_TEXT_DOMAIN === $domain)
            return WC_BPD_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
        
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('WC_BPD_NAME','Bulk Product Delete For WooCommerce'); # Plugin Name
        $this->define('WC_BPD_SLUG','wc-bpd'); # Plugin Slug
        $this->define('WC_BPD_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
        $this->define('WC_BPD_ADMIN',WC_BPD_PATH.'admin/'); # Plugin DIR
        $this->define('WC_BPD_LANGUAGE_PATH',WC_BPD_PATH.'languages');
        $this->define('WC_BPD_TEXT_DOMAIN','wc-bulk-product-delete'); #plugin lang Domain
        $this->define('WC_BPD_URL',plugins_url('', __FILE__ )); 
        $this->define('WC_BPD_FILE',plugin_basename( __FILE__ )); 
    }
    
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
    
    /**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}

if(is_admin()){
    new WooCommerce_Bulk_Product_Delete;
}
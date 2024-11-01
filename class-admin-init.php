<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/woocommerce-role-based-price/
 *
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class WooCommerce_Bulk_Product_Delete_Admin extends WooCommerce_Bulk_Product_Delete {

    /**
	 * Initialize the class and set its properties.
	 * @since      0.1
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array($this,'register_my_custom_menu_page' ));
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
        add_filter( 'woocommerce_get_settings_pages',  array($this,'settings_page') ); 
        

	}
 
 
    public function register_my_custom_menu_page() {
        add_submenu_page('edit.php?post_type=product',
                         __('Bulk Product Delete For WooCommerce',WC_BPD_LANGUAGE_PATH),
                         __('Bulk Product Delete',WC_BPD_LANGUAGE_PATH)
                         , 'manage_woocommerce','wc_bpd', array($this,'admin_page') );
    }
    
    /**
     * [[Description]]
     */
    public function admin_page(){       
        $page_name = 'admin_page.php';
        require(WC_BPD_PATH.'views/'.$page_name);
    }
    
 
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() { 
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_style(WC_BPD_SLUG.'_core_style',WC_BPD_URL.'/css/style.css' , array(), $this->version, 'all' );  
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_script(WC_BPD_SLUG.'_core_script', WC_BPD_URL.'/js/script.js', array('jquery'), $this->version, false ); 
        }
 
	}
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
        $screen_ids[] = 'product_page_wc_bpd'; 
        return $screen_ids;
    }
    
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WC_BPD_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', admin_url('edit.php?post_type=product&page=wc_bpd'), __('Settings',WC_BPD_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', '#', __('F.A.Q',WC_BPD_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/WooCommerce-Bulk-Product-Delete', 'View On Github',WC_BPD_TEXT_DOMAIN );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/WooCommerce-Bulk-Product-Delete/issues/', __('Report Issue',WC_BPD_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J5FCNXN4GPXRS', __('Donate',WC_BPD_TEXT_DOMAIN) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WC_BPD_TEXT_DOMAIN) );
		}
		return $plugin_meta;
	}	    
}

?>
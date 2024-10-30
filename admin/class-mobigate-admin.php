<?php
/**
 * Mobigate.
 *
 * @package   Mobigate
 * @author    Don Nguyen <don.nguyen@hazuu.com>
 * @license   GPL-2.0+
 * @link      http://hazuu.com
 * @copyright 2014 Don Nguyen
 */

/**
 *
 * @package Mobigate
 * @author  Don Nguyen <don.nguyen@hazuu.com>
 */
class Mobigate_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Mobigate" to the name of your initial plugin class
		 *
		 */
		$plugin = Mobigate::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_head', array( $this, 'admin_header' ) );

		add_action( 'delete_post', array( $this, 'removeAddedGame' ), 10 );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Mobigate" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Mobigate::VERSION );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Mobigate" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Mobigate::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Mobigate Setting', $this->plugin_slug ),
			__( 'Mobigate', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_mobigate_option_page' )
		);

		add_submenu_page($this->plugin_slug, 
			'List of games', 
			'Games', 
			'manage_options', 
			'list_of_game', 
			array( $this, 'display_list_game_page' )	
		);

		add_submenu_page(null, 
			'Select categry', 
			'Select categry', 
			'manage_options', 
			'select_category_for_game', 
			array( $this, 'display_select_category_page' )	
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_mobigate_option_page() {
		if (isset($_POST["update_settings"])) {
	    	$api_key = esc_attr($_POST["api_key"]);   
	    	update_option("api_key", $api_key);
	    	$message = "Setting updated!";

	    	//update option to force get games
	    	update_option("force_update", true);
		} 

		include_once( 'views/admin.php' );
	}

	/**
	 * Render the listing game page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_select_category_page() {
		include_once( 'views/category.php' );
	}

	/**
	 * Render the listing game page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_list_game_page() {
		$api = new APIConnector;
		$api->setAPIKey(get_option('api_key'));
		$games = $api->getAllGames();

		$game_list_table = new Game_List_Table();
		$game_list_table->prepare_items($games); 

		if (isset($_GET["action"])) {
			switch ($_GET["action"]) {
				case 'updategame':
					$requestId = $_GET["requestId"];
					$this->updateGame($api, $requestId);
					wp_safe_redirect( admin_url( "admin.php?page=list_of_game" ) );
					break;

				default:
					break;
			}
		}

		if (isset($_POST["action"])) {
			switch ($_POST["action"]) {
				case 'publish_or_update':
					$requestIds = $_POST["game"];
					$params = array('page' => 'select_category_for_game',
							'requestId' => $requestIds,
							'action' => 'bulk_action'
						);
					$ready_parames = http_build_query($params);
					//$this->processBulkAction($api, $requestIds);
					wp_safe_redirect( admin_url( "admin.php?".$ready_parames ) );
					break;

				case 'addgame':
					$requestId = $_POST["requestId"];
					$categories = $_POST["post_category"];
					$this->addNewGame($api, $requestId, $categories);
					wp_safe_redirect( admin_url( "admin.php?page=list_of_game" ) );
					break;

				case 'bulk_game':
					$requestIds = $_POST["requestId"];
					$categories = $_POST["post_category"];
					$this->processBulkAction($api, $requestIds, $categories);
					wp_safe_redirect( admin_url( "admin.php?page=list_of_game" ) );
					break;

				default:
					break;
			}
		}

		include_once( 'views/games.php' );
	}

	public function processBulkAction($api, $requestIds, $categories){
		$publish = 0;
		$update = 0;

		foreach ($requestIds as $requestId) {
			if(Mobigate_Helper::isAddedGame($requestId)){
				$this->updateGame($api, $requestId, true);
				$update++;
			}else{
				$this->addNewGame($api, $requestId, $categories, true);
				$publish++;
			}
		}
		queue_flash_message( "Đã thêm mới ".$publish." game và cập nhật ".$update." game", $class = 'updated' );
	}

	public function addNewGame($api, $requestId, $categories = array(), $bulk = false ){
		$game_object = $api->getGameDetails($requestId);

		if($game_object){
			$content = "<div class='mobigate_game'>";
			$content .= "<div class='game_info'>";
			$content .= "<div class='game_thumb'><img width='60' height='60' src='".$game_object->avatar."'></div>";
			$content .= "<div class='platform'>Tải game ".$game_object->name."</div>";
			//$content .= "<div class='download-sms'><span>Tải SMS</span> ".$game_object->downloadSMS->body." gửi ".$game_object->downloadSMS->serviceNumber."</div>";
			$content .= "<div><a href='". $game_object->downloadUrl."' class='download mobigate-download' target='_blank'>DOWNLOAD</a></div>";
			$content .= "</div><br>";
			$content .= "<div class='description'>";
			$content .= $game_object->description;
			$content .= "</div>";
			$content .= "</div>";
			// Create post object
			$game_post = array(
			  'post_title'    => wp_strip_all_tags( $game_object->name ),
			  'post_content'  => $content,
			  'post_status'   => 'publish',
			  'post_author'   => wp_get_current_user()->ID,
			  'tags_input' => $game_object->supportedOs,
			  'post_category' => $categories,
			);

			// Insert the post into the database
			$post_id = wp_insert_post( $game_post );

			if($post_id){
				if(!$bulk){
					queue_flash_message( "Đã thêm game mới: ".$game_object->name, $class = 'updated' );
				}
				
				//Don't forget to update added_game option
				Mobigate_Helper::addNewGameToOption($requestId, $post_id);

				$thumbid = $this->getImageRemote($game_object->avatar);

				if($thumbid){
					set_post_thumbnail( $post_id , $thumbid );
				}
			}
		}else{
			if(!$bulk){
				queue_flash_message( "Có lỗi, hiện tại không thể thêm game, vui lòng thử lại sau!", $class = 'error' );
			}
		}
		
	}

	public function updateGame($api, $requestId, $bulk = false){
		$game_object = $api->getGameDetails($requestId);
		
		if($game_object){
			$content = "<div class='mobigate_game'>";
			$content .= "<div class='game_info'>";
			$content .= "<div class='game_thumb'><img width='60' height='60' src='".$game_object->avatar."'></div>";
			$content .= "<div class='platform'>Tải game ".$game_object->name."</div>";
			//$content .= "<div class='download-sms'><span>Tải SMS</span> ".$game_object->downloadSMS->body." gửi ".$game_object->downloadSMS->serviceNumber."</div>";
			$content .= "<div><a href='". $game_object->downloadUrl."' class='download mobigate-download' target='_blank'>DOWNLOAD</a></div>";
			$content .= "</div><br>";
			$content .= "<div class='description'>";
			$content .= $game_object->description;
			$content .= "</div>";
			$content .= "</div>";

			$post_id = Mobigate_Helper::getPostIdFromRequestId($requestId);
			// Create post object
			$game_post = array(
			  'ID'           => $post_id,
			  'post_title'    => wp_strip_all_tags( $game_object->name ),
			  'post_content'  => $content,
			  'post_status'   => 'publish',
			  'post_author'   => wp_get_current_user()->ID,
			  'tags_input' => $game_object->supportedOs,
			);

			// Insert the post into the database
			wp_update_post( $game_post );

			if($post_id){
				if(!$bulk){
					queue_flash_message( "Đã cập nhật thông tin game: ".$game_object->name, $class = 'updated' );
				}
				
				$thumbid = $this->getImageRemote($game_object->avatar);

				if($thumbid){
					set_post_thumbnail( $post_id , $thumbid );
				}
			}
		}else{
			if(!$bulk){
				queue_flash_message( "Có lỗi, hiện tại không thể cập nhật game, vui lòng thử lại sau!", $class = 'error' );
			}
		}
		
	}

	private function getImageRemote($thumb_url){
		if ( ! empty($thumb_url) ) {
            // Download file to temp location
            $tmp = download_url( $thumb_url );

            // Set variables for storage
            // fix file filename for query strings
            preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);
            $file_array['name'] = basename($matches[0]);
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if ( is_wp_error( $tmp ) ) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';
            }

            // do the validation and storage stuff
            $thumbid = media_handle_sideload( $file_array, $post->ID, $desc );
            // If error storing permanently, unlink
            if ( is_wp_error($thumbid) ) {
                @unlink($file_array['tmp_name']);
                
            }
            return $thumbid;
        }
        return "";
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function admin_header() {
		echo '<style type="text/css">';
	    echo '.wp-list-table.games .column-icon { width: 50px; }';
	    echo '.wp-list-table.games .column-rate { width: 80px; }';
	    echo '.wp-list-table.games .column-download_count { width: 120px; }';
	    echo '.wp-list-table.games .column-desc { width: 40%;}';
	    echo '.wp-list-table.games .column-actions { width: 100px;}';
	    echo '</style>';
	}

	function removeAddedGame( $post_id ) {
	    Mobigate_Helper::removeAddedGame($post_id);
	}

}

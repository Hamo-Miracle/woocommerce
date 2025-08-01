<?php

class ADBC_Clean_Transient extends WP_List_Table {

	private $aDBc_message = "";
	private $aDBc_class_message = "updated";
	private $aDBc_elements_to_display = array();
	private $aDBc_type_to_clean = "";
	private $aDBc_plural_title = "";
	private $aDBc_sql_get_transients = "";
	private $aDBc_custom_sql_args = "";
	private $aDBc_search_sql_arg = "";
	private $aDBc_order_by_sql_arg = "";
	private $aDBc_limit_offset_sql_arg = "";
    /**
     * Constructor
     */
    function __construct($element_type){

		if($element_type == "expired-transients"){

			$this->aDBc_type_to_clean 	= "expired-transients";
			$aDBc_singular 				= __('Expired transient', 'advanced-database-cleaner');
			$this->aDBc_plural_title 	= __('Expired transients', 'advanced-database-cleaner');
			$this->aDBc_custom_sql_args = " AND b.option_value < UNIX_TIMESTAMP()";

		}

		// Prepare additional sql args if any: per page, LIMIT, OFFSET, etc.

		if ( ADBC_PLUGIN_PLAN == "pro" ) {

			$this->aDBc_search_sql_arg 			= aDBc_get_search_sql_arg( "a.option_name", "a.option_value" );

		}

		$this->aDBc_order_by_sql_arg 			= aDBc_get_order_by_sql_arg( "a.option_id" );
		$this->aDBc_limit_offset_sql_arg 		= aDBc_get_limit_offset_sql_args();

        parent::__construct(array(
            'singular'  => $aDBc_singular,
            'plural'    => $this->aDBc_plural_title,
            'ajax'      => false
		));

		$this->aDBc_prepare_elements_to_clean();
		$this->aDBc_print_page_content();
    }

	/** Prepare elements to display */
	function aDBc_prepare_elements_to_clean(){

		global $wpdb;

		// Process bulk action if any before preparing elements to clean
		$this->process_bulk_action();

		// Get all elements to clean
		if(function_exists('is_multisite') && is_multisite()){
			$blogs_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blogs_ids as $blog_id){

				switch_to_blog($blog_id);

				$this->aDBc_fill_array_elements_to_clean($blog_id);

				restore_current_blog();
			}
		}else{

			$this->aDBc_fill_array_elements_to_clean("1");
		}
		// Call WP prepare_items function
		$this->prepare_items();
	}

	/** Fill array elements to display */
	function aDBc_fill_array_elements_to_clean($blog_id){

		global $wpdb;

		// Get all dashboard transients
		$this->aDBc_sql_get_transients = "SELECT a.option_id, a.option_name, a.option_value as option_content, a.autoload, b.option_value as option_timeout FROM $wpdb->options a LEFT JOIN $wpdb->options b ON b.option_name =
		CONCAT(
			CASE WHEN a.option_name LIKE '\_site\_transient\_%'
				THEN '_site_transient_timeout_'
				ELSE '_transient_timeout_'
			END
			,
			SUBSTRING(a.option_name, CHAR_LENGTH(
				CASE WHEN a.option_name LIKE '\_site\_transient\_%'
				   THEN '_site_transient_'
				   ELSE '_transient_'
				END
			) + 1)
		)
		WHERE (a.option_name LIKE '\_transient\_%' OR a.option_name LIKE '\_site\_transient\_%') AND a.option_name NOT LIKE '%\_transient\_timeout\_%'"
		. $this->aDBc_custom_sql_args
		. $this->aDBc_search_sql_arg
		. $this->aDBc_order_by_sql_arg
		//. $this->aDBc_limit_offset_sql_arg
		;

		$time_now = time();

		$aDBc_all_transient_feed = $wpdb->get_results($this->aDBc_sql_get_transients);

		foreach($aDBc_all_transient_feed as $aDBc_transient){

			// Get timeout of transient
			switch ( $this->aDBc_type_to_clean ) {

				case "expired-transients" :
					$transient_timeout = __( 'Expired', 'advanced-database-cleaner' );
					break;

			}

			// Get transient content
			$transient_content = maybe_unserialize($aDBc_transient->option_content);
			if(is_array($transient_content)){
				$transient_content = "<i>Array</i>";
			}elseif(gettype($transient_content) == 'object'){
				$transient_content = "<i>Object</i>";
			}else{
				$transient_content = aDBc_create_tooltip_for_long_string($aDBc_transient->option_content, 35);
			}

			// Susbst transient name
			$transient_name = aDBc_create_tooltip_for_long_string($aDBc_transient->option_name, 35);

			array_push($this->aDBc_elements_to_display, array(
				'transient_id' 			=> $aDBc_transient->option_id,
				'transient_name' 		=> $transient_name,
				'transient_content' 	=> $transient_content,
				'transient_timeout'		=> $transient_timeout,
				'transient_autoload'	=> $aDBc_transient->autoload,
				'site_id'				=> $blog_id
				)
			);
		}
	}

	/** WP: Get columns */
	function get_columns(){
		$columns = array(
			'cb'       				=> '<input type="checkbox" />',
			'transient_id' 			=> __('ID','advanced-database-cleaner'),
			'transient_name' 		=> __('Transient name','advanced-database-cleaner'),
			'transient_content' 	=> __('Value','advanced-database-cleaner'),
			'transient_timeout'		=> __('Expires In','advanced-database-cleaner'),
			'transient_autoload'   	=> __('Autoload','advanced-database-cleaner'),
			'site_id'   			=> __('Site id','advanced-database-cleaner')
		);
		return $columns;
	}

	/** WP: Column default */
	function column_default($item, $column_name){
		switch($column_name){
			case 'transient_id':
			case 'transient_name':
			case 'transient_content':
			case 'transient_timeout':
			case 'transient_autoload':
			case 'site_id':
				return $item[$column_name];
			default:
			  return print_r($item, true) ; //Show the whole array for troubleshooting purposes
		}
	}

	/** WP: Get columns that should be hidden */
    function get_hidden_columns(){
		// If MU, nothing to hide, else hide Side ID column
		if(function_exists('is_multisite') && is_multisite()){
			return array();
		}else{
			return array('site_id');
		}
    }

	function get_sortable_columns() {

		  $sortable_columns = array(
			'transient_id' 			=> array('a.option_id', false), //true means it's already sorted
			'transient_name' 		=> array('a.option_name', false)
		  );
		// Since order_by works directly with sql request, we will not order_by in mutlisite since it will not work
		if(function_exists('is_multisite') && is_multisite()){
			return array();
		}else{
			return $sortable_columns;
		}
	}

	/** WP: Prepare items to display */
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page = 50;
		if(!empty($_GET['per_page'])){
			$per_page = absint($_GET['per_page']);
		}

		$current_page = $this->get_pagenum();
		// Prepare sequence of elements to display
		$display_data = array_slice($this->aDBc_elements_to_display,(($current_page-1) * $per_page), $per_page);
		$this->set_pagination_args( array(
			'total_items' => count($this->aDBc_elements_to_display),
			'per_page'    => $per_page
		));
		$this->items = $display_data;
	}

	/** WP: Column cb for check box */
	function column_cb($item) {
		return sprintf('<input type="checkbox" name="aDBc_elements_to_process[]" value="%s" />', $item['site_id']."|".$item['transient_id']);
	}

	/** WP: Get bulk actions */
	function get_bulk_actions() {
		$actions = array(
			'clean'    => __('Clean','advanced-database-cleaner')
		);
		return $actions;
	}

	/** WP: Message to display when no items found */
	function no_items() {
		_e('No elements found!','advanced-database-cleaner');
	}

	/** WP: Process bulk actions */
    public function process_bulk_action() {

		// Detect when a bulk action is being triggered.
		$action = $this->current_action();

		if ( ! $action )
			return;

		// security check!
		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		// Check role
		if ( ! current_user_can( 'administrator' ) )
			wp_die( 'Security check failed!' );		

        if ( $action == 'clean' ) {
			// If the user wants to clean the elements he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				if(function_exists('is_multisite') && is_multisite()){
					// Prepare feeds to delete
					$feeds_to_delete = array();
					foreach($_POST['aDBc_elements_to_process'] as $aDBc_feed){
						$feed_info 			= explode("|", $aDBc_feed);
						$sanitized_site_id 	= sanitize_html_class($feed_info[0]);
						$sanitized_item_id 	= sanitize_html_class($feed_info[1]);
						// For security, we only proceed if both parts are clean and are numbers
						if(is_numeric($sanitized_site_id) && is_numeric($sanitized_item_id)){
							if(empty($feeds_to_delete[$sanitized_site_id])){
								$feeds_to_delete[$sanitized_site_id] = array();
							}
							array_push($feeds_to_delete[$sanitized_site_id], $sanitized_item_id);
						}
					}
					// Delete feeds
					foreach($feeds_to_delete as $site_id => $feed_ids){
						switch_to_blog($site_id);
						global $wpdb;
						$transients_to_delete = $wpdb->get_results("select option_name, option_id from $wpdb->options WHERE option_id IN (" . implode(',',$feed_ids) . ")");
						foreach($transients_to_delete as $transient){
							$site_wide = (strpos($transient->option_name, '_site_transient') !== false);
							$name = str_replace($site_wide ? '_site_transient_' : '_transient_', '', $transient->option_name);
							if(false !== $site_wide){
								// We used a query directly here instead of delete_site_transient() because in MU, WP tries to delete the transient from sitemeta table, however, in our case, all results above are from options table. So, we need to delete them from options table
								$name_timeout = '_site_transient_timeout_' . $name;
								$wpdb->query("DELETE FROM $wpdb->options WHERE option_id = {$transient->option_id} OR option_name = '{$name_timeout}'");
							}else{
								delete_transient($name);
							}
						}
						restore_current_blog();
					}
				}else{
					global $wpdb;
					$ids_to_delete = array();
					foreach($_POST['aDBc_elements_to_process'] as $aDBc_feed){
						$feed_info 		= explode("|", $aDBc_feed);
						$sanitized_id 	= sanitize_html_class($feed_info[1]);
						if(is_numeric($sanitized_id)){
							array_push($ids_to_delete, $sanitized_id);
						}
					}

					$names_to_delete = $wpdb->get_col("select option_name from $wpdb->options WHERE option_id IN (" . implode(',',$ids_to_delete) . ")");
					foreach($names_to_delete as $transient_name){
						$site_wide = (strpos($transient_name, '_site_transient') !== false);
						$name = str_replace($site_wide ? '_site_transient_' : '_transient_', '', $transient_name);
						if(false !== $site_wide){
							// Here, we used delete_site_transient() instead of a query because on a single site, this function will delete the transient from options table
							delete_site_transient($name);
						}else{
							delete_transient($name);
						}
					}
				}
				// Update the message to show to the user
				$this->aDBc_message = __("Selected 'Transients' successfully cleaned!", "advanced-database-cleaner");
			}
        }
    }

	/** Print the page content */
	function aDBc_print_page_content(){

		include_once 'page_custom_clean.php';
	}
}
?>
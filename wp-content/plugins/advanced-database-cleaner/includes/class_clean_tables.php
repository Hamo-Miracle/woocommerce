<?php

class ADBC_Tables_List extends WP_List_Table {

	/** Holds the message to be displayed if any */
	private $aDBc_message = "";

	/** Holds the class for the message : updated or error. Default is updated */
	private $aDBc_class_message = "updated";

	/** Holds tables that will be displayed */
	private $aDBc_tables_to_display = array();

	/** Holds counts + info of tables categories */
	private $aDBc_tables_categories_info	= array();

	/** Should we display "run search" or "continue search" button (after a timeout failed). Default is "run search" */
	private $aDBc_which_button_to_show = "new_search";

	private $aDBc_total_tables_to_optimize = 0;
	private $aDBc_total_lost = 0;
	private $aDBc_tables_name_to_optimize = array();

	private $aDBc_total_tables_to_repair = 0;
	private $aDBc_tables_name_to_repair = array();

	// This array contains belongs_to info about plugins and themes
	private $array_belongs_to_counts = array();

	// Holds msg that will be shown if folder adbc_uploads cannot be created by the plugin (This is verified after clicking on scan button)
	private $aDBc_permission_adbc_folder_msg = "";

    function __construct(){

        parent::__construct(array(
            'singular'  => __('Table', 'advanced-database-cleaner'),
            'plural'    => __('Tables', 'advanced-database-cleaner'),
            'ajax'      => false
		));

		$this->aDBc_prepare_and_count_tables();
		$this->aDBc_print_page_content();
    }

	/** Prepare items */
	function aDBc_prepare_and_count_tables() {

		if ( ADBC_PLUGIN_PLAN == "pro" ) {

			// Verify if the adbc_uploads cannot be created
			$adbc_folder_permission = get_option( "aDBc_permission_adbc_folder_needed" );

			if ( ! empty( $adbc_folder_permission ) ) {

				$this->aDBc_permission_adbc_folder_msg = sprintf( __( 'The plugin needs to create the following directory "%1$s" to save the scan results but this was not possible automatically. Please create that directory manually and set correct permissions so it can be writable by the plugin.','advanced-database-cleaner' ), ADBC_UPLOAD_DIR_PATH_TO_ADBC );

				// Once we display the msg, we delete that option from DB
				delete_option( "aDBc_permission_adbc_folder_needed" );

			}

		}

		// Test if user wants to delete a scheduled task
		if(isset($_POST['aDBc_delete_schedule'])){

			//Quick nonce security check!
			if(!check_admin_referer('delete_optimize_schedule_nonce', 'delete_optimize_schedule_nonce'))
				return; //get out if we didn't click the delete link

			// We delete the schedule
			$aDBc_sanitized_schedule_name = sanitize_html_class($_POST['aDBc_delete_schedule']);
			wp_clear_scheduled_hook('aDBc_optimize_scheduler', array($aDBc_sanitized_schedule_name));

			// We delete the item from database
			$aDBc_schedules = get_option('aDBc_optimize_schedule');
			unset($aDBc_schedules[$aDBc_sanitized_schedule_name]);
			update_option('aDBc_optimize_schedule', $aDBc_schedules, "no");

			$this->aDBc_message = __('The clean-up schedule deleted successfully!', 'advanced-database-cleaner');
		}

		// Verify if the user wants to edit the categorization of a table. This block test comes from edit_item_categorization.php
		if ( ADBC_PLUGIN_PLAN == "pro" ) {

			if ( isset( $_POST['aDBc_cancel'] ) ) {

				// If the user cancels the edit, remove the temp file
				if ( file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tables_manually_correction_temp.txt" ) )
					unlink( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tables_manually_correction_temp.txt" );

			} else if ( isset( $_POST['aDBc_correct'] ) ) {

				// Get the new belongs to of items
				$new_belongs_to = $_POST['new_belongs_to'];

				// Get value of checkbox to see if user wants to send correction to the server
				if ( isset( $_POST['aDBc_send_correction_to_server'] ) ) {
					$this->aDBc_message = aDBc_edit_categorization_of_items( "tables", $new_belongs_to, 1 );
				} else {
					$this->aDBc_message = aDBc_edit_categorization_of_items( "tables", $new_belongs_to, 0 );
				}

				// Remove the temp file
				if ( file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tables_manually_correction_temp.txt" ) )
					unlink( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tables_manually_correction_temp.txt" );
			}
		}

		// Process bulk action if any before preparing tables to display
		$this->process_bulk_action();

		// Get the names of all tables that should be optimized and count them to print it in the right side of the page
		global $wpdb;
		$aDBc_tables_to_optimize = $wpdb->get_results("SELECT table_name, data_free FROM information_schema.tables WHERE table_schema = '" . DB_NAME ."' and Engine <> 'InnoDB' and data_free > 0");
		$this->aDBc_total_tables_to_optimize = count($aDBc_tables_to_optimize);
		foreach($aDBc_tables_to_optimize as $table){

			// Get table name
			$table_name = "";
			// This test to prevent issues in MySQL 8 where tables are not shown
			// MySQL 5 uses $table->table_name while MySQL 8 uses $table->TABLE_NAME
			if(property_exists($table, "table_name")){
				$table_name = $table->table_name;
			}else if(property_exists($table, "TABLE_NAME")){
				$table_name = $table->TABLE_NAME;
			}

			array_push($this->aDBc_tables_name_to_optimize, $table_name);

			if(property_exists($table, "data_free")){
				$this->aDBc_total_lost += $table->data_free;
			}
		}

		// Get the names of all tables that should be repaired and count them to print it in the right side of the page
		$aDBc_tables_maybe_repair = $wpdb->get_results("SELECT table_name FROM information_schema.tables WHERE table_schema = '" . DB_NAME ."' and Engine IN ('CSV', 'MyISAM', 'ARCHIVE')");
		foreach($aDBc_tables_maybe_repair as $table){

			// Get table name
			$table_name = "";
			// This test to prevent issues in MySQL 8 where tables are not shown
			// MySQL 5 uses $table->table_name while MySQL 8 uses $table->TABLE_NAME
			if(property_exists($table, "table_name")){
				$table_name = $table->table_name;
			}else if(property_exists($table, "TABLE_NAME")){
				$table_name = $table->TABLE_NAME;
			}

			$query_result = $wpdb->get_results("CHECK TABLE `" . $table_name. "`");
			foreach($query_result as $row){
				if($row->Msg_type == 'error'){
					if(preg_match('/corrupt/i', $row->Msg_text)){
						array_push($this->aDBc_tables_name_to_repair, $table_name);
					}
				}
			}
		}
		$this->aDBc_total_tables_to_repair = count($this->aDBc_tables_name_to_repair);

		// Prepare data
		aDBc_prepare_items_to_display(
			$this->aDBc_tables_to_display,
			$this->aDBc_tables_categories_info,
			$this->aDBc_which_button_to_show,
			$this->aDBc_tables_name_to_optimize,
			$this->aDBc_tables_name_to_repair,
			$this->array_belongs_to_counts,
			$this->aDBc_message,
			$this->aDBc_class_message,
			"tables"
		);

		// Call WP prepare_items function
		$this->prepare_items();
	}

	/** WP: Get columns */
	function get_columns(){
		$aDBc_belongs_to_toolip = "<span class='aDBc-tooltips-headers'>
									<img class='aDBc-info-image' src='".  ADBC_PLUGIN_DIR_PATH . '/images/information2.svg' . "'/>
									<span>" . __('Indicates the creator of the table: either a plugin, a theme or WordPress itself. If not sure about the creator, an estimation (%) will be displayed. The higher the percentage is, the more likely that the table belongs to that creator.','advanced-database-cleaner') ." </span>
								  </span>";
		$columns = array(
			'cb'          		=> '<input type="checkbox" />',
			'table_name' 		=> __('Table name','advanced-database-cleaner'),
			'table_prefix' 		=> __('Prefix','advanced-database-cleaner'),
			'table_rows' 		=> __('Rows','advanced-database-cleaner'),
			'table_size' 		=> __('Size','advanced-database-cleaner'),
			'table_lost' 		=> __('Lost','advanced-database-cleaner'),
			'site_id'   		=> __('Site','advanced-database-cleaner'),
			'table_belongs_to'  => __('Belongs to','advanced-database-cleaner') . $aDBc_belongs_to_toolip
		);
		return $columns;
	}

	function get_sortable_columns() {

		$sortable_columns = array(

			'table_name'   		=> array( 'table_name', false ),
			'table_rows'    	=> array( 'table_rows', false ),
			'table_size'    	=> array( 'table_size', false ),
			'site_id'    		=> array( 'site_id', false )

		);

		return $sortable_columns;
	}

	/** WP: Prepare items to display */
	function prepare_items() {
		$columns 	= $this->get_columns();
		$hidden 	= $this->get_hidden_columns();
		$sortable 	= $this->get_sortable_columns();
		$this->_column_headers 	= array($columns, $hidden, $sortable);
		$per_page 	= 50;
		if(!empty($_GET['per_page'])){
			$per_page = absint($_GET['per_page']);
		}
		$current_page = $this->get_pagenum();
		// Prepare sequence of tables to display
		$display_data = array_slice($this->aDBc_tables_to_display,(($current_page-1) * $per_page), $per_page);
		$this->set_pagination_args( array(
			'total_items' => count($this->aDBc_tables_to_display),
			'per_page'    => $per_page
		));
		$this->items = $display_data;
	}

	/** WP: Get columns that should be hidden */
    function get_hidden_columns() {

		// If MU, nothing to hide, else hide Side ID column
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			return array( 'table_prefix', 'table_lost' );

		} else {

			return array( 'table_prefix', 'table_lost', 'site_id' );

		}
    }

	/** WP: Column default */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'table_name':

				$prefix_and_name = $item['table_prefix'] . $item[$column_name];

				$return_name = "<span class='aDBc-bold'>" . esc_html($item['table_prefix']) . "</span>" . esc_html($item[$column_name]);

				if ( $item['table_lost'] > 0 && in_array( $prefix_and_name, $this->aDBc_tables_name_to_optimize ) ) {

					$lost = aDBc_get_size_from_bytes( $item['table_lost'] );

					$return_name .= "<br/>";
					$return_name .= "<span class='aDBc-lost-space'>" . __( 'Lost space', 'advanced-database-cleaner' ) . "</span>";
					$return_name .= "<span style='font-size:12px'> : " . $lost . "</span>";
					$return_name .= "<span style='color:grey'> (" .  __( 'to optimize', 'advanced-database-cleaner' ) . ")</span>";

				}

				if ( in_array( $prefix_and_name, $this->aDBc_tables_name_to_repair ) ) {

					$return_name .= "<br/>";
					$return_name .= "<span class='aDBc-corrupted'>" . __( 'Corrupted!', 'advanced-database-cleaner' ) . "</span>";
					$return_name .= "<span style='color:grey'> (" .  __( 'to repair', 'advanced-database-cleaner' ) . ")</span>";

				}

				return $return_name;
				break;

			case 'table_size':

				return aDBc_get_size_from_bytes( $item['table_size'] );
				break;

			case 'table_lost':

				return aDBc_get_size_from_bytes( $item['table_lost'] );
				break;

			case 'table_prefix':
			case 'table_rows':
			case 'site_id':
			case 'table_belongs_to':

			  return $item[$column_name];

			default:

			  return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes

		}
	}

	/** WP: Column cb for check box */
	function column_cb( $item ) {
		$value = $item['table_prefix'] . "|" . $item['table_name'];
		return sprintf( 
			'<input type="checkbox" name="aDBc_elements_to_process[]" value="%s" />', 
			esc_attr($value)
		);
	}

	/** WP: Get bulk actions */
	function get_bulk_actions() {

		$actions = array(
			'scan_selected' 		=> __( 'Scan selected tables', 'advanced-database-cleaner' ),
			'edit_categorization' 	=> __( 'Edit categorization', 'advanced-database-cleaner' ),
			'optimize'  			=> __( 'Optimize', 'advanced-database-cleaner' ),
			'repair'    			=> __( 'Repair', 'advanced-database-cleaner' ),
			'empty'    				=> __( 'Empty rows', 'advanced-database-cleaner' ),
			'delete'    			=> __( 'Delete', 'advanced-database-cleaner' )
		);

		if ( ADBC_PLUGIN_PLAN == "free" ) {

			unset( $actions['scan_selected'] );
			unset( $actions['edit_categorization'] );

		}

		return $actions;

	}

	/** WP: Message to display when no items found */
	function no_items() {

		_e( 'No tables found!', 'advanced-database-cleaner' );

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

		// Get the list of all tables names to validate selected tables
		global $wpdb;
		$sql_rows = "SELECT `TABLE_NAME` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '" . DB_NAME . "';";
		$valid_tables_names = $wpdb->get_col( $sql_rows );

        if ( $action == 'delete' ) {

			// Prepare an array containing names of tables deleted
			$names_deleted = array();

			// If the user wants to clean the tables he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				foreach($_POST['aDBc_elements_to_process'] as $table){
					$table_info 	= explode("|", $table, 2);
					$table_prefix 	= $table_info[0];
					$table_name 	= wp_unslash($table_info[1]); // Because WP adds slashes to the name in the POST request
					$full_table_name = $table_prefix . $table_name;

					// Validate table name before deleting
					if ( in_array( $full_table_name, $valid_tables_names ) ) {
						if($wpdb->query("DROP TABLE `" . $full_table_name . "`")){
							array_push($names_deleted, $table_name);
						}
					}
				}

				// After deleting tables, delete names also from file categorization
				// xxx (should I add this as well to options & crons?)
				if ( ADBC_PLUGIN_PLAN == "pro" ) {

					aDBc_refresh_categorization_file_after_delete($names_deleted, 'tables');

				}

				// Update the message to show to the user
				$this->aDBc_message = __('Selected tables cleaned successfully!', 'advanced-database-cleaner');
			}

        }else if($action == 'optimize'){
			// If the user wants to optimize the tables he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				foreach($_POST['aDBc_elements_to_process'] as $table) {
					$table_info 	= explode("|", $table, 2);
					$table_prefix 	= $table_info[0];
					$table_name 	= wp_unslash($table_info[1]); // Because WP adds slashes to the name in the POST request
					$full_table_name = $table_prefix . $table_name;

					// Validate table name before optimizing
					if ( in_array( $full_table_name, $valid_tables_names ) ) {
						$wpdb->query("OPTIMIZE TABLE `" . $full_table_name . "`");
						// run analyze sql query to force updating the table statistics
						$wpdb->query("ANALYZE TABLE `" . $full_table_name . "`");
					}

				}
				// Update the message to show to the user
				$this->aDBc_message = __('Selected tables optimized successfully!', 'advanced-database-cleaner');
			}
        }else if($action == 'empty'){
			// If the user wants to empty the tables he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				foreach($_POST['aDBc_elements_to_process'] as $table) {
					$table_info 	= explode("|", $table, 2);
					$table_prefix 	= $table_info[0];
					$table_name 	= wp_unslash($table_info[1]); // Because WP adds slashes to the name in the POST request
					$full_table_name = $table_prefix . $table_name;

					// Validate table name before emptying
					if ( in_array( $full_table_name, $valid_tables_names ) ) {
						$wpdb->query("TRUNCATE TABLE `" . $full_table_name . "`");
						// run analyze sql query to force updating the table statistics
						$wpdb->query("ANALYZE TABLE `" . $full_table_name . "`");
					}
				}
				// Update the message to show to the user
				$this->aDBc_message = __('Selected tables emptied successfully!', 'advanced-database-cleaner');
			}
        }else if($action == 'repair'){
			// If the user wants to repair the tables he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				$cannot_repair = 0;
				foreach($_POST['aDBc_elements_to_process'] as $table) {
					$table_info 	= explode("|", $table, 2);
					$table_prefix 	= $table_info[0];
					$table_name 	= wp_unslash($table_info[1]); // Because WP adds slashes to the name in the POST request
					$full_table_name = $table_prefix . $table_name;

					// Validate table name before repairing
					if ( in_array( $full_table_name, $valid_tables_names ) ) {
						$query_result = $wpdb->get_results("REPAIR TABLE `" . $full_table_name . "`");
						foreach($query_result as $row){
							if($row->Msg_type == 'error'){
								if(preg_match('/corrupt/i', $row->Msg_text)){
									$cannot_repair++;
								}
							} else {
								// run analyze sql query to force updating the table statistics
								$wpdb->query("ANALYZE TABLE `" . $full_table_name . "`");
							}
						}
					}
				}

				// Update the message to show to the user
				if($cannot_repair == 0){
					$this->aDBc_message = __('Selected tables repaired successfully!', 'advanced-database-cleaner');
				}else{
					$this->aDBc_class_message = "error";
					$this->aDBc_message = __('Some of your tables cannot be repaired!', 'advanced-database-cleaner');
				}
			}
        }else if($action == 'edit_categorization'){
			// If the user wants to edit categorization of the tables he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				// Create a temp file containing tables names to change categorization for
				$aDBc_path_items = @fopen(ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tables_manually_correction_temp.txt", "w");
				if($aDBc_path_items){
					foreach($_POST['aDBc_elements_to_process'] as $table) {
						$table_info = explode("|", $table, 2);
						$table_prefix 	= $table_info[0];
						$table_name 	= wp_unslash($table_info[1]); // Because WP adds slashes to the name in the POST request
						$full_table_name = $table_prefix . $table_name;

						// Validate table name before adding it to the file
						if ( in_array( $full_table_name, $valid_tables_names ) ) {
							fwrite($aDBc_path_items, $table_name . "\n");
						}
					}
					fclose($aDBc_path_items);
				}
			}
		}
    }

	/** Print the page content */
	function aDBc_print_page_content(){
		// Print a message if any
		if($this->aDBc_message != ""){
			echo '<div id="aDBc_message" class="' . $this->aDBc_class_message . ' notice is-dismissible"><p>' . $this->aDBc_message . '</p></div>';
		}

		// If the folder adbc_uploads cannot be created, show a msg to users
		if(!empty($this->aDBc_permission_adbc_folder_msg)){
			echo '<div class="error notice is-dismissible"><p>' . $this->aDBc_permission_adbc_folder_msg . '</p></div>';
		}

		?>
		<div class="aDBc-content-max-width">

		<?php

		// If tables_manually_correction_temp.txt exist, this means that user want to edit categorization.

		if ( ADBC_PLUGIN_PLAN == "pro" && file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . '/tables_manually_correction_temp.txt' ) ) {

			include_once 'edit_item_categorization.php';

		} else {

			// If not, we print the tables normally
			// Print a notice/warning according to each type of tables
			if ( ADBC_PLUGIN_PLAN == "pro" ) {

				if($_GET['aDBc_cat'] == 'o' && $this->aDBc_tables_categories_info['o']['count'] > 0){
					echo '<div class="aDBc-box-warning-orphan">' . __('Tables below seem to be orphan! However, please delete only those you are sure to be orphan!','advanced-database-cleaner') . '</div>';
				}else if(($_GET['aDBc_cat'] == 'all' || $_GET['aDBc_cat'] == 'u') && $this->aDBc_tables_categories_info['u']['count'] > 0){

					$aDBc_settings = get_option('aDBc_settings');
					$hide_not_categorized_msg = empty($aDBc_settings['hide_not_categorized_yet_msg']) ? "" : $aDBc_settings['hide_not_categorized_yet_msg'];
					if ( $hide_not_categorized_msg != "yes" ) {
						echo '<div id="aDBc-box-info" class="aDBc-box-info">' 
							. '<div style="width:100%">' 
							. __('Some of your tables are not categorized yet! Please click on the button below to categorize them!','advanced-database-cleaner') 
							. '</div>'
							. '<div><a href="#" id="aDBc-dismiss-not-categorized-yet-msg" title="' . __('Dismiss similar messages', 'advanced-database-cleaner') . '"><span class="dashicons dashicons-dismiss" style="text-decoration:none;font-size:16px;margin-top:4px"></span></a></div>'
						. '</div>';
					}
				}
			}

		?>

			<div class="aDBc-clear-both" style="margin-top:15px"></div>

			<!-- Code for "run new search" button + Show loading image -->
			<div style="float:left">

				<?php
				if ( $this->aDBc_which_button_to_show == "new_search" ) {
					$aDBc_search_text  	= __( 'Scan tables', 'advanced-database-cleaner' );
				} else {
					$aDBc_search_text  	= __( 'Continue scanning ...', 'advanced-database-cleaner' );
				}
				?>

				<!-- Hidden input used by ajax to know which item type we are dealing with -->
				<input type="hidden" id="aDBc_item_type" value="tables"/>

				<?php
				// These hidden inputs are used by ajax to see if we should execute scanning automatically after reloading a page
				$iteration = get_option("aDBc_temp_last_iteration_tables");
				$currently_scanning = get_option("aDBc_temp_currently_scanning_tables");
				?>
				<input type="hidden" id="aDBc_currently_scanning" value="<?php echo $currently_scanning; ?>"/>
				<input type="hidden" id="aDBc_iteration" value="<?php echo $iteration; ?>"/>
				<input type="hidden" id="aDBc_count_uncategorized" value="<?php echo $this->aDBc_tables_categories_info['u']['count']; ?>"/>
				<input type="hidden" id="aDBc_count_all_items" value="<?php echo $this->aDBc_tables_categories_info['all']['count']; ?>"/>

				<?php

				if ( ADBC_PLUGIN_PLAN == "pro" ) {

				?>

					<input id="aDBc_new_search_button" type="submit" class="aDBc-run-new-search" value="<?php echo $aDBc_search_text; ?>"  name="aDBc_new_search_button" />

				<?php

				} else {

				?>

					<div class="aDBc-premium-tooltip">

						<input id="aDBc_new_search_button" type="submit" class="aDBc-run-new-search" value="<?php echo $aDBc_search_text; ?>"  name="aDBc_new_search_button" style="opacity:0.5" disabled />

						<span style="width:390px" class="aDBc-premium-tooltiptext">

							<?php _e('Please <a href="?page=advanced_db_cleaner&aDBc_tab=premium">upgrade</a> to Pro to categorize and detect orphaned tables','advanced-database-cleaner') ?>

						</span>

					</div>

				<?php
				}
				?>

			</div>

			<!-- Print numbers of items found in each category -->
			<div class="aDBc-category-counts">

				<?php

				$aDBc_new_URI = $_SERVER['REQUEST_URI'];

				// Remove the paged parameter to start always from the first page when selecting a new category
				$aDBc_new_URI = remove_query_arg( 'paged', $aDBc_new_URI );

				foreach ( $this->aDBc_tables_categories_info as $abreviation => $category_info ) {

					$aDBc_new_URI 		= add_query_arg( 'aDBc_cat', $abreviation, $aDBc_new_URI );
					$selected_color 	= $abreviation == $_GET['aDBc_cat'] ? $category_info['color'] : '#eee';
					$aDBc_link_style 	= "color:" . $category_info['color'];
					$aDBc_count 		= $category_info['count'];

					if ( ADBC_PLUGIN_PLAN == "free" && $abreviation != "all" && $abreviation != "u" ) {

						$aDBc_new_URI 		= "";
						$aDBc_link_style 	= $aDBc_link_style . ";cursor:default;pointer-events:none";
						$aDBc_count 		= "-";

					}

				?>
					<span class="<?php echo $abreviation == $_GET['aDBc_cat'] ? 'aDBc-selected-category' : ''?>">

						<span class="aDBc-premium-tooltip aDBc-category-span">

							<a href="<?php echo esc_url( $aDBc_new_URI ); ?>" class="aDBc-category-counts-links" style="<?php echo $aDBc_link_style ?>">

								<span><?php echo $category_info['name']; ?></span>

							</a>

							<div class="aDBc-category-total" style="border:1px solid <?php echo $selected_color ?>; border-bottom:3px solid <?php echo $selected_color ?>;">

								<span style="color:#000"><?php echo $aDBc_count ?></span>

							</div>

							<?php
							if ( ADBC_PLUGIN_PLAN == "free" && $abreviation != "all" && $abreviation != "u" ) {
							?>

								<span style="width:150px" class="aDBc-premium-tooltiptext">
									<a href="https://sigmaplugin.com/downloads/wordpress-advanced-database-cleaner/" target="_blank">
										<?php _e( 'Available in Pro version!', 'advanced-database-cleaner' ); ?>
									</a>
								</span>

							<?php
							}
							?>

						</span>

					</span>

				<?php
				}
				?>

			</div>

			<div class="aDBc-clear-both"></div>

			<div id="aDBc-progress-container">

				<span id="aDBc_collected_files" href="#" style="color:gray">
				</span>

				<div class="aDBc-progress-background">
					<div id="aDBc-progress-bar" class="aDBc-progress-bar"></div>
				</div>

				<a id="aDBc_stop_scan" href="#" style="color:red">
					<?php _e('Stop the scan','advanced-database-cleaner') ?>
				</a>

				<span id="aDBc_stopping_msg" style="display:none">
					<?php _e('Stopping...','advanced-database-cleaner') ?>
				</span>

			</div>

			<?php include_once 'header_page_filter.php'; ?>

			<div class="aDBc-clear-both"></div>

			<form id="aDBc_form" action="" method="post">
				<div class="aDBc-left-content">
					<?php
					$this->display();
					?>
				</div>
			</form>

			<div class="aDBc-right-box">

				<div class="aDBc-right-box-content" style="text-align:center">

					<?php

					if ( $this->aDBc_total_tables_to_optimize == 0 && $this->aDBc_total_tables_to_repair == 0 ) {

					?>
						<img width="58px" src="<?php echo ADBC_PLUGIN_DIR_PATH . '/images/db_clean.svg'?>"/>
						<div class="aDBc-text-status-db"><?php _e( 'Your database is optimized!', 'advanced-database-cleaner' ); ?></div>

					<?php

					} else {

						// Add link to numbers of tables that should be optimized/repaired
						$aDBc_new_URI = $_SERVER['REQUEST_URI'];
						$aDBc_new_URI = remove_query_arg( array( 'paged', 's', 'belongs_to' ), $aDBc_new_URI );
						$aDBc_new_URI = add_query_arg( 'aDBc_cat', 'all', $aDBc_new_URI );
					?>
						<img width="55px" src="<?php echo ADBC_PLUGIN_DIR_PATH . '/images/warning.svg'?>"/>

						<?php
						if ( $this->aDBc_total_tables_to_optimize > 0 ) {

							$aDBc_new_URI = add_query_arg( 't_type', 'optimize', $aDBc_new_URI );

						?>
							<div class="aDBc-text-status-db">
								<b><a href="<?php echo esc_url( $aDBc_new_URI ); ?>"><?php echo $this->aDBc_total_tables_to_optimize; ?></a></b>
								<?php _e( 'table(s) should be optimized!', 'advanced-database-cleaner' ); ?>
							</div>

							<div>
								<?php
								$aDBc_table_size = aDBc_get_size_from_bytes( $this->aDBc_total_lost );
								echo __( 'You can save around', 'advanced-database-cleaner' ) . " : " . $aDBc_table_size;
								?>
							</div>

						<?php
						}

						if ( $this->aDBc_total_tables_to_repair > 0 ) {

							$aDBc_new_URI 	= add_query_arg( 't_type', 'repair', $aDBc_new_URI );
							$to_repair_css 	= $this->aDBc_total_tables_to_optimize > 0 ? "aDBc-to-repair-section" : "";

						?>
							<div class="aDBc-text-status-db <?php echo $to_repair_css; ?>">
								<b><a href="<?php echo esc_url( $aDBc_new_URI ); ?>"><?php echo $this->aDBc_total_tables_to_repair; ?></a></b>
								<?php _e( 'table(s) should be repaired!', 'advanced-database-cleaner' ); ?>
							</div>

						<?php
						}
					}
					?>

				</div>

				<div class="aDBc-right-box-content">

					<div style="text-align:center">
						<img width="60px" src="<?php echo ADBC_PLUGIN_DIR_PATH . '/images/alarm-clock.svg'?>"/>

						<?php
						$aDBc_schedules = get_option( 'aDBc_optimize_schedule' );
						$aDBc_schedules = is_array( $aDBc_schedules ) ? $aDBc_schedules : array();

						// Count schedules available
						$count_schedules = count( $aDBc_schedules );
						echo "<div class='aDBc-schedule-text'><b>" . $count_schedules ."</b> " .__('optimize schedule(s) set','advanced-database-cleaner') . "</div>";
						?>
					</div>

					<?php
					foreach ( $aDBc_schedules as $hook_name => $hook_params ) {

						echo "<div class='aDBc-schedule-hook-box'>";
						echo "<b>" . __( 'Name', 'advanced-database-cleaner' ) . "</b> : " . $hook_name;
						echo "</br>";

						// We convert hook name to a string because the arg maybe only a digit!
						$timestamp = wp_next_scheduled( "aDBc_optimize_scheduler", array( $hook_name . '' ) );
						if($timestamp){
							$next_run = get_date_from_gmt(date('Y-m-d H:i:s', (int) $timestamp), 'M j, Y - H:i');
						}else{
							$next_run = "---";
						}
						echo "<b>".__('Next run','advanced-database-cleaner') . "</b> : " . $next_run . "</br>";

						$operation1 = in_array('optimize', $hook_params['operations']) ? __('Optimize','advanced-database-cleaner') : '';
						$operation2 = in_array('repair', $hook_params['operations']) ? __('Repair','advanced-database-cleaner') : '';
						$plus = !empty($operation1) && !empty($operation2) ? " + " : "";
						echo "<b>".__('Perform','advanced-database-cleaner') . "</b> : " . $operation1 . $plus . $operation2 . "</br>";

						$repeat = $hook_params['repeat'];
						switch($repeat){
							case "once" :
								$repeat = __('Once','advanced-database-cleaner');
								break;
							case "hourly" :
								$repeat = __('Hourly','advanced-database-cleaner');
								break;
							case "twicedaily" :
								$repeat = __('Twice a day','advanced-database-cleaner');
								break;
							case "daily" :
								$repeat = __('Daily','advanced-database-cleaner');
								break;
							case "weekly" :
								$repeat = __('Weekly','advanced-database-cleaner');
								break;
							case "monthly" :
								$repeat = __('Monthly','advanced-database-cleaner');
								break;
						}

						echo "<b>".__('Frequency','advanced-database-cleaner') . "</b> : " . $repeat . "</br>";

						echo $hook_params['active'] == "1" ? "<img class='aDBc-schedule-on-off' src='". ADBC_PLUGIN_DIR_PATH . "/images/switch-on.svg" . "'/>" : "<img class='aDBc-schedule-on-off' src='". ADBC_PLUGIN_DIR_PATH . "/images/switch-off.svg" . "'/>";

						$aDBc_new_URI = $_SERVER['REQUEST_URI'];
						$aDBc_new_URI = add_query_arg('aDBc_view', 'edit_optimize_schedule', $aDBc_new_URI);
						$aDBc_new_URI = add_query_arg('hook_name', $hook_name, $aDBc_new_URI);

					?>

						<span class="aDBc-edit-delete-schedule">

							<a href="<?php echo esc_url( $aDBc_new_URI ); ?>" class="aDBc-edit-schedule-link">
								<?php _e( 'Edit', 'advanced-database-cleaner' ); ?>
							</a>
							|
							<form action="" method="post" class="aDBc-delete-schedule-link">
								<input type="hidden" name="aDBc_delete_schedule" value="<?php echo $hook_name ?>" />
								<input class="aDBc-submit-link" type="submit" value="<?php _e('Delete','advanced-database-cleaner') ?>" />
								<?php wp_nonce_field('delete_optimize_schedule_nonce', 'delete_optimize_schedule_nonce') ?>
							</form>

						</span>

						</div>
					<?php

					}

					$aDBc_new_URI = $_SERVER['REQUEST_URI'];
					$aDBc_new_URI = add_query_arg('aDBc_view', 'add_optimize_schedule', $aDBc_new_URI);
					?>

					<a href="<?php echo esc_url( $aDBc_new_URI ); ?>" id="aDBc_add_schedule" class="button-primary aDBc-add-new-schedule">
						<?php _e('Add new schedule','advanced-database-cleaner'); ?>
					</a>

				</div>

			</div>

			<div class="aDBc-clear-both"></div>
		<?php
		}
		?>
		</div>
	<?php
	}
}

new ADBC_Tables_List();

?>

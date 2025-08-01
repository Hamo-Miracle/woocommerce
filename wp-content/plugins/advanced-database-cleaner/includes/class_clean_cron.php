<?php

class ADBC_Tasks_List extends WP_List_Table {

	/** Holds the message to be displayed if any */
	private $aDBc_message = "";

	/** Holds the class for the message : updated or error. Default is updated */
	private $aDBc_class_message = "updated";

	/** Holds tasks that will be displayed */
	private $aDBc_tasks_to_display = array();

	/** Holds counts + info of tasks categories */
	private $aDBc_tasks_categories_info	= array();

	/** Should we display "run search" or "continue search" button (after a timeout failed). Default is "run search" */
	private $aDBc_which_button_to_show = "new_search";

	// This array contains belongs_to info about plugins and themes
	private $array_belongs_to_counts = array();

	// Holds msg that will be shown if folder adbc_uploads cannot be created by the plugin (This is verified after clicking on scan button)
	private $aDBc_permission_adbc_folder_msg = "";

    function __construct() {

        parent::__construct( array(
            'singular'  => __( 'Task', 'advanced-database-cleaner' ),
            'plural'    => __( 'Tasks', 'advanced-database-cleaner' ),
            'ajax'      => false
		) );

		$this->aDBc_prepare_and_count_tasks();
		$this->aDBc_print_page_content();
    }

	/** Prepare items */
	function aDBc_prepare_and_count_tasks() {

		if ( ADBC_PLUGIN_PLAN == "pro" ) {

			// Verify if the adbc_uploads cannot be created
			$adbc_folder_permission = get_option( "aDBc_permission_adbc_folder_needed" );

			if ( ! empty( $adbc_folder_permission ) ) {

				$this->aDBc_permission_adbc_folder_msg = sprintf( __( 'The plugin needs to create the following directory "%1$s" to save the scan results but this was not possible automatically. Please create that directory manually and set correct permissions so it can be writable by the plugin.','advanced-database-cleaner' ), ADBC_UPLOAD_DIR_PATH_TO_ADBC );

				// Once we display the msg, we delete that option from DB
				delete_option( "aDBc_permission_adbc_folder_needed" );

			}

		}

		// Verify if the user wants to edit the categorization of a task. This block test comes from edit_item_categorization.php
		if ( ADBC_PLUGIN_PLAN == "pro" ) {

			if ( isset( $_POST['aDBc_cancel'] ) ) {

				// If the user cancels the edit, remove the temp file
				if ( file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tasks_manually_correction_temp.txt" ) )
					unlink( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tasks_manually_correction_temp.txt" );

			} else if ( isset( $_POST['aDBc_correct'] ) ) {

				// Get the new belongs to of items
				$new_belongs_to = $_POST['new_belongs_to'];

				// Get value of checkbox to see if user wants to send correction to the server
				if ( isset( $_POST['aDBc_send_correction_to_server'] ) ) {
					$this->aDBc_message = aDBc_edit_categorization_of_items( "tasks", $new_belongs_to, 1 );
				} else {
					$this->aDBc_message = aDBc_edit_categorization_of_items( "tasks", $new_belongs_to, 0 );
				}

				// Remove the temp file
				if ( file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tasks_manually_correction_temp.txt" ) )
					unlink( ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tasks_manually_correction_temp.txt" );
			}
		}

		// Process bulk action if any before preparing tasks to display
		$this->process_bulk_action();

		// Prepare data
		aDBc_prepare_items_to_display(
			$this->aDBc_tasks_to_display,
			$this->aDBc_tasks_categories_info,
			$this->aDBc_which_button_to_show,
			array(),
			array(),
			$this->array_belongs_to_counts,
			$this->aDBc_message,
			$this->aDBc_class_message,
			"tasks"
		);

		// Call WP prepare_items function
		$this->prepare_items();
	}

	/** WP: Get columns */
	function get_columns() {

		$aDBc_belongs_to_toolip = "<span class='aDBc-tooltips-headers'>
									<img class='aDBc-info-image' src='".  ADBC_PLUGIN_DIR_PATH . '/images/information2.svg' . "'/>
									<span>" . __( 'Indicates the creator of the task: either a plugin, a theme or WordPress itself. If not sure about the creator, an estimation (%) will be displayed. The higher the percentage is, the more likely that the task belongs to that creator.','advanced-database-cleaner' ) ." </span>
								  </span>";
		$columns = array(
			'cb'        		=> '<input type="checkbox" />',
			'hook_name' 		=> __( 'Hook name','advanced-database-cleaner' ),
			'arguments' 		=> __( 'Arguments','advanced-database-cleaner' ),
			'next_run'  		=> __( 'Next run - Frequency','advanced-database-cleaner' ),
			'site_id'   		=> __( 'Site','advanced-database-cleaner' ),
			'hook_belongs_to'  	=> __( 'Belongs to','advanced-database-cleaner' ) . $aDBc_belongs_to_toolip
		);

		return $columns;
	}

	function get_sortable_columns() {

		$sortable_columns = array(

			'hook_name'  => array( 'hook_name', false ),
			'site_id'    => array( 'site_id', false )

		);

		return $sortable_columns;
	}

	/** WP: Prepare items to display */
	function prepare_items() {
		$columns 	= $this->get_columns();
		$hidden 	= $this->get_hidden_columns();
		$sortable 	= $this->get_sortable_columns();
		$this->_column_headers  = array($columns, $hidden, $sortable);
		$per_page 	= 50;
		if(!empty($_GET['per_page'])){
			$per_page = absint($_GET['per_page']);
		}
		$current_page = $this->get_pagenum();
		// Prepare sequence of tasks to display
		$display_data = array_slice($this->aDBc_tasks_to_display,(($current_page-1) * $per_page), $per_page);
		$this->set_pagination_args( array(
			'total_items' => count($this->aDBc_tasks_to_display),
			'per_page'    => $per_page
		));
		$this->items = $display_data;
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

	/** WP: Column default */
	function column_default($item, $column_name){
		switch($column_name){
			case 'arguments':
				if($item[$column_name] == "none"){
					return "<span>" . __('None', 'advanced-database-cleaner') . "</span>";
				}else{
					$unserialized_args = json_decode($item[$column_name]);

					// If the arguments are not an array, we return [N/A]
					if (is_array($unserialized_args)) {
						$args_as_string = array_map(function($arg) {
							return (is_object($arg) || is_array($arg)) ? json_encode($arg) : (string) $arg;
						}, $unserialized_args);
					
						return "<span class='aDBc-arguments'>" . implode(" / ", $args_as_string) . "</span>";
					} else {
						return "<span class='aDBc-arguments'>[N/A]</span>";
					}
				}
				break;
			case 'hook_name':
				return esc_html($item[$column_name]);
			case 'next_run':
			case 'site_id':
			case 'hook_belongs_to':
			  return $item[$column_name];
			default:
			  return print_r($item, true) ; //Show the whole array for troubleshooting purposes
		}
	}

	/** WP: Column cb for check box */
	function column_cb($item) {

		$data = array(
			'site_id'   => $item['site_id'],
			'hook_name' => $item['hook_name'],
			'timestamp' => $item['timestamp'],
			'arguments' => $item['arguments']
		);

		$value = json_encode($data);

		return sprintf(
			"<input type='checkbox' name='aDBc_elements_to_process[]' value='%s' />", 
			esc_attr($value)
		);
	}

	/** WP: Get bulk actions */
	function get_bulk_actions() {

		$actions = array(
			'scan_selected' 		=> __( 'Scan selected tasks','advanced-database-cleaner' ),
			'edit_categorization' 	=> __( 'Edit categorization','advanced-database-cleaner' ),
			'delete'    			=> __( 'Delete','advanced-database-cleaner' )
		);

		if ( ADBC_PLUGIN_PLAN == "free" ) {

			unset( $actions['scan_selected'] );
			unset( $actions['edit_categorization'] );

		}

		return $actions;
	}

	/** WP: Message to display when no items found */
	function no_items() {

		_e('No tasks found!','advanced-database-cleaner');

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

        if ( $action == 'delete' ) {

			// If the user wants to clean the tasks he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				if(function_exists('is_multisite') && is_multisite()){
					// Prepare tasks to delete in organized array to minimize switching from blogs
					$tasks_to_delete = array();
					foreach($_POST['aDBc_elements_to_process'] as $task){

						$json 		= wp_unslash( $task ); // Unslash because WP adds slashes to the POST data
						$data 		= json_decode( $json, true );
						$site_id 	= sanitize_html_class($data['site_id']);

						if(is_numeric($site_id)){
							if(empty($tasks_to_delete[$site_id])){
								$tasks_to_delete[$site_id] = array();
							}
							array_push($tasks_to_delete[$site_id], $task);
						}
					}
					// Delete tasks
					foreach($tasks_to_delete as $site_id => $tasks_info){
						switch_to_blog($site_id);
						foreach($tasks_info as $task) {

							$json 		= wp_unslash( $task ); // Unslash because WP adds slashes to the POST data
							$data 		= json_decode( $json, true );
	
							$hook 		= $data['hook_name'];
							$timestamp 	= $data['timestamp'];
							$args 		= $data['arguments'];

							if(is_numeric($timestamp)){
								if($args == "none"){
									wp_unschedule_event($timestamp, $hook);
								}else{
									$args = json_decode(stripslashes($args));
									wp_unschedule_event($timestamp, $hook, $args);
									// Check if the user has deleted a task beloging to this plugin. If so, update his data in DB to inactive
									// A task of ADBC cannot be without an arg, not necessary to add this check to "none" args
									if($hook == "aDBc_clean_scheduler"){
										aDBc_update_task_in_db_after_delete($args[0], "aDBc_clean_schedule");
									}else if($hook == "aDBc_optimize_scheduler"){
										aDBc_update_task_in_db_after_delete($args[0], "aDBc_optimize_schedule");
									}
								}
							}
						}
						restore_current_blog();
					}
				}else{
					foreach($_POST['aDBc_elements_to_process'] as $task) {

						$json 		= wp_unslash( $task ); // Unslash because WP adds slashes to the POST data
						$data 		= json_decode( $json, true );

						$hook 		= $data['hook_name'];
						$timestamp 	= $data['timestamp'];
						$args 		= $data['arguments'];

						if(is_numeric($timestamp)){
							if($args == "none"){
								wp_unschedule_event($timestamp, $hook);
							}else{
								$args = json_decode(stripslashes($args));
								wp_unschedule_event($timestamp, $hook, $args);
								// Check if the user has deleted a task beloging to this plugin. If so, update his data in DB to inactive
								// A task of ADBC cannot be without an arg, not necessary to add this check to "none" args
								if($hook == "aDBc_clean_scheduler"){
									aDBc_update_task_in_db_after_delete($args[0], "aDBc_clean_schedule");
								}else if($hook == "aDBc_optimize_scheduler"){
									aDBc_update_task_in_db_after_delete($args[0], "aDBc_optimize_schedule");
								}
							}
						}
					}
				}
				// Update the message to show to the user
				$this->aDBc_message = __('Selected scheduled tasks cleaned successfully!', 'advanced-database-cleaner');
			}
        }else if($action == 'edit_categorization'){
			// If the user wants to edit categorization of the tasks he/she selected
			if(isset($_POST['aDBc_elements_to_process'])){
				// Create a temp file containing tasks names to change categorization for
				$aDBc_path_items = @fopen(ADBC_UPLOAD_DIR_PATH_TO_ADBC . "/tasks_manually_correction_temp.txt", "w");
				if($aDBc_path_items){
					foreach($_POST['aDBc_elements_to_process'] as $task) {

						$json = wp_unslash( $task ); // Unslash because WP adds slashes to the POST data
						$data = json_decode( $json, true );
						$hook = $data['hook_name'];

						fwrite($aDBc_path_items, $hook . "\n");
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

		// If tasks_manually_correction_temp.txt exist, this means that user want to edit categorization

		if ( ADBC_PLUGIN_PLAN == "pro" && file_exists( ADBC_UPLOAD_DIR_PATH_TO_ADBC . '/tasks_manually_correction_temp.txt' ) ) {

			include_once 'edit_item_categorization.php';

		} else {

			// If not, we print the tasks normally
			// Print a notice/warning according to each type of tasks
			if ( ADBC_PLUGIN_PLAN == "pro" ) {

				if($_GET['aDBc_cat'] == 'o' && $this->aDBc_tasks_categories_info['o']['count'] > 0){
					echo '<div class="aDBc-box-warning-orphan">' . __('Tasks below seem to be orphan! However, please delete only those you are sure to be orphan!','advanced-database-cleaner') . '</div>';
				}else if(($_GET['aDBc_cat'] == 'all' || $_GET['aDBc_cat'] == 'u') && $this->aDBc_tasks_categories_info['u']['count'] > 0){

					$aDBc_settings = get_option('aDBc_settings');
					$hide_not_categorized_msg = empty($aDBc_settings['hide_not_categorized_yet_msg']) ? "" : $aDBc_settings['hide_not_categorized_yet_msg'];
					if ( $hide_not_categorized_msg != "yes" ) {
						echo '<div id="aDBc-box-info" class="aDBc-box-info">' 
							. '<div style="width:100%">' 
							. __('Some of your tasks are not categorized yet! Please click on the button below to categorize them!','advanced-database-cleaner') 
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
					$aDBc_search_text  	= __( 'Scan tasks', 'advanced-database-cleaner' );
				} else {
					$aDBc_search_text  	= __( 'Continue scanning ...', 'advanced-database-cleaner' );
				}
				?>

				<!-- Hidden input used by ajax to know which item type we are dealing with -->
				<input type="hidden" id="aDBc_item_type" value="tasks"/>

				<?php
				// These hidden inputs are used by ajax to see if we should execute the scan automatically after reloading a page
				$iteration = get_option("aDBc_temp_last_iteration_tasks");
				$currently_scanning = get_option("aDBc_temp_currently_scanning_tasks");
				?>
				<input type="hidden" id="aDBc_currently_scanning" value="<?php echo $currently_scanning; ?>"/>
				<input type="hidden" id="aDBc_iteration" value="<?php echo $iteration; ?>"/>
				<input type="hidden" id="aDBc_count_uncategorized" value="<?php echo $this->aDBc_tasks_categories_info['u']['count']; ?>"/>
				<input type="hidden" id="aDBc_count_all_items" value="<?php echo $this->aDBc_tasks_categories_info['all']['count']; ?>"/>

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

							<?php _e('Please <a href="?page=advanced_db_cleaner&aDBc_tab=premium">upgrade</a> to Pro to categorize and detect orphaned tasks','advanced-database-cleaner') ?>

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

				foreach ( $this->aDBc_tasks_categories_info as $abreviation => $category_info ) {

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

							<a href="<?php echo esc_url( $aDBc_new_URI ) ?>" class="aDBc-category-counts-links" style="<?php echo $aDBc_link_style ?>">

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

				<?php
				$this->display();
				?>

			</form>
		<?php
		}
		?>
		</div>
	<?php
	}
}

new ADBC_Tasks_List();

?>

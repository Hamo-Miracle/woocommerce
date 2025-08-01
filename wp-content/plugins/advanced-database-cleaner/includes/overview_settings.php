<?php

global $wpdb, $wp_version;

// DB size
$aDBc_db_size = $wpdb->get_var("SELECT sum(round(((data_length + index_length) / 1024), 2)) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");

if($aDBc_db_size >= 1024){
	$aDBc_db_size = round(($aDBc_db_size / 1024), 2) . " MB";
}else{
	$aDBc_db_size = round($aDBc_db_size, 2) . " KB";
}

// Total tables
$aDBc_total_tables = $wpdb->get_var("SELECT count(*) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");

// Total options
if(function_exists('is_multisite') && is_multisite()){

	$aDBc_options_toolip = "<span class='aDBc-tooltips-headers'>
					<img class='aDBc-info-image' src='".  ADBC_PLUGIN_DIR_PATH . '/images/information2.svg' . "'/>
					<span>" . __('Indicates the total number of rows in your option tables of all your network sites, including transients...','advanced-database-cleaner') ." </span>
				  </span>";

}else{

	$aDBc_options_toolip = "<span class='aDBc-tooltips-headers'>
					<img class='aDBc-info-image' src='".  ADBC_PLUGIN_DIR_PATH . '/images/information2.svg' . "'/>
					<span>" . __('Indicates the total number of rows in your options table, including transients...','advanced-database-cleaner') ." </span>
				  </span>";

}

// Total options
$aDBc_total_options = 0;
if(function_exists('is_multisite') && is_multisite()){
	$blogs_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	foreach($blogs_ids as $blog_id){
		switch_to_blog($blog_id);
		global $wpdb;
		$aDBc_total_options += $wpdb->get_var("SELECT count(*) FROM $wpdb->options");
		restore_current_blog();
	}
}else{
	// Count total options
	$aDBc_total_options = $wpdb->get_var("SELECT count(*) FROM $wpdb->options");
}

// Total scheduled tasks
$aDBc_all_tasks = aDBc_get_all_scheduled_tasks();
$aDBc_total_tasks = 0;
if(function_exists('is_multisite') && is_multisite()){
	foreach($aDBc_all_tasks as $hook => $task_info){
		foreach($task_info['sites'] as $site => $info){
			$aDBc_total_tasks += count($task_info['sites'][$site]['args']);
		}
	}
}else{
	foreach($aDBc_all_tasks as $hook => $task_info){
		$aDBc_total_tasks += count($task_info['sites'][1]['args']);
	}
}

// Is MU?
if(function_exists('is_multisite') && is_multisite()){
	$aDBc_is_mu = __('Yes', 'advanced-database-cleaner');
	$aDBc_number_sites = $wpdb->get_var("SELECT count(*) FROM $wpdb->blogs");
}else{
	$aDBc_is_mu = __('No', 'advanced-database-cleaner');
	$aDBc_number_sites = "1";
}

?>

<div class="aDBc-content-max-width">
	<div class="aDBc-overview-box">
		<div class="aDBc-overview-box-head"><?php _e('Overview', 'advanced-database-cleaner'); ?></div>
		<ul>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('WP Version', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $wp_version ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Database size', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_db_size ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Total tables', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_total_tables ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php echo __('Total options', 'advanced-database-cleaner') . $aDBc_options_toolip ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_total_options ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Total cron tasks', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_total_tasks ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('WP multisite Enabled ?', 'advanced-database-cleaner'); ?>
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_is_mu ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Number of sites', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo $aDBc_number_sites ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Script Max timeout', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo ADBC_ORIGINAL_TIMEOUT . " ". __('seconds', 'advanced-database-cleaner') ?></div>
			</li>

			<li>
				<div class="aDBc-overview-text-left">
					<span class="dashicons dashicons-yes aDBc-overview-dashicon"></span>
					<?php _e('Local time', 'advanced-database-cleaner'); ?> :
				</div>
				<div class="aDBc-float-left"><?php echo date_i18n('Y-m-d H:i:s') ?></div>
			</li>

		</ul>
	</div>

	<div class="aDBc-overview-box">

		<div class="aDBc-overview-box-head"><?php _e('Settings', 'advanced-database-cleaner') ?></div>

		<form action="" method="post">
			<ul>

				<?php
					$aDBc_settings = get_option('aDBc_settings');
					$in_main_site_msg = "";
					if(is_multisite()){
						$in_main_site_msg = __('(In main site only)', 'advanced-database-cleaner');
					?>
						<li style="padding-top:10px;padding-bottom:10px">
							<input type="checkbox" name="aDBc_network_menu" <?php echo (!empty($aDBc_settings['network_menu']) && $aDBc_settings['network_menu'] != "0") ? "checked='checked'" : "" ?>/>
							<?php _e('Show network plugin menu', 'advanced-database-cleaner'); ?>
							<div class="aDBc-overview-setting-desc">
								<?php _e('Displays a menu at the left side of your network admin panel', 'advanced-database-cleaner'); ?>
							</div>
						</li>		

					<?php 
					}
					?>

				<li style="padding-top:10px;padding-bottom:10px">
					<input type="checkbox" name="aDBc_left_menu" <?php echo (!empty($aDBc_settings['left_menu']) && $aDBc_settings['left_menu'] != "0") ? "checked='checked'" : "" ?>/>
					<?php echo __('Show plugin left menu', 'advanced-database-cleaner') . ' ' . $in_main_site_msg; ?>
					<div class="aDBc-overview-setting-desc">
						<?php _e('Displays a menu at the left side of your WP admin', 'advanced-database-cleaner'); ?>
					</div>
				</li>

				<li style="padding-top:10px;padding-bottom:10px">
					<input type="checkbox" name="aDBc_menu_under_tools" <?php echo (!empty($aDBc_settings['menu_under_tools']) && $aDBc_settings['menu_under_tools'] != "0") ? "checked='checked'" : "" ?>/>
					<?php echo __('Show plugin menu under tools', 'advanced-database-cleaner') . ' ' . $in_main_site_msg;; ?>
					<div class="aDBc-overview-setting-desc">
						<?php _e('Displays a menu under "tools" menu', 'advanced-database-cleaner'); ?>
					</div>
				</li>

				<?php
				if ( ADBC_PLUGIN_PLAN == "free" ) {
				?>

					<li>
						<input type="checkbox" name="aDBc_hide_premium_tab" <?php echo (!empty($aDBc_settings['hide_premium_tab']) && $aDBc_settings['hide_premium_tab']) == '1' ? "checked='checked'" : ""?>/>
						<?php _e('Hide premium tab', 'advanced-database-cleaner'); ?>
						<div class="aDBc-overview-setting-desc">
							<?php _e('If checked, it will hide the above premium tab', 'advanced-database-cleaner'); ?>
						</div>
					</li>

				<?php
				}
				?>

			</ul>

			<div id="aDBc_save_settings" class="button-primary aDBc-save-settings">

				<span id="aDBc_save_icon" class="dashicons dashicons-saved aDBc-button-icon"></span>

				<?php _e( 'Save settings', 'advanced-database-cleaner' ); ?>

			</div>

		</form>

	</div>

	<div class="aDBc-clear-both"></div>

</div>

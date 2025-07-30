<?php

/**
 * Option Panel
 *
 * @package Blogfull
 */

function blogfull_customize_register($wp_customize) {

	//=================================
	// Trending Posts Section.
	//=================================

	$wp_customize->add_section('news_ticker_section',
		array(
			'title' => esc_html__('News Ticker', 'blogfull'),
			'priority' => 31,
			'capability' => 'edit_theme_options', 
		)
	);

	$wp_customize->add_setting('enable_news_ticker',
		array(
			'default' => false,
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'blogus_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(new Blogus_Toggle_Control( $wp_customize, 'enable_news_ticker', 
		array(
			'label' => esc_html__('Enable / Disable', 'blogfull'),
			'section' => 'news_ticker_section',
			'type' => 'toggle',
			'priority' => 22,

		)
	));

	// Setting - number_of_slides.
	$wp_customize->add_setting('news_ticker_title',
		array(
			'default' =>  __('Trending', 'blogfull'),
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			// 'transport' => $selective_refresh
		)
	);

	$wp_customize->add_control('news_ticker_title',
		array(
			'label' => esc_html__('Title', 'blogfull'),
			'section' => 'news_ticker_section',
			'type' => 'text',
			'priority' => 23,
			// 'active_callback' => 'blogfull_flash_posts_section_status'

		)
	);

	// Setting - drop down category for slider.
	$wp_customize->add_setting('news_ticker_category',
		array(
			'default' => 0,
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint',
		)
	);


	$wp_customize->add_control(new blogus_Dropdown_Taxonomies_Control($wp_customize, 'news_ticker_category',
		array(
			'label' => esc_html__('Category', 'blogfull'),
			'description' => esc_html__('Posts to be shown on trending posts ', 'blogfull'),
			'section' => 'news_ticker_section',
			'type' => 'dropdown-taxonomies',
			'taxonomy' => 'category',
			'priority' => 23,
			// 'active_callback' => 'blogfull_flash_posts_section_status'
		)
	));
}
add_action('customize_register', 'blogfull_customize_register');
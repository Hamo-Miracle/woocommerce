/**
 * customizer.js
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	var myCustomizer = window.parent.window.wp.customize;

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title a, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title a, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-title a, .site-description' ).css( {
					'color': to
				} );
			}
		} );
	} );
	
	// Header text hide and show and text color.
	wp.customize( 'header_textcolor', function( value ) {
		if(value() == 'blank'){
			myCustomizer.control('blogus_title_font_size').container.hide();
		}else{
			myCustomizer.control('blogus_title_font_size').container.show();
		}
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title a, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
				$( '.site-branding-text ' ).addClass('d-none');
				myCustomizer.control('blogus_title_font_size').container.hide();
			} else {
				$('.site-title').css('position', 'unset');
				$( '.site-title a, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-branding-text ' ).removeClass('d-none');
				$( '.site-title a, .site-description' ).css( {
					'color': to
				} );
				myCustomizer.control('blogus_title_font_size').container.show();
			}
		} );
	} );
	
	// Site Title Font Size.
	wp.customize( 'blogus_title_font_size', function( value ) {
		value.bind( function( newVal ) {
			$( '.site-title a' ).css( {
				'font-size': newVal+'px',
			} );
		} );
	} );
	
	// Sidebar Width.
	wp.customize( 'blogus_theme_sidebar_width', function( value ) {
		value.bind( function( newVal ) {
			var contentRightElements = document.querySelectorAll('.content-right');
			var Rightsidebar = document.querySelectorAll('.sidebar-right');
			var Leftsidebar = document.querySelectorAll('.sidebar-left');
			contentRightElements.forEach(function(element) {
				element.style.setProperty('width', `calc((1130px - ${newVal}px))`, 'important');
			});
			Rightsidebar.forEach(function(element) {
				element.style.setProperty('width', `${newVal}px`, 'important');
			});
			Leftsidebar.forEach(function(element) {
				element.style.setProperty('width', `${newVal}px`, 'important');
			});
		} );
	} );
	
	// Footer logo width.
	wp.customize( 'blogus_footer_logo_width', function( value ) {
		value.bind( function( newVal ) {
			$( 'footer .footer-logo img' ).css( {
				'width': newVal+'px',
			} );
		} );
	} );
	
	// Footer logo Height.
	wp.customize( 'blogus_footer_logo_height', function( value ) {
		value.bind( function( newVal ) {
			$( 'footer .footer-logo img' ).css( {
				'height': newVal+'px',
			} );
		} );
	} );
	// Footer Background Image
	wp.customize( 'blogus_footer_widget_background', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('footer.footer').css('background-image', 'url(' + newVal + ')');
				$('footer.footer').addClass('back-img');
			}else{
				$('footer.footer').removeAttr('style');
				$('footer.footer').removeClass('back-img');
			}
		});
	});

	// Footer Background overlay color.
	wp.customize( 'blogus_footer_overlay_color', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('footer .overlay').css('background', newVal);
			}else{
				$('footer .overlay').css('background', '');
			}
		} );
	} );
	// Footer all Text color.
	wp.customize( 'blogus_footer_text_color', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('footer .bs-widget p, .site-title-footer a, .site-title-footer a:hover, .site-description-footer, .site-description-footer:hover, footer .bs-widget h6, footer .mg_contact_widget .bs-widget h6, footer .bs-widget ul li a').css('color', newVal);
			}else{
				$('footer .bs-widget p, .site-title-footer a, .site-title-footer a:hover, .site-description-footer, .site-description-footer:hover, footer .bs-widget h6, footer .mg_contact_widget .bs-widget h6, footer .bs-widget ul li a').css('color', '');
			}
		} );
	} );

	// Footer Widget Area Column.
	wp.customize( 'blogus_footer_column_layout', function( value ) {
		var colum = 12 / value();
		var wclass = $('.animated.bs-widget');
		if(wclass.hasClass('col-md-12')){
			wclass.removeClass('col-md-12');
		}else if(wclass.hasClass('col-md-6')){
			wclass.removeClass('col-md-6');
		}else if(wclass.hasClass('col-md-4')){
			wclass.removeClass('col-md-4');
		}else if(wclass.hasClass('col-md-3')){
			wclass.removeClass('col-md-3');
		}
		wclass.addClass(`col-md-${colum}`);

		value.bind( function( newVal ) {
			colum = 12 / newVal;
			wclass = $('.animated.bs-widget');
			if(wclass.hasClass('col-md-12')){
				wclass.removeClass('col-md-12');
			}else if(wclass.hasClass('col-md-6')){
				wclass.removeClass('col-md-6');
			}else if(wclass.hasClass('col-md-4')){
				wclass.removeClass('col-md-4');
			}else if(wclass.hasClass('col-md-3')){
				wclass.removeClass('col-md-3');
			}
			wclass.addClass(`col-md-${colum}`);
			console.log(wclass);
		} );
	} );

	wp.customize( 'scrollup_layout', function( value ) {
		value.bind( function( newVal ) {
			$('.bs_upscr i').removeClass();
			$('.bs_upscr i').addClass(newVal);
		});
	});
	// wp.customize( 'header_textcolor_dark_layout', function( value ) {
	// 	value.bind( function( newVal ) {
	// 		$( 'body.dark .site-title a, body.dark .site-description' ).css( {
	// 			'color': newVal,
	// 		} );
	// 	} );
	// } );

	// Featured Links Background overlay Image.
	wp.customize( 'fatured_post_image_one', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('.promoss .one .bs-widget.promo').css('background-image', 'url(' + newVal + ')');
			}else{
				$('.promoss .one .bs-widget.promo').removeAttr('style');
			}
		} );
	} );
	wp.customize( 'fatured_post_image_two', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('.promoss .two .bs-widget.promo').css('background-image', 'url(' + newVal + ')');
			}else{
				$('.promoss .two .bs-widget.promo').removeAttr('style');
			}
		} );
	} );
	wp.customize( 'fatured_post_image_three', function( value ) {
		value.bind( function( newVal ) {
			if(newVal !== ''){
				$('.promoss .three .bs-widget.promo').css('background-image', 'url(' + newVal + ')');
			}else{
				$('.promoss .three .bs-widget.promo').removeAttr('style');
			}
		} );
	} );
	wp.customize('background_image', function(value) {
        value.bind(function(newImage) {
            if (newImage) {
                $('.wrapper').css('background-color', 'transparent');
            }
        });
    });
	function customizePreviewStyle(settingId, target, property, unit = '') {
		wp.customize(settingId, function(value) {
			value.bind(function(newVal) {
				if (property.startsWith('--')) { 
					document.documentElement.style.setProperty(property, newVal + unit);
				} else { 
					let styleElement = document.getElementById('customizer-preview-styles');
					 
					if (!styleElement) {
						styleElement = document.createElement('style');
						styleElement.id = 'customizer-preview-styles';
						document.head.appendChild(styleElement);
					}
					 
					let styleContent = styleElement.innerHTML;
					 
					const regex = new RegExp(`${target}\\s*{[^}]*}`, 'g');
					styleContent = styleContent.replace(regex, '');
					 
					styleContent += `${target} { ${property}: ${newVal + unit}; }`;
					 
					styleElement.innerHTML = styleContent;
				}
			});
		});
	}
	// For a CSS variable
	customizePreviewStyle('site_title_fontfamily', ':root', '--title-font', '');
	customizePreviewStyle('site_title_fontweight', ':root', '--title-weight', '');
	customizePreviewStyle('blogus_menu_fontfamily', ':root', '--menus-font', '');
	customizePreviewStyle('background_color', ':root', '--wrap-color', '');

	// For a normal CSS property
	customizePreviewStyle('blogus_slider_overlay_text_color', '.bs-slide .inner .title a', 'color', '');
	customizePreviewStyle('blogus_footer_copy_bg', 'footer .bs-footer-copyright .bs-footer-overlay-copyright', 'background-color','');
	customizePreviewStyle('blogus_footer_copy_text', 'footer .bs-footer-copyright p, footer .bs-footer-copyright a', 'color','');
	customizePreviewStyle('blogus_slider_title_font_size', '.homemain .bs-slide .inner .title', 'font-size','px');
	customizePreviewStyle('primary_menu_bg_color', 'header.bs-default .navbar-collapse ul, .navbar-wp .dropdown-menu > li > a:hover, .navbar-wp .dropdown-menu > li > a:focus', 'background', '');

} )( jQuery );
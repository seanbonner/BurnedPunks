/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function () {

	wp.customize.bind('ready', function () {

		// Only show the color hue control when there's a custom primary color.
		wp.customize('minimalio_settings_fixed_heading', function (setting) {

			wp.customize.control('minimalio_heading_options_fixed_background', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_heading_options_fixed_color', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_heading_options_fixed_color_hover', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_options_mobile_menu_icon_colour_fixed', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show header container width when horizontal is selected
		wp.customize('minimalio_settings_header_variation', function (setting) {
			wp.customize.control('minimalio_options_header_container_type', function (control) {
				var visibility = function () {
					if ('vertical' === setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_header_variation', function (setting) {
			wp.customize.control('minimalio_options_menu_position', function (control) {
				var visibility = function () {
					if ('vertical' === setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_logo_position', function (setting) {
			wp.customize.control('minimalio_options_menu_position', function (control) {
				var visibility = function () {
					if ('center' === setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio filter label if filter categories enabled
		wp.customize('minimalio_settings_archive_template_filter_enable', function (setting) {
			wp.customize.control('minimalio_options_blog_all', function (control) {
				var visibility = function () {
					if ('no' === setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio posts per page if paginatin is enabled.
		wp.customize('minimalio_settings_portfolio_behaviour', function (setting) {
			wp.customize.control('minimalio_options_portfolio_pagination', function (control) {
				var visibility = function () {
					if ('single' !== setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio posts per page if paginatin is enabled.
		wp.customize('minimalio_settings_portfolio_behaviour', function (setting) {
			wp.customize.control('minimalio_options_portfolio_posts_per_page', function (control) {
				var visibility = function () {
					if ('single' !== setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio posts per page if paginatin is enabled.
		wp.customize('minimalio_settings_portfolio_pagination', function (setting) {
			wp.customize.control('minimalio_options_portfolio_posts_per_page', function (control) {
				var visibility = function () {
					if ('no' === setting.get()) {
						control.container.slideUp(180);
					} else {
						control.container.slideDown(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio single postif is enabled.
		wp.customize('minimalio_settings_portfolio_view', function (setting) {
			wp.customize.control('minimalio_options_sidebar_position_portfolio', function (control) {
				var visibility = function () {
					if ('post' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};
				visibility();
				setting.bind(visibility);
			});

		});

		// Hide blog posts per page when 'no' or 'pagination' is selected (uses WordPress default)
		wp.customize('minimalio_settings_blog_pagination', function (setting) {
			wp.customize.control('minimalio_options_blog_posts_per_page', function (control) {
				var visibility = function () {
					var value = setting.get();
					// Only show if not 'no' and not 'pagination'
					if ('no' !== value && 'pagination' !== value) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show copyright settings when enabled
		wp.customize('minimalio_settings_enable_copyright_section', function (setting) {
			wp.customize.control('minimalio_options_footer_menu', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show copyright settings when enabled
		wp.customize('minimalio_settings_enable_copyright_section', function (setting) {
			wp.customize.control('minimalio_options_footer_logo', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show copyright settings when enabled
		wp.customize('minimalio_settings_enable_copyright_section', function (setting) {
			wp.customize.control('minimalio_options_footer_menu', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show copyright settings when enabled
		wp.customize('minimalio_settings_enable_copyright_section', function (setting) {
			wp.customize.control('minimalio_options_footer_copyright', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show copyright settings when enabled
		wp.customize('minimalio_settings_enable_copyright_section', function (setting) {
			wp.customize.control('minimalio_options_developed', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});


		// Only show image aspect ratio if grid is enabled for portfolio
		wp.customize('minimalio_settings_portfolio_type', function (setting) {
			wp.customize.control('minimalio_options_post_card_image_aspect_ratio', function (control) {
				var visibility = function () {
					if ('grid' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show portfolio hover color if enabled
		wp.customize('minimalio_settings_portfolio_hover_option', function (setting) {
			wp.customize.control('minimalio_options_portfolio_hover_color', function (control) {
				var visibility = function () {
					if ('color' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show image aspect ratio if grid is enabled for portfolio
		wp.customize('minimalio_settings_blog_type', function (setting) {
			wp.customize.control('minimalio_options_blog_post_card_image_aspect_ratio', function (control) {
				var visibility = function () {
					if ('grid' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_single_post_title', function (setting) {
			wp.customize.control('minimalio_options_single_post_title_size', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_post_title_align', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});


		wp.customize('minimalio_settings_single_portfolio_title', function (setting) {
			wp.customize.control('minimalio_options_single_portfolio_title_size', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_portfolio_title_align', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_single_post_latest_posts', function (setting) {
			wp.customize.control('minimalio_options_single_post_latest_posts_title', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);

					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_post_latest_posts_number', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_single_post_latest_posts_title', function (setting) {
			wp.customize.control('minimalio_options_single_post_latest_posts_title_label', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);

					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_post_latest_posts_title_size', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_options_single_post_latest_posts_title_align', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_single_portfolio_latest', function (setting) {
			wp.customize.control('minimalio_options_single_portfolio_latest_portfolio_title', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_portfolio_latest_portfolio_number', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		wp.customize('minimalio_settings_single_portfolio_latest_portfolio_title', function (setting) {
			wp.customize.control('minimalio_options_single_portfolio_latest_portfolio_title_label', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);

					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
			wp.customize.control('minimalio_options_single_portfolio_latest_portfolio_title_size', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_options_single_portfolio_latest_portfolio_title_align', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		/* REMOVE Header -> Menu orientation option from the Customizer
	// Only show menu orientation on left menu.
		wp.customize( 'minimalio_settings_header_variation', function( setting ) {
	  wp.customize.control( 'minimalio_options_header_left_menu', function( control ) {
		var visibility = function() {
		  if ( 'left' === setting.get() ) {
			control.container.slideDown( 180 );
		  } else {
			control.container.slideUp( 180 );
		  }
		};

		visibility();
		setting.bind( visibility );
			});
	});
	*/

		// Only show container width
		wp.customize('minimalio_settings_container_type', function (setting) {
			wp.customize.control('minimalio__options_container_width', function (control) {
				var visibility = function () {
					if ('container' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Only show lightbox caption styling options when captions are enabled
		wp.customize('minimalio_lightbox_show_captions_settings', function (setting) {
			wp.customize.control('minimalio_lightbox_captions_bg_color_control', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_lightbox_captions_font_color_control', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});

			wp.customize.control('minimalio_lightbox_captions_font_size_control', function (control) {
				var visibility = function () {
					if ('yes' === setting.get()) {
						control.container.slideDown(180);
					} else {
						control.container.slideUp(180);
					}
				};

				visibility();
				setting.bind(visibility);
			});
		});

		// Get Google Font weights and styles based on the font family.
		wp.customize('minimalio_typography_settings_google_font', function (setting) {
			wp.customize.control('minimalio_typography_options_google_font', function (control) {
				control.setting.bind(function (to) {
					var webSafeFonts = ['Arial', 'Verdana', 'Tahoma', 'Times+New+Roman',
						'Georgia', 'Garamond', 'Courier+New', 'Brush+Script+MT'
					];
					// on select get the selected font family
					var fontFamily = control['elements'][0]['_value'];
					// build the url for calling Google font api
					const url = "https://www.googleapis.com/webfonts/v1/webfonts?family=" + fontFamily + "&key=AIzaSyDJE7BEyz0eCDG9S822vr1R8-YvuIaK9Ro";
					var fontWeight = wp.customize.control('minimalio_typography_options_google_font_weight').container.find('select');
					var fontStyle = wp.customize.control('minimalio_typography_options_google_font_style').container.find('select');
					var fontWeightOptions = '<option value="400" selected disabled hidden>Select an Option</option>';
					var fontStyleOptions = '<option value="normal" selected disabled hidden>Select an Option</option>';
					var defaultWeights = '<option value="400" selected disabled hidden>Select an Option</option><option value="400">400</option>';
					var webSafeWeights = '<option value="400" selected disabled hidden>Select an Option</option><option value="100">100</option> <option value="400">400</option> <option value="700">700</option> <option value="900">900</option>';
					var defaultStyle = '<option value="normal" selected disabled hidden>Select an Option</option><option value="normal">Regular</option>';
					var webSafeStyle = '<option value="normal" selected disabled hidden>Select an Option</option><option value="normal">Regular</option> <option value="italic">Italic</option>';
					var option = '';
					// if fontFamily is a web safe font, return default weights and styles

					if (webSafeFonts.includes(fontFamily)) {
						fontWeight.empty();
						fontStyle.empty();
						fontWeight.append(webSafeWeights);
						fontStyle.append(webSafeStyle);
						return false;
					} else {
						//fetch the google font
						fetch(url)
							.then(
								(response) => {
									if (!response.ok) {
										fontWeight.empty();
										fontStyle.empty();
										fontWeight.append(defaultWeights);
										fontStyle.append(defaultStyle);
										return false;
									}
									return response.json(); //text()., .blob(), etc.
								})
							.then(
								data => {
									var variants = data['items'][0]['variants'];
									variants.map((variant) => {
										if (!isNaN(variant)) {
											fontWeightOptions += '<option value="' + variant + '">' + variant + '</option>';
										} else if (variant === "regular") {
											fontWeightOptions += '<option value="none" selected disabled hidden>Select an Option</option><option value="400"> 400 </option>';
											fontStyleOptions += '<option value="none" selected disabled hidden>Select an Option</option><option value= "normal"> Regular </option>';
										} else {
											// remove numbers from the variant to get the style (regular, italic)
											var variantNoDigits = variant.replace(/[0-9]/g, '');
											// check if we already added the styles to the fontStyleOptions
											if (!fontStyleOptions.includes(variantNoDigits)) {
												option = '<option value="' + variantNoDigits + '">' + variantNoDigits.charAt(0).toUpperCase() + variantNoDigits.slice(1) + '</option>';
												fontStyleOptions += option;
											}
										}
									});
									fontWeightOptions = fontWeightOptions.replace('<option', '<option selected');
									fontStyleOptions = fontStyleOptions.replace('<option', '<option selected');

									if (fontWeightOptions !== '') {
										fontWeight.empty();
										fontWeight.append(fontWeightOptions);
									} else {
										fontWeight.empty();
										fontWeight.append(defaultWeights);
									};
									if (fontStyleOptions !== '') {
										fontStyle.empty();
										fontStyle.append(fontStyleOptions);
									} else {
										fontStyle.empty();
										fontStyle.append(defaultStyle);
									};
								});
					}
				});
			});
		});
	});
})(jQuery);

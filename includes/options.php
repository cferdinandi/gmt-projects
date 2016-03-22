<?php

/**
 * Theme Options v1.1.0
 * Adjust theme settings from the admin dashboard.
 * Find and replace `YourTheme` with your own namepspacing.
 *
 * Created by Michael Fields.
 * https://gist.github.com/mfields/4678999
 *
 * Forked by Chris Ferdinandi
 * http://gomakethings.com
 *
 * Free to use under the MIT License.
 * http://gomakethings.com/mit/
 */


	/**
	 * Theme Options Fields
	 * Each option field requires its own uniquely named function. Select options and radio buttons also require an additional uniquely named function with an array of option choices.
	 */

	function projects_settings_field_page_slug() {
		$options = projects_get_theme_options();
		?>
		<input type="text" name="projects_theme_options[page_slug]" id="page_slug" value="<?php echo esc_attr( $options['page_slug'] ); ?>" />
		<label class="description" for="page_slug"><?php _e( 'The events page slug', 'projects' ); ?></label>
		<?php
	}

	function projects_settings_field_page_title() {
		$options = projects_get_theme_options();
		?>
		<input type="text" name="projects_theme_options[page_title]" id="page_title" value="<?php echo esc_attr( $options['page_title'] ); ?>" />
		<label class="description" for="page_title"><?php _e( 'The events archive page title', 'projects' ); ?></label>
		<?php
	}

	function projects_settings_field_page_text() {
		$options = projects_get_theme_options();
		?>
		<textarea class="large-text" name="projects_theme_options[page_text]" id="page_text" cols="50" rows="10"><?php esc_textarea( projects_get_jetpack_markdown( $options, 'page_text' ) ); ?></textarea>
		<label class="description" for="page_text"><?php _e( 'The events archive page text', 'projects' ); ?></label>
		<?php
	}



	/**
	 * Theme Option Defaults & Sanitization
	 * Each option field requires a default value under projects_get_theme_options(), and an if statement under projects_theme_options_validate();
	 */

	// Get the current options from the database.
	// If none are specified, use these defaults.
	function projects_get_theme_options() {
		$saved = (array) get_option( 'projects_theme_options' );
		$defaults = array(
			'page_slug' => 'projects',
			'page_title' => 'Projects',
			'page_text' => '',
			'page_text_markdown' => '',
		);

		$defaults = apply_filters( 'projects_default_theme_options', $defaults );

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return $options;
	}

	// Sanitize and validate updated theme options
	function projects_theme_options_validate( $input ) {
		$output = array();

		if ( isset( $input['page_slug'] ) && ! empty( $input['page_slug'] ) )
			$output['page_slug'] = wp_filter_nohtml_kses( $input['page_slug'] );

		if ( isset( $input['page_title'] ) && ! empty( $input['page_title'] ) )
			$output['page_title'] = wp_filter_nohtml_kses( $input['page_title'] );

		if ( isset( $input['page_text'] ) && ! empty( $input['page_text'] ) ) {
			$output['page_text'] = wp_filter_post_kses( projects_process_jetpack_markdown( $input['page_text'] ) );
			$output['page_text_markdown'] = wp_filter_post_kses( $input['page_text'] );
		}

		return apply_filters( 'projects_theme_options_validate', $output, $input );
	}



	/**
	 * Theme Options Menu
	 * Each option field requires its own add_settings_field function.
	 */

	// Create theme options menu
	// The content that's rendered on the menu page.
	function projects_theme_options_render_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Projects Options', 'projects' ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'projects_options' );
					do_settings_sections( 'projects_options' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Register the theme options page and its fields
	function projects_theme_options_init() {

		// Register a setting and its sanitization callback
		// register_setting( $option_group, $option_name, $sanitize_callback );
		// $option_group - A settings group name.
		// $option_name - The name of an option to sanitize and save.
		// $sanitize_callback - A callback function that sanitizes the option's value.
		register_setting( 'projects_options', 'projects_theme_options', 'projects_theme_options_validate' );


		// Register our settings field group
		// add_settings_section( $id, $title, $callback, $page );
		// $id - Unique identifier for the settings section
		// $title - Section title
		// $callback - // Section callback (we don't want anything)
		// $page - // Menu slug, used to uniquely identify the page. See projects_theme_options_add_page().
		add_settings_section( 'general', null,  '__return_false', 'projects_options' );


		// Register our individual settings fields
		// add_settings_field( $id, $title, $callback, $page, $section );
		// $id - Unique identifier for the field.
		// $title - Setting field title.
		// $callback - Function that creates the field (from the Theme Option Fields section).
		// $page - The menu page on which to display this field.
		// $section - The section of the settings page in which to show the field.
		add_settings_field( 'page_slug', __( 'Page Slug', 'projects' ), 'projects_settings_field_page_slug', 'projects_options', 'general' );
		add_settings_field( 'page_title', __( 'Page Title', 'projects' ), 'projects_settings_field_page_title', 'projects_options', 'general' );
		add_settings_field( 'page_text', __( 'Page Text', 'projects' ), 'projects_settings_field_page_text', 'projects_options', 'general' );
		add_settings_field( 'heading_future', __( 'Future Heading', 'projects' ), 'projects_settings_field_heading_future', 'projects_options', 'general' );
		add_settings_field( 'heading_past', __( 'Past Heading', 'projects' ), 'projects_settings_field_heading_past', 'projects_options', 'general' );
	}
	add_action( 'admin_init', 'projects_theme_options_init' );

	// Add the theme options page to the admin menu
	// Use add_theme_page() to add under Appearance tab (default).
	// Use add_menu_page() to add as it's own tab.
	// Use add_submenu_page() to add to another tab.
	function projects_theme_options_add_page() {

		// add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		// $page_title - Name of page
		// $menu_title - Label in menu
		// $capability - Capability required
		// $menu_slug - Used to uniquely identify the page
		// $function - Function that renders the options page
		// $theme_page = add_theme_page( __( 'Events Options', 'projects' ), __( 'Options', 'projects' ), 'edit_theme_options', 'projects_options', 'projects_theme_options_render_page' );

		// $theme_page = add_menu_page( __( 'Theme Options', 'projects' ), __( 'Theme Options', 'projects' ), 'edit_theme_options', 'projects_options', 'projects_theme_options_render_page' );
		$theme_page = add_submenu_page( 'edit.php?post_type=gmt-projects', __( 'Events Options', 'projects' ), __( 'Options', 'projects' ), 'edit_theme_options', 'projects_options', 'projects_theme_options_render_page' );
	}
	add_action( 'admin_menu', 'projects_theme_options_add_page' );



	// Restrict access to the theme options page to admins
	function projects_option_page_capability( $capability ) {
		return 'edit_theme_options';
	}
	add_filter( 'option_page_capability_projects_options', 'projects_option_page_capability' );

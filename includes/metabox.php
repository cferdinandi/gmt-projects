<?php

	/**
	 * Create the metabox
	 */
	function projects_create_metabox() {
		add_meta_box( 'projects_metabox', 'Project Details', 'projects_render_metabox', 'gmt-projects', 'normal', 'default');
	}
	add_action( 'add_meta_boxes', 'projects_create_metabox' );



	/**
	 * Create the metabox default values
	 */
	function projects_metabox_defaults() {
		return array(
			'url' => '',
			'url_label' => '',
		);
	}



	/**
	 * Render the metabox
	 */
	function projects_render_metabox() {

		// Variables
		global $post;
		$saved = get_post_meta( $post->ID, 'project_details', true );
		$defaults = projects_metabox_defaults();
		$details = wp_parse_args( $saved, $defaults );

		?>

			<fieldset>

				<div>
					<label for="projects_url"><?php _e( 'URL', 'projects' ); ?></label>
					<input type="url" class="large-text" id="projects_url" name="projects[url]" value="<?php echo esc_attr( $details['url'] ); ?>">
				</div>
				<br>

				<div>
					<label for="projects_url_label"><?php _e( 'URL Label', 'projects' ); ?></label>
					<input type="text" class="large-text" id="projects_url_label" name="projects[url_label]" value="<?php echo esc_attr( $details['url_label'] ); ?>">
				</div>
				<br>

			</fieldset>

		<?php

		// Security field
		wp_nonce_field( 'projects_form_metabox_nonce', 'projects_form_metabox_process' );

	}



	/**
	 * Save the metabox
	 * @param  Number $post_id The post ID
	 * @param  Array  $post    The post data
	 */
	function projects_save_metabox( $post_id, $post ) {

		if ( !isset( $_POST['projects_form_metabox_process'] ) ) return;

		// Verify data came from edit screen
		if ( !wp_verify_nonce( $_POST['projects_form_metabox_process'], 'projects_form_metabox_nonce' ) ) {
			return $post->ID;
		}

		// Verify user has permission to edit post
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}

		// Check that events details are being passed along
		if ( !isset( $_POST['projects'] ) ) {
			return $post->ID;
		}

		// Sanitize all data
		$sanitized = array();
		foreach ( $_POST['projects'] as $key => $detail ) {
			if ( $key === 'description' ) {
				$sanitized['description'] = wp_filter_post_kses( projects_process_jetpack_markdown( $detail ) );
				$sanitized['description_markdown'] = wp_filter_post_kses( $detail );
				continue;
			}
			$sanitized[$key] = wp_filter_post_kses( $detail );
		}

		// Update data in database
		update_post_meta( $post->ID, 'project_details', $sanitized );

	}
	add_action('save_post', 'projects_save_metabox', 1, 2);



	/**
	 * Save events data to revisions
	 * @param  Number $post_id The post ID
	 */
	function projects_save_revisions( $post_id ) {

		// Check if it's a revision
		$parent_id = wp_is_post_revision( $post_id );

		// If is revision
		if ( $parent_id ) {

			// Get the data
			$parent = get_post( $parent_id );
			$details = get_post_meta( $parent->ID, 'project_details', true );

			// If data exists, add to revision
			if ( !empty( $details ) && is_array( $details ) ) {
				$defaults = projects_metabox_defaults();
				foreach ( $defaults as $key => $value ) {
					if ( array_key_exists( $key, $details ) ) {
						add_metadata( 'post', $post_id, 'project_details_' . $key, $details[$key] );
					}
				}
			}

		}

	}
	add_action( 'save_post', 'projects_save_revisions' );



	/**
	 * Restore events data with post revisions
	 * @param  Number $post_id     The post ID
	 * @param  Number $revision_id The revision ID
	 */
	function projects_restore_revisions( $post_id, $revision_id ) {

		// Variables
		$post = get_post( $post_id );
		$revision = get_post( $revision_id );
		$defaults = projects_metabox_defaults();
		$details = array();

		// Update content
		foreach ( $defaults as $key => $value ) {
			$detail_revision = get_metadata( 'post', $revision->ID, 'project_details_' . $key, true );
			if ( isset( $detail_revision ) ) {
				$details[$key] = $detail_revision;
			}
		}
		update_post_meta( $post_id, 'project_details', $project_details );

	}
	add_action( 'wp_restore_post_revision', 'projects_restore_revisions', 10, 2 );



	/**
	 * Get the data to display on the revisions page
	 * @param  Array $fields The fields
	 * @return Array The fields
	 */
	function projects_get_revisions_fields( $fields ) {
		$defaults = projects_metabox_defaults();
		foreach ( $defaults as $key => $value ) {
			$fields['project_details_' . $key] = ucfirst( $key );
		}
		return $fields;
	}
	add_filter( '_wp_post_revision_fields', 'projects_get_revisions_fields' );



	/**
	 * Display the data on the revisions page
	 * @param  String|Array $value The field value
	 * @param  Array        $field The field
	 */
	function projects_display_revisions_fields( $value, $field ) {
		global $revision;
		return get_metadata( 'post', $revision->ID, $field, true );
	}
	add_filter( '_wp_post_revision_field_my_meta', 'projects_display_revisions_fields', 10, 2 );
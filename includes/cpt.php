<?php

	/**
	 * Add custom post type
	 */
	function projects_add_custom_post_type() {

		$options = projects_get_theme_options();
		$labels = array(
			'name'               => _x( 'Projects', 'post type general name', 'projects' ),
			'singular_name'      => _x( 'Project', 'post type singular name', 'projects' ),
			'add_new'            => _x( 'Add New', 'projects', 'projects' ),
			'add_new_item'       => __( 'Add New Project', 'projects' ),
			'edit_item'          => __( 'Edit Project', 'projects' ),
			'new_item'           => __( 'New Project', 'projects' ),
			'all_items'          => __( 'All Projects', 'projects' ),
			'view_item'          => __( 'View Project', 'projects' ),
			'search_items'       => __( 'Search Projects', 'projects' ),
			'not_found'          => __( 'No projects found', 'projects' ),
			'not_found_in_trash' => __( 'No projectss found in the Trash', 'projects' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Projects', 'projects' ),
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our projects and project-specific data',
			'public'        => true,
			// 'menu_position' => 5,
			'menu_icon'     => 'dashicons-clipboard',
			'supports'      => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
				'revisions',
				'wpcom-markdown',
			),
			'has_archive'   => true,
			'rewrite' => array(
				'slug' => $options['page_slug'],
			),
			'map_meta_cap'  => true,
		);
		register_post_type( 'gmt-projects', $args );
	}
	add_action( 'init', 'projects_add_custom_post_type' );
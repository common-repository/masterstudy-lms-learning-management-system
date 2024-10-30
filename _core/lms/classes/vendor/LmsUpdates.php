<?php

namespace stmLms\Classes\Vendor;

abstract class LmsUpdates {
	private static $updates = array(
		'2.6.0'  => array( 'lms_chat_columns' ), // LMS Chat system update with fixes
		'2.6.4'  => array( 'lms_page_routes' ), // LMS Chat system update with fixes
		'2.6.7'  => array( 'lms_admin_notification_transient' ), // LMS Rate Us Admin Notification
		'2.9.22' => array( 'lms_add_lesson_video_sources' ), // Added lesson video sources
		'3.0.0'  => array(
			'lms_create_curriculum_tables', // Curriculum Refactoring
			'lms_migrate_course_data', // Curriculum & Course Files Refactoring
			'lms_migrate_lesson_data', // Lesson Files Refactoring & Changing Slide Lesson Type
			'lms_instructor_role_add_capability', // Add "list_users" capability to Instructor User Role
		),
		'3.0.6'  => array(
			'lms_remove_url_from_widgets', // Clear stm links from widgets
			'lms_remove_stm_links_from_content', // Clear stm links from pages content
			'lms_remove_copyright_url', // Clear stm links from copyright
		),
		'3.0.19' => array( 'lms_udemy_course_additional_info' ), // Add additional info for Udemy Courses
		'3.0.25' => array( 'lms_generate_required_pages' ), // Generate LMS Pages for old Users
		'3.1.0'  => array( 'lms_reset_page_routes' ), // Reset Page Routes for new Course Player
		'3.1.3'  => array(
			'lms_remove_url_from_widgets', // Clear stm links from widgets
			'lms_remove_copyright_url', // Clear stm links from copyright
		),
		'3.1.5'  => array( 'lms_remove_url_from_widgets' ),
		'3.1.7'  => array( 'lms_composite_index_to_user_lessons' ),
		'3.1.19' => array( 'lms_move_student_assignment_attachments' ), // Move Student Assignment Attachments to Post Meta
		'3.2.2'  => array( 'lms_replaced_auth_settings_values' ), // Profile Authorization settings changed
		'3.3.0'  => array( 'lms_set_default_certificate' ), // Find default certificate and set its ID to wp_option
		'3.3.1'  => array( 'lms_composite_index_to_user_courses_table' ), // Added indexing to user courses table
		'3.3.9'  => array( 'lms_reset_page_routes', 'lms_update_db_tables' ), // Reset Page Routes and Update Database Tables
		'3.3.14' => array( 'lms_reset_page_routes' ), // Reset Page Routes for new Manage Students
		'3.3.16' => array( 'lms_replaced_single_course_style' ), // Find course style option and rename it
		'3.3.35' => array( 'lms_flush_rewrite_rules' ), // Update permalinks for thank you page endpoints
	);

	/**
	 * Init LMS Updates
	 */
	public static function init() {
		if ( version_compare( get_option( 'stm_lms_version', '1.0.0' ), STM_LMS_VERSION, '<' ) ) {
			self::update_version();
		}
	}

	/**
	 * Get All Updates
	 * @return array
	 */
	public static function get_updates() {
		return self::$updates;
	}

	/**
	 * Check If Needs Updates
	 * @return bool
	 */
	public static function needs_to_update() {
		$current_db_version = get_option( 'stm_lms_db_version', '1.0.0' );
		$update_versions    = array_keys( self::get_updates() );
		usort( $update_versions, 'version_compare' );

		return ! empty( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
	}

	/**
	 * Run Needed Updates
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_to_update() ) {
			$current_db_version = get_option( 'stm_lms_db_version', '1.0.0' );
			$updates            = self::get_updates();

			foreach ( $updates as $version => $callback_arr ) {
				if ( version_compare( $current_db_version, $version, '<' ) ) {
					foreach ( $callback_arr as $callback ) {
						call_user_func( array( '\\stmLms\\Classes\\Vendor\\LmsUpdateCallbacks', $callback ) );
					}
				}
			}
		}

		update_option( 'stm_lms_db_version', STM_LMS_DB_VERSION, true );
	}

	/**
	 * Update Plugin Version
	 */
	public static function update_version() {
		update_option( 'stm_lms_version', STM_LMS_VERSION, true );
		self::maybe_update_db_version();
	}
}

<?php

namespace WPFormsConversationalForms\Admin;

/**
 * Conversational Forms overview functionality.
 *
 * @since 1.0.0
 */
class Overview {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		\add_filter( 'wpforms_overview_row_actions', array( $this, 'add_row_view_action' ), 10, 2 );
	}

	/**
	 * Add view row action if Conversational mode is activated.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $row_actions Table row actions.
	 * @param \WP_Post $form        Form object.
	 *
	 * @return array
	 */
	public function add_row_view_action( $row_actions, $form ) {

		$form_data = ! empty( $form->post_content ) ? \wpforms_decode( $form->post_content ) : array();

		if ( empty( $form_data['settings']['conversational_forms_enable'] ) ) {
			return $row_actions;
		}

		$action = array(
			'view' => \sprintf(
				'<a href="%s" title="%s" target="_blank">%s</a>',
				\esc_url( \home_url( $form->post_name ) ),
				\esc_html__( 'View Conversational Form', 'wpforms-conversational-forms' ),
				\esc_html__( 'Conversational Form Preview', 'wpforms-conversational-forms' )
			),
		);

		return \wpforms_array_insert( $row_actions, $action, 'preview_' );
	}
}

<?php

/**
 * Login Form element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_login_form_element( $elements ) {
	$elements[] = array(
		'id'          => 'login_form',
		'category'    => 'content',
		'title'       => __( 'Login Form', 'pagex' ),
		'description' => __( 'Login form.', 'pagex' ),
		'type'        => 'dynamic',
		'callback'    => 'pagex_login_form',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'    => 'label_username',
						'title' => __( 'Username Label', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-6',
					),
					array(
						'id'    => 'label_password',
						'title' => __( 'Password Label', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-6',
					),
					array(
						'id'    => 'button_text',
						'title' => __( 'Button Text', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-6',
					),
				)
			),
			array(
				'title'  => __( 'Submit Button', 'pagex' ),
				'params' => array(
					array(
						'id'       => 'qw',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-6',
						'action'   => 'css',
						'selector' => '[el] .pagex-login-form-submit {text-align: [val]}',
						'options'  => array(
							''       => __( 'Left', 'pagex' ),
							'center' => __( 'Center', 'pagex' ),
							'right'  => __( 'Right', 'pagex' ),
						),
					),
					array(
						'id'       => 'fw',
						'type'     => 'checkbox',
						'title'    => __( 'Full Width', 'pagex' ),
						'label'    => __( 'Make Button Full Width', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] [type="submit"] {width: 100%}',
					),
					array(
						'id'       => 'a',
						'type'     => 'button_style',
						'selector' => '[type="submit"]',
					),
				),
			),
			array(
				'title'  => __( 'Style', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Form', 'pagex' ),
					),
					array(
						'id'       => 'ty',
						'type'     => 'typography',
						'selector' => '',
					),
					array(
						'id'       => 'gh',
						'title'    => __( 'Border Width', 'pagex' ),
						'class'    => 'col-3',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {border-width: [val]}',
					),
					array(
						'id'       => 'hj',
						'title'    => __( 'Border Radius', 'pagex' ),
						'class'    => 'col-3',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {border-radius: [val]}',
					),
					array(
						'id'       => 'jk',
						'title'    => __( 'Padding', 'pagex' ),
						'class'    => 'col-3',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {padding: [val]}',
					),
					array(
						'id'       => 'xc',
						'title'    => __( 'Row Gap', 'pagex' ),
						'class'    => 'col-3',
						'type'     => 'number',
						'action'   => 'css',
						'selector' => '[el] .form-group {margin-bottom: [val]px}',
					),
					array(
						'type' => 'clear'
					),
					array(
						'id'       => 'yu',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] {color: [val]}',
					),
					array(
						'type' => 'clear'
					),
					array(
						'id'       => 'ui',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .form-control {background: [val]}',
					),
					array(
						'id'       => 'io',
						'title'    => __( 'Background on Focus', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .form-control:focus {background: [val]}',
					),
					array(
						'id'       => 'op',
						'title'    => __( 'Border', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .form-control {border-color: [val]}',
					),
					array(
						'id'       => 'pa',
						'title'    => __( 'Border on Focus', 'pagex' ),
						'class'    => 'col-8',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .form-control:focus {border-color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Label', 'pagex' ),
					),
					array(
						'id'       => 'we',
						'type'     => 'checkbox',
						'label'    => __( 'Hide Labels', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-form-label {display: none}',
					),
					array(
						'id'       => 'er',
						'type'     => 'typography',
						'selector' => '.pagex-form-label',
					),
					array(
						'id'       => 'rt',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-label {color: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Login Form shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_login_form( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'button_text'    => __( 'Log In', 'pagex' ),
		'label_username' => __( 'Username or Email Address', 'pagex' ),
		'label_password' => __( 'Password', 'pagex' ),
		'label_remember' => __( 'Remember Me', 'pagex' ),
	) );

	$html = '';

	$html .= '<form class="pagex-login-form">';

	$html .= '<div class="form-group pagex-login-form-username">';
	$html .= '<label class="pagex-form-label">' . $data['label_username'] . '</label>';
	$html .= '<input type="text" name="log" class="form-control" placeholder="' . esc_attr( $data['label_username'] ) . '" required>';
	$html .= '</div>';

	$html .= '<div class="form-group pagex-login-form-password">';
	$html .= '<label class="pagex-form-label">' . $data['label_password'] . '</label>';
	$html .= '<input type="password" name="pwd" class="form-control" placeholder="' . esc_attr( $data['label_password'] ) . '" required>';
	$html .= '</div>';

	$html .= '<div class="form-group pagex-login-form-submit">';
	$html .= '<button type="submit" class="btn pagex-login-form-submit-button"><span>' . $data['button_text'] . '</span></button>';
	$html .= '</div>';

	$html .= '<div class="form-group pagex-login-form-message"></div>';

	$html .= '</form>';

	if ( is_user_logged_in() && ! Pagex::is_frontend_builder_frame_active() ) {
		$current_user = wp_get_current_user();

		$html = '<div class="pagex-login-form-logged-in-message">' .
		        sprintf( __( 'You are logged in as %1$s.', 'pagex' ), $current_user->display_name ) .
		        ' <a href="' . wp_logout_url(home_url()) . '">' . __( 'Logout.', 'pagex' ) . '</a></div>';
	}

	return $html;
}


/**
 * Proceed the form data via ajax
 */
function pagex_form_ajax_send_login_form() {

	$info['user_login']    = isset( $_POST['log'] ) ? $_POST['log'] : '';
	$info['user_password'] = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
	$info['remember']      = true;

	$user_signon = wp_signon( $info );

	if ( is_wp_error( $user_signon ) ) {
		wp_send_json_error( array(
			'message' => $user_signon->get_error_message()
		) );
	} else {
		wp_send_json_success();
	}
}

add_action( 'wp_ajax_pagex_form_ajax_send_login_form', 'pagex_form_ajax_send_login_form' );
add_action( 'wp_ajax_nopriv_pagex_form_ajax_send_login_form', 'pagex_form_ajax_send_login_form' );
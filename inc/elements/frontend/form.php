<?php

/**
 * Form element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_form_element( $elements ) {
	$elements[] = array(
		'id'          => 'form',
		'category'    => 'content',
		'title'       => __( 'Form', 'pagex' ),
		'description' => __( 'Contact or MailChimp form.', 'pagex' ),
		'type'        => 'dynamic',
		'info'        => 'https://github.com/yumecommerce/pagex/wiki/Form',
		'callback'    => 'pagex_form',
		'options'     => array(
			array(
				'params' => array(
					array(
						'id'     => 'items',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'id'      => 'type',
								'title'   => __( 'Type', 'pagex' ),
								'type'    => 'select',
								'class'   => 'col-6',
								'options' => array(
									'text'     => __( 'Text', 'pagex' ),
									'email'    => __( 'Email', 'pagex' ),
									'number'   => __( 'Number', 'pagex' ),
									'textarea' => __( 'Textarea', 'pagex' ),
									'radio'    => __( 'Radio', 'pagex' ),
									'checkbox' => __( 'Checkbox', 'pagex' ),
									'select'   => __( 'Select', 'pagex' ),
									'html'     => 'HTML',
								),
							),
							array(
								'id'         => 'width',
								'title'      => __( 'Wrapper Width', 'pagex' ),
								'type'       => 'select',
								'responsive' => true,
								'class'      => 'col-6',
								'options'    => array(
									'xs' => array(
										''         => __( 'Default', 'pagex' ),
										'col'      => __( 'Basic', 'pagex' ),
										'col-auto' => __( 'Auto', 'pagex' ),
										'col-1'    => '1',
										'col-2'    => '2',
										'col-3'    => '3',
										'col-4'    => '4',
										'col-5'    => '5',
										'col-6'    => '6',
										'col-7'    => '7',
										'col-8'    => '8',
										'col-9'    => '9',
										'col-10'   => '10',
										'col-11'   => '11',
										'col-12'   => '12',
									),
									'sm' => array(
										''            => __( 'Inherit', 'pagex' ),
										'col-sm-auto' => __( 'Auto', 'pagex' ),
										'col-sm'      => __( 'Basic', 'pagex' ),
										'col-sm-1'    => '1',
										'col-sm-2'    => '2',
										'col-sm-3'    => '3',
										'col-sm-4'    => '4',
										'col-sm-5'    => '5',
										'col-sm-6'    => '6',
										'col-sm-7'    => '7',
										'col-sm-8'    => '8',
										'col-sm-9'    => '9',
										'col-sm-10'   => '10',
										'col-sm-11'   => '11',
										'col-sm-12'   => '12',
									),
									'md' => array(
										''            => __( 'Inherit', 'pagex' ),
										'col-md-auto' => __( 'Auto', 'pagex' ),
										'col-md'      => __( 'Basic', 'pagex' ),
										'col-md-1'    => '1',
										'col-md-2'    => '2',
										'col-md-3'    => '3',
										'col-md-4'    => '4',
										'col-md-5'    => '5',
										'col-md-6'    => '6',
										'col-md-7'    => '7',
										'col-md-8'    => '8',
										'col-md-9'    => '9',
										'col-md-10'   => '10',
										'col-md-11'   => '11',
										'col-md-12'   => '12',
									),
									'lg' => array(
										''            => __( 'Inherit', 'pagex' ),
										'col-lg-auto' => __( 'Auto', 'pagex' ),
										'col-lg'      => __( 'Basic', 'pagex' ),
										'col-lg-1'    => '1',
										'col-lg-2'    => '2',
										'col-lg-3'    => '3',
										'col-lg-4'    => '4',
										'col-lg-5'    => '5',
										'col-lg-6'    => '6',
										'col-lg-7'    => '7',
										'col-lg-8'    => '8',
										'col-lg-9'    => '9',
										'col-lg-10'   => '10',
										'col-lg-11'   => '11',
										'col-lg-12'   => '12',
									),
									'xl' => array(
										''            => __( 'Inherit', 'pagex' ),
										'col-xl-auto' => __( 'Auto', 'pagex' ),
										'col-xl'      => __( 'Basic', 'pagex' ),
										'col-xl-1'    => '1',
										'col-xl-2'    => '2',
										'col-xl-3'    => '3',
										'col-xl-4'    => '4',
										'col-xl-5'    => '5',
										'col-xl-6'    => '6',
										'col-xl-7'    => '7',
										'col-xl-8'    => '8',
										'col-xl-9'    => '9',
										'col-xl-10'   => '10',
										'col-xl-11'   => '11',
										'col-xl-12'   => '12',
									),
								)
							),
							array(
								'id'        => 'label',
								'title'     => __( 'Label', 'pagex' ),
								'type'      => 'text',
								'class'     => 'col pagex-repeater-value',
								'condition' => array(
									'type' => array(
										'text',
										'email',
										'number',
										'textarea',
										'radio',
										'checkbox',
										'select'
									)
								),
							),
							array(
								'id'        => 'placeholder',
								'title'     => __( 'Placeholder', 'pagex' ),
								'type'      => 'text',
								'class'     => 'col',
								'condition' => array(
									'type' => array( 'text', 'email', 'number', 'textarea', 'select' )
								),
							),
							array(
								'id'        => 'rows',
								'title'     => __( 'Rows', 'pagex' ),
								'type'      => 'number',
								'class'     => 'col',
								'condition' => array(
									'type' => array( 'textarea' )
								),
							),
							array(
								'type' => 'clear',
							),
							array(
								'id'        => 'html',
								'title'     => 'HTML',
								'class'     => 'col-12',
								'type'      => 'textarea',
								'condition' => array(
									'type' => array( 'html' )
								),
							),
							array(
								'id'          => 'options',
								'title'       => __( 'Options', 'pagex' ),
								'description' => __( 'Enter each option in a separate line.', 'pagex' ),
								'type'        => 'textarea',
								'condition'   => array(
									'type' => array( 'radio', 'checkbox', 'select' )
								),
							),
							array(
								'id'        => 'inline',
								'label'     => __( 'Make Inline List', 'pagex' ),
								'type'      => 'checkbox',
								'class'     => 'col-auto',
								'condition' => array(
									'type' => array( 'radio', 'checkbox' )
								),
							),
							array(
								'id'        => 'required',
								'label'     => __( 'Make Field Required', 'pagex' ),
								'type'      => 'checkbox',
								'value'     => 'required',
								'class'     => 'col-auto',
								'condition' => array(
									'type' => array(
										'text',
										'email',
										'number',
										'textarea',
										'radio',
										'checkbox',
										'select'
									)
								),
							),
							array(
								'type' => 'clear',
							),

						),
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
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {border-width: [val]}',
					),
					array(
						'id'       => 'hj',
						'title'    => __( 'Border Radius', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {border-radius: [val]}',
					),
					array(
						'id'         => 'jk',
						'title'      => __( 'Padding', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'text',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .form-control {padding: [val]}',
					),
					array(
						'id'         => 'xc',
						'title'      => __( 'Row Gap', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .form-group {margin-bottom: [val]px}',
					),
					array(
						'id'         => 'cv',
						'title'      => __( 'Column Gap', 'pagex' ),
						'class'      => 'col-4',
						'type'       => 'number',
						'responsive' => true,
						'action'     => 'css',
						'selector'   => '[el] .pagex-form-item {padding: 0 [val]px} [el] .form-row {margin-right: -[val]px; margin-left: -[val]px}',
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
						'id'       => 'aq',
						'title'    => __( 'Box Shadow', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control {box-shadow: [val]}',
					),
					array(
						'id'       => 'ed',
						'title'    => __( 'Box Shadow on Focus', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'text',
						'action'   => 'css',
						'selector' => '[el] .form-control:focus {box-shadow: [val]}',
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

					array(
						'type'  => 'heading',
						'title' => __( 'Radio and Checkbox inputs', 'pagex' ),
					),
					array(
						'id'       => 'zx',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-check-wrapper {color: [val]}',
					),
					array(
						'id'       => 'sd',
						'title'    => __( 'Background', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] [type=checkbox], [el] [type=radio] {background: [val]}',
					),
					array(
						'id'       => 'df',
						'title'    => __( 'Checked Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] [type=checkbox]:checked, [el] [type=radio]:checked {border-color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'HTML Fields', 'pagex' ),
					),
					array(
						'id'       => 'sw',
						'type'     => 'typography',
						'selector' => '.pagex-form-type-html',
					),
					array(
						'id'       => 'de',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-type-html {color: [val]}',
					),

					array(
						'type'  => 'heading',
						'title' => __( 'Response Messages', 'pagex' ),
					),
					array(
						'id'       => 'qe',
						'type'     => 'typography',
						'selector' => '.pagex-form-response-message',
					),
					array(
						'id'       => 'wr',
						'title'    => __( 'Color', 'pagex' ),
						'class'    => 'col-4',
						'type'     => 'color',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-response-message {color: [val]}',
					),
				),
			),
			array(
				'title'  => __( 'Submit Button', 'pagex' ),
				'params' => array(
					array(
						'id'    => 'button_text',
						'title' => __( 'Text', 'pagex' ),
						'type'  => 'text',
						'class' => 'col-4',
					),
					array(
						'id'         => 'submit_width',
						'title'      => __( 'Wrapper Width', 'pagex' ),
						'type'       => 'select',
						'responsive' => true,
						'class'      => 'col-4',
						'options'    => array(
							'xs' => array(
								''         => __( 'Default', 'pagex' ),
								'col'      => __( 'Basic', 'pagex' ),
								'col-auto' => __( 'Auto', 'pagex' ),
								'col-1'    => '1',
								'col-2'    => '2',
								'col-3'    => '3',
								'col-4'    => '4',
								'col-5'    => '5',
								'col-6'    => '6',
								'col-7'    => '7',
								'col-8'    => '8',
								'col-9'    => '9',
								'col-10'   => '10',
								'col-11'   => '11',
								'col-12'   => '12',
							),
							'sm' => array(
								''            => __( 'Inherit', 'pagex' ),
								'col-sm-auto' => __( 'Auto', 'pagex' ),
								'col-sm'      => __( 'Basic', 'pagex' ),
								'col-sm-1'    => '1',
								'col-sm-2'    => '2',
								'col-sm-3'    => '3',
								'col-sm-4'    => '4',
								'col-sm-5'    => '5',
								'col-sm-6'    => '6',
								'col-sm-7'    => '7',
								'col-sm-8'    => '8',
								'col-sm-9'    => '9',
								'col-sm-10'   => '10',
								'col-sm-11'   => '11',
								'col-sm-12'   => '12',
							),
							'md' => array(
								''            => __( 'Inherit', 'pagex' ),
								'col-md-auto' => __( 'Auto', 'pagex' ),
								'col-md'      => __( 'Basic', 'pagex' ),
								'col-md-1'    => '1',
								'col-md-2'    => '2',
								'col-md-3'    => '3',
								'col-md-4'    => '4',
								'col-md-5'    => '5',
								'col-md-6'    => '6',
								'col-md-7'    => '7',
								'col-md-8'    => '8',
								'col-md-9'    => '9',
								'col-md-10'   => '10',
								'col-md-11'   => '11',
								'col-md-12'   => '12',
							),
							'lg' => array(
								''            => __( 'Inherit', 'pagex' ),
								'col-lg-auto' => __( 'Auto', 'pagex' ),
								'col-lg'      => __( 'Basic', 'pagex' ),
								'col-lg-1'    => '1',
								'col-lg-2'    => '2',
								'col-lg-3'    => '3',
								'col-lg-4'    => '4',
								'col-lg-5'    => '5',
								'col-lg-6'    => '6',
								'col-lg-7'    => '7',
								'col-lg-8'    => '8',
								'col-lg-9'    => '9',
								'col-lg-10'   => '10',
								'col-lg-11'   => '11',
								'col-lg-12'   => '12',
							),
							'xl' => array(
								''            => __( 'Inherit', 'pagex' ),
								'col-xl-auto' => __( 'Auto', 'pagex' ),
								'col-xl'      => __( 'Basic', 'pagex' ),
								'col-xl-1'    => '1',
								'col-xl-2'    => '2',
								'col-xl-3'    => '3',
								'col-xl-4'    => '4',
								'col-xl-5'    => '5',
								'col-xl-6'    => '6',
								'col-xl-7'    => '7',
								'col-xl-8'    => '8',
								'col-xl-9'    => '9',
								'col-xl-10'   => '10',
								'col-xl-11'   => '11',
								'col-xl-12'   => '12',
							),
						)
					),
					array(
						'id'       => 'qw',
						'title'    => __( 'Align', 'pagex' ),
						'type'     => 'select',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-type-submit {justify-content: [val]}',
						'options'  => array(
							''         => __( 'Left', 'pagex' ),
							'center'   => __( 'Center', 'pagex' ),
							'flex-end' => __( 'Right', 'pagex' ),

						),
					),
					array(
						'id'       => 'fw',
						'type'     => 'checkbox',
						'title'    => __( 'Full Width', 'pagex' ),
						'label'    => __( 'Make Button Full Width', 'pagex' ),
						'action'   => 'css',
						'selector' => '[el] .pagex-form-submit-button {width: 100%}',
					),
					array(
						'id'       => 'a',
						'type'     => 'button_style',
						'selector' => '.pagex-form-submit-button',
					),
					array(
						'id'       => 'am',
						'type'     => 'text',
						'title'    => __( 'Margin', 'pagex' ),
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-submit-button {margin: [val]}',
					),
					array(
						'id'       => 'xz',
						'type'     => 'text',
						'title'    => __( 'Min. Height', 'pagex' ),
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-form-submit-button {min-height: [val]}',
					),
					array(
						'id'       => 'icon',
						'label'    => __( 'Icon', 'pagex' ),
						'type'     => 'icon',
						'selector' => '.pagex-form-submit-icon',
					),
				),
			),
			array(
				'title'  => __( 'Action', 'pagex' ),
				'params' => array(
					array(
						'id'          => 'action[type]',
						'title'       => __( 'Action', 'pagex' ),
						'description' => __( 'Action which will be performed after the form is submitted.', 'pagex' ),
						'type'        => 'select',
						'options'     => array(
							'email'     => 'Email',
							'mailchimp' => 'MailChimp',
						),
					),
					array(
						'id'          => 'action[email_to]',
						'title'       => __( 'Recipient', 'pagex' ),
						'description' => __( 'If recipient is not specified the admin email will be used instead.', 'pagex' ),
						'type'        => 'text',
						'class'       => 'col-6',
						'condition'   => array(
							'action[type]' => array( 'email' )
						),
					),
					array(
						'id'        => 'action[email_subject]',
						'title'     => __( 'Email Subject', 'pagex' ),
						'type'      => 'text',
						'class'     => 'col-6',
						'condition' => array(
							'action[type]' => array( 'email' )
						),
					),
					array(
						'id'          => 'action[email_meta]',
						'title'       => __( 'Meta Data', 'pagex' ),
						'label'       => __( 'Send meta data', 'pagex' ),
						'description' => __( 'By default meta data includes page url, user agent and remote ip', 'pagex' ),
						'type'        => 'checkbox',
						'condition'   => array(
							'action[type]' => array( 'email' )
						),
					),
					array(
						'id'          => 'action[mailchimp_list_id]',
						'title'       => __( 'List ID', 'pagex' ),
						'description' => '<a target="_blank" href="https://admin.mailchimp.com/lists">' . __( 'Open Lists.', 'pagex' ) . '</a>' . __( 'Then click on the list title, then in list menu Settings > List name and defaults.', 'pagex' ),
						'type'        => 'text',
						'condition'   => array(
							'action[type]' => array( 'mailchimp' )
						),
					),

					array(
						'type'        => 'heading',
						'title'       => __( 'Custom Messages', 'pagex' ),
						'description' => __( 'If custom messages are not provided the default ones will be used.', 'pagex' ),
					),
					array(
						'id'    => 'success_msg',
						'title' => __( 'Success Message', 'pagex' ),
						'type'  => 'text',
					),
				),
			),
		),
	);

	return $elements;
}

/**
 * Form shortcode
 *
 * @param $atts
 *
 * @return string
 */
function pagex_form( $atts ) {
	$data = Pagex::get_dynamic_data( $atts );

	$data = wp_parse_args( $data, array(
		'button_text' => __( 'Send', 'pagex' ),
		'success_msg' => __( 'The form was sent successfully.', 'pagex' ),
		'items'       => array(),
		'action'      => array(
			'type' => 'email',
		),
	) );

	$html = $messages = '';

	if ( empty( $data['items'] ) ) {
		return $html;
	}

	foreach ( $data['items'] as $k => $v ) {
		$item = $attr = '';

		$item .= $v['label'] ? '<label class="pagex-form-label">' . esc_attr( $v['label'] ) . '</label>' : '';

		$attr .= $v['placeholder'] ? 'placeholder="' . esc_attr( $v['placeholder'] ) . '" ' : '';
		$attr .= 'class="form-control" ';
		$attr .= isset( $v['required'] ) ? $v['required'] : '';

		$options = $v['options'] ? array_filter( preg_split( "/\\r\\n|\\r|\\n/", $v['options'] ) ) : array();

		switch ( $v['type'] ) {
			case 'text':
				$item .= '<input type="text" ' . $attr . '>';
				break;
			case 'email':
				$item .= '<input type="email" ' . $attr . '>';
				break;
			case 'number':
				$item .= '<input type="number" ' . $attr . '>';
				break;
			case 'textarea':
				if ( isset( $v['rows'] ) && $v['rows'] ) {
					$attr .= ' rows="' . $v['rows'] . '"';
					$attr .= ' rows="' . $v['rows'] . '"';
				}

				$item .= '<textarea ' . $attr . '></textarea>';
				break;
			case 'radio':
				$name = uniqid();
				if ( isset( $v['inline'] ) ) {
					$item .= '<div class="pagex-form-check-wrapper pagex-form-inline">';
				} else {
					$item .= '<div class="pagex-form-check-wrapper pagex-form-column">';
				}
				foreach ( $options as $option ) {
					$required = isset( $v['required'] ) ? $v['required'] : '';

					$item .= '<label><input type="radio" class="pagex-form-check" name="' . $name . '" value="' . esc_attr( $option ) . '" ' . $required . '><span>' . $option . '</span></label>';
				}
				$item .= '</div>';
				break;
			case 'checkbox':
				if ( isset( $v['inline'] ) ) {
					$item .= '<div class="pagex-form-check-wrapper pagex-form-inline">';
				} else {
					$item .= '<div class="pagex-form-check-wrapper pagex-form-column">';
				}
				foreach ( $options as $option ) {
					$required = isset( $v['required'] ) ? $v['required'] : '';

					$item .= '<label><input type="checkbox" class="pagex-form-check" name="' . uniqid() . '" value="' . esc_attr( $option ) . '" ' . $required . '><span>' . $option . '</span></label>';
				}
				$item .= '</div>';

				break;
			case 'select':
				$item .= '<select ' . $attr . '>';
				if ( $v['placeholder'] ) {
					$item .= '<option value="">' . esc_attr( $v['placeholder'] ) . '</option>';
				}
				foreach ( $options as $option ) {
					$option = esc_attr( $option );
					$item   .= '<option value="' . $option . '">' . $option . '</option>';
				}
				$item .= '</select>';
				break;
			case 'html':
				$item .= nl2br( $v['html'] );
				break;
		}

		if ( $item ) {
			$width = implode( ' ', array_filter( $v['width'] ) );
			$width = $width ? $width : 'col-12';

			$html .= '<div class="pagex-form-item form-group ' . $width . ' pagex-form-type-' . $v['type'] . '">' . $item . '</div>';
		}
	}

	// submit button
	$submit_width = isset( $data['submit_width'] ) ? implode( ' ', array_filter( $data['submit_width'] ) ) : array();
	$submit_width = $submit_width ? $submit_width : 'col-12';
	$icon         = isset( $data['icon'] ) ? '<div class="pagex-form-submit-icon">' . pagex_generate_icon( 'icon', $data ) . '</div>' : '';

	$html .= '<div class="pagex-form-item form-group ' . $submit_width . ' pagex-form-type-submit"><button type="submit" class="btn pagex-form-submit-button">' . $icon . '<span>' . $data['button_text'] . '</span></button></div>';

	// form messages success or error
	$messages .= '<div class="pagex-form-response-message">' . $data['success_msg'] . '</div>';

	// store action and params to hidden input which will be parsed after submitting
	$action = '<input type="hidden" name="pagex-action" value="' . urlencode( json_encode( array_filter( $data['action'] ) ) ) . '">';

	return '<form class="pagex-form"><div class="form-row">' . $html . '</div>' . $messages . $action . '</form>';
}


/**
 * Proceed the form data via ajax
 */
function pagex_form_ajax_send_form() {
	$form        = $_POST['form'];
	$form_action = json_decode( urldecode( $_POST['form_action'] ), true );

	$error_msg = __( 'An error occurred.', 'pagex' );

	switch ( $form_action['type'] ) {
		case 'email':
			$admin_email = get_option( 'admin_email' );

			/* translators: %s: Site title. */
			$subject = sprintf( __( 'New message from "%s"', 'pagex' ), get_bloginfo( 'name' ) );

			$to      = isset( $form_action['email_to'] ) && $form_action['email_to'] ? $form_action['email_to'] : $admin_email;
			$subject = isset( $form_action['email_subject'] ) && $form_action['email_subject'] ? $form_action['email_subject'] : $subject;

			$headers = sprintf( 'From: %s <%s>' . "\r\n", get_bloginfo( 'name' ), $admin_email );
			$headers .= sprintf( 'Reply-To: %s' . "\r\n", 'noreplay@' . site_url() );
			$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

			$content = '';
			foreach ( $form as $v ) {
				$content .= $v['label'] . ': ' . implode( ', ', $v['value'] ) . '<br>';
			}

			if ( isset( $form_action['email_meta'] ) ) {
				$content .= '<br><br>---------<br><br>';
				$content .= __( 'Page URL', 'pagex' ) . ': ' . $_POST['url'] . '<br>';
				$content .= __( 'User Agent', 'pagex' ) . ': ' . $_SERVER['HTTP_USER_AGENT'] . '<br>';
				$content .= __( 'Remote IP', 'pagex' ) . ': ' . $_SERVER['REMOTE_ADDR'] . '<br>';
			}

			$email_sent = wp_mail( $to, $subject, $content, $headers );

			if ( $email_sent ) {
				wp_send_json_success();
			} else {
				wp_send_json_error( array(
					'message' => $error_msg
				) );
			}

			break;
		case 'mailchimp':

			$settings = Pagex::get_settings();

			$api_key = isset( $settings['apis']['mailchimp_key'] ) ? $settings['apis']['mailchimp_key'] : '';

			if ( ! $api_key ) {
				wp_send_json_error( array(
					'message' => __( 'API Key is required.', 'pagex' ) . ' <a href="' . admin_url( 'admin.php?page=pagex#tab_apis' ) . '" target="_blank">' . __( 'Set it now?', 'pagex' ) . '</a>'
				) );
			}

			$list_id = isset( $form_action['mailchimp_list_id'] ) ? $form_action['mailchimp_list_id'] : '';

			if ( ! $list_id ) {
				wp_send_json_error( array(
					'message' => __( 'List ID is required. You can add it in element settings.', 'pagex' )
				) );
			}

			$data = array();

			foreach ( $form as $v ) {
				$val = $v['value'][0];

				if ( strpos( $val, '@' ) !== false ) {
					$data['email'] = $val;
				} else {
					$data['name'] = $val;
				}
			}

			if ( ! isset( $data['email'] ) ) {
				wp_send_json_error( array(
					'message' => __( 'Email is not valid.', 'pagex' )
				) );
			}

			$body = array(
				'apikey'        => $api_key,
				'email_address' => $data['email'],
				'status'        => 'subscribed',
			);

			if ( isset( $data['name'] ) ) {
				$body['merge_fields'] = array(
					'FNAME' => $data['name']
				);
			}

			$request = 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5( strtolower( $data['email'] ) );

			$request_args = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
					'Content-Type'  => 'application/json; charset=utf-8',
				),
				'method'  => 'PUT',
				'body'    => wp_json_encode( $body ),
			);

			$response = wp_remote_post( $request, $request_args );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( array(
					'message' => $error_msg
				) );
			} else {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( ! is_array( $body ) ) {
					wp_send_json_error( array(
						'message' => $error_msg
					) );
				} else {
					wp_send_json_success();
				}
			}
			break;
	}
}

add_action( 'wp_ajax_pagex_form_ajax_send_form', 'pagex_form_ajax_send_form' );
add_action( 'wp_ajax_nopriv_pagex_form_ajax_send_form', 'pagex_form_ajax_send_form' );
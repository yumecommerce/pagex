<?php

/**
 * Column element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_column_element( $elements ) {
	$elements[] = array(
		'id'       => 'column',
		'title'    => __( 'Column', 'pagex' ),
		'template' => '<div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div>',
		'options'  => array(
			array(
				'params' => array(
					array(
						'id'          => 'column_width',
						'title'       => __( 'Column Width', 'pagex' ),
						'description' => __( 'Auto classes size columns based on the width of their content. Basic classes make sibling columns automatically resize around.', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'class',
						'class'       => 'col-6',
						'selector'    => '[el]',
						'options'     => array(
							'xs' => array(
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
						'id'          => 'column_order',
						'title'       => __( 'Order', 'pagex' ),
						'description' => __( 'Visual order of the column.', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'class',
						'class'       => 'col-6',
						'selector'    => '[el]',
						'options'     => array(
							''              => __( 'Default', 'pagex' ),
							'order[pref]1'  => '1',
							'order[pref]2'  => '2',
							'order[pref]3'  => '3',
							'order[pref]4'  => '4',
							'order[pref]5'  => '5',
							'order[pref]6'  => '6',
							'order[pref]7'  => '7',
							'order[pref]8'  => '8',
							'order[pref]9'  => '9',
							'order[pref]10' => '10',
							'order[pref]11' => '11',
							'order[pref]12' => '12',
						)
					),
					array(
						'id'          => 'column_self_align',
						'title'       => __( 'Self Alignment', 'pagex' ),
						'description' => __( 'Alignment on the vertical axis.', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'class',
						'class'       => 'col-6',
						'selector'    => '[el]',
						'options'     => array(
							''                         => __( 'Default', 'pagex' ),
							'align-self[pref]start'    => __( 'Top', 'pagex' ),
							'align-self[pref]end'      => __( 'Bottom', 'pagex' ),
							'align-self[pref]center'   => __( 'Center', 'pagex' ),
							'align-self[pref]baseline' => __( 'Baseline', 'pagex' ),
							'align-self[pref]stretch'  => __( 'Stretch', 'pagex' ),
						)
					),
					array(
						'id'          => 'column_offset',
						'title'       => __( 'Offsetting', 'pagex' ),
						'description' => __( 'Force sibling columns away from one another. Note this option controls margin classes utilities.', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'class',
						'class'       => 'col-6',
						'selector'    => '[el]',
						'options'     => array(
							''             => __( 'Default', 'pagex' ),
							'm[pref]auto'  => __( 'Auto', 'pagex' ),
							'mt[pref]auto' => __( 'Top', 'pagex' ),
							'mb[pref]auto' => __( 'Bottom', 'pagex' ),
							'my[pref]auto' => __( 'Top and Bottom', 'pagex' ),
							'ml[pref]auto' => __( 'Left', 'pagex' ),
							'mr[pref]auto' => __( 'Right', 'pagex' ),
							'mx[pref]auto' => __( 'Left and Right', 'pagex' ),
						)
					),
					array(
						'id'          => 'column_sticky',
						'title'       => __( 'Sticky Position', 'pagex' ),
						'description' => __( 'Fixed the column inside a row while scrolling.', 'pagex' ),
						'type'        => 'select',
						'responsive'  => true,
						'action'      => 'class',
						'class'       => 'col-6',
						'selector'    => '[el]',
						'options'     => array(
							''                                => __( 'Default', 'pagex' ),
							'pagex-sticky-column[pref]top'    => __( 'Top', 'pagex' ),
							'pagex-sticky-column[pref]center' => __( 'Center', 'pagex' ),
						)
					),
				)
			)
		)
	);

	return $elements;
}
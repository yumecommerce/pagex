<?php

/**
 * Section element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_section_element( $elements ) {
	$elements[] = array(
		'id'       => 'section',
		'title'    => __( 'Section', 'pagex' ),
		'template' => '<div class="section" data-id="<%= pagex.genID() %>" data-type="section"><div class="container" data-id="<%= pagex.genID() %>" data-type="container"><div class="row" data-id="<%= pagex.genID() %>" data-type="row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div></div></div>',
		'options'  => array(
			array(
				'params' => array(
					array(
						'id'       => 'pagex_section_pos',
						'title'    => __( 'Position', 'pagex' ),
						'type'     => 'select',
						'action'   => 'class',
						'selector' => '[el]',
						'options'  => array(
							''                                          => __( 'Default', 'pagex' ),
							'pagex-section-position-absolute'           => __( 'Absolute', 'pagex' ),
							'pagex-section-position-absolute-home-page' => __( 'Absolute only for Homepage', 'pagex' ),
							'pagex-section-position-fixed'              => __( 'Fixed', 'pagex' ),
							//'pagex-section-position-fixed-home-page'    => __( 'Fixed only for Homepage', 'pagex' ),
						),
					),
					array(
						'id'          => 'pagex_section_tr_bg',
						'label'       => __( 'Transparent Background', 'pagex' ),
						'description' => __( 'Make background of the section transparent when section is not fixed', 'pagex' ),
						'type'        => 'checkbox',
						'action'      => 'class',
						'selector'    => '[el]',
						'value'       => 'pagex-position-fixed-transparent',
						'condition'   => array(
							'pagex_section_pos' => array( 'pagex-section-position-fixed' )
						)
					),
					array(
						'id'          => 'pagex_section_tr_cl',
						'title'       => __( 'Color Before Fixed', 'pagex' ),
						'description' => __( 'Primary color for elements of none fixed section. Note! This option covers only basic style. Use .pagex-section-fixed class in your custom CSS to trigger style when section fixed.', 'pagex' ),
						'type'        => 'color',
						'action'      => 'css',
						'selector'    => '
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="heading"] .pagex-heading,
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="nav_menu"] .pagex-nav-menu-mobile-trigger, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="nav_menu"] .pagex-nav-menu > li > a, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="button"] > .element-wrap > .pagex-button-wrapper .pagex-button, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="button"] > .element-wrap > .pagex-button-wrapper .pagex-button-icon, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="search_form"] .pagex-search-form-icon .pagex-icon, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="menu_cart"] .pagex-menu-cart .pagex-icon, 
						[el].pagex-section-position-fixed:not(.pagex-section-fixed) > .container > .row > [data-type="column"] > [data-type="icon"] .pagex-icon
						 {color: [val] !important }',
						'condition'   => array(
							'pagex_section_pos' => array( 'pagex-section-position-fixed' )
						)
					),
					array(
						'id'      => 'pagex_shape_top',
						'title'   => __( 'Top Divider', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							''                             => __( 'None', 'pagex' ),
							'pagex-clouds'                 => __( 'Clouds', 'pagex' ),
							'pagex-flipped-clouds'         => __( 'Clouds (flipped)', 'pagex' ),
							'pagex-clouds-opacity'         => __( 'Clouds Opacity', 'pagex' ),
							'pagex-flipped-clouds-opacity' => __( 'Clouds Opacity (flipped)', 'pagex' ),
							'pagex-curve'                  => __( 'Curve', 'pagex' ),
							'pagex-flipped-curve'          => __( 'Curve (flipped)', 'pagex' ),
							'pagex-tilt'                   => __( 'Tilt', 'pagex' ),
							'pagex-tilt-opacity'           => __( 'Tilt Opacity', 'pagex' ),
							'pagex-triangle'               => __( 'Triangle', 'pagex' ),
							'pagex-flipped-triangle'       => __( 'Triangle (flipped)', 'pagex' ),
							'pagex-wave'                   => __( 'Wave', 'pagex' ),
							'pagex-wave-opacity'           => __( 'Wave Opacity', 'pagex' ),
							'pagex-wave-small-border'      => __( 'Wave Border', 'pagex' ),
						),
					),
					array(
						'id'         => 'pagex_shape_top_w',
						'title'      => __( 'Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .pagex-shape-top svg {width: calc(100% + [val])}',
						'condition'  => array(
							'!pagex_shape_top' => array( '' )
						)
					),
					array(
						'id'         => 'pagex_shape_top_h',
						'title'      => __( 'Height', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .pagex-shape-top svg {height: [val]}',
						'condition'  => array(
							'!pagex_shape_top' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_top_c',
						'title'     => __( 'Color', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] > .pagex-shape-top {color: [val]}',
						'condition' => array(
							'!pagex_shape_top' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_top_above',
						'label'     => __( 'Above the content', 'pagex' ),
						'type'      => 'checkbox',
						'action'    => 'class',
						'class'     => 'col-auto',
						'selector'  => '.pagex-shape-top',
						'scope'     => true,
						'value'     => 'pagex-shape-above',
						'condition' => array(
							'!pagex_shape_top' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_top_flip',
						'label'     => __( 'Flip', 'pagex' ),
						'type'      => 'checkbox',
						'action'    => 'class',
						'class'     => 'col-auto',
						'selector'  => '.pagex-shape-top',
						'scope'     => true,
						'value'     => 'pagex-shape-flip',
						'condition' => array(
							'!pagex_shape_top' => array( '' )
						)
					),

					array(
						'id'      => 'pagex_shape_bottom',
						'title'   => __( 'Bottom Divider', 'pagex' ),
						'type'    => 'select',
						'options' => array(
							''                             => __( 'None', 'pagex' ),
							'pagex-clouds'                 => __( 'Clouds', 'pagex' ),
							'pagex-flipped-clouds'         => __( 'Clouds (flipped)', 'pagex' ),
							'pagex-clouds-opacity'         => __( 'Clouds Opacity', 'pagex' ),
							'pagex-flipped-clouds-opacity' => __( 'Clouds Opacity (flipped)', 'pagex' ),
							'pagex-curve'                  => __( 'Curve', 'pagex' ),
							'pagex-flipped-curve'          => __( 'Curve (flipped)', 'pagex' ),
							'pagex-tilt'                   => __( 'Tilt', 'pagex' ),
							'pagex-tilt-opacity'           => __( 'Tilt Opacity', 'pagex' ),
							'pagex-triangle'               => __( 'Triangle', 'pagex' ),
							'pagex-flipped-triangle'       => __( 'Triangle (flipped)', 'pagex' ),
							'pagex-wave'                   => __( 'Wave', 'pagex' ),
							'pagex-wave-opacity'           => __( 'Wave Opacity', 'pagex' ),
							'pagex-wave-small-border'      => __( 'Wave Border', 'pagex' ),
						),
					),
					array(
						'id'         => 'pagex_shape_bottom_w',
						'title'      => __( 'Width', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .pagex-shape-bottom svg {width: calc(100% + [val])}',
						'condition'  => array(
							'!pagex_shape_bottom' => array( '' )
						)
					),
					array(
						'id'         => 'pagex_shape_bottom_h',
						'title'      => __( 'Height', 'pagex' ),
						'type'       => 'text',
						'action'     => 'css',
						'responsive' => true,
						'class'      => 'col-4',
						'selector'   => '[el] > .pagex-shape-bottom svg {height: [val]}',
						'condition'  => array(
							'!pagex_shape_bottom' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_bottom_c',
						'title'     => __( 'Color', 'pagex' ),
						'type'      => 'color',
						'action'    => 'css',
						'class'     => 'col-4',
						'selector'  => '[el] > .pagex-shape-bottom {color: [val]}',
						'condition' => array(
							'!pagex_shape_bottom' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_bottom_above',
						'label'     => __( 'Above the content', 'pagex' ),
						'type'      => 'checkbox',
						'action'    => 'class',
						'class'     => 'col-auto',
						'selector'  => '.pagex-shape-bottom',
						'scope'     => true,
						'value'     => 'pagex-shape-above',
						'condition' => array(
							'!pagex_shape_bottom' => array( '' )
						)
					),
					array(
						'id'        => 'pagex_shape_bottom_flip',
						'label'     => __( 'Flip', 'pagex' ),
						'type'      => 'checkbox',
						'action'    => 'class',
						'class'     => 'col-auto',
						'selector'  => '.pagex-shape-bottom',
						'scope'     => true,
						'value'     => 'pagex-shape-flip',
						'condition' => array(
							'!pagex_shape_bottom' => array( '' )
						)
					),
				)
			)
		)
	);

	return $elements;
}
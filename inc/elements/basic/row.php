<?php

/**
 * Row and Inner Row elements
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_row_element( $elements ) {
	// params are the same for row and inner row elements
	$row_params = array(
		array(
			'id'          => 'justify_content',
			'title'       => __( 'Justify Content', 'pagex' ),
			'description' => __( 'Horizontal alignment for columns.', 'pagex' ),
			'type'        => 'select',
			'responsive'  => true,
			'class'       => 'col-6',
			'action'      => 'class',
			'selector'    => '[el]',
			'options'     => array(
				''                             => __( 'Default', 'pagex' ),
				'justify-content[pref]start'   => __( 'Left', 'pagex' ),
				'justify-content[pref]center'  => __( 'Center', 'pagex' ),
				'justify-content[pref]end'     => __( 'Right', 'pagex' ),
				'justify-content[pref]between' => __( 'Between', 'pagex' ),
				'justify-content[pref]around'  => __( 'Around', 'pagex' ),
			),
		),
		array(
			'id'          => 'align_items',
			'title'       => __( 'Align Items', 'pagex' ),
			'description' => __( 'Vertical alignment for columns.', 'pagex' ),
			'type'        => 'select',
			'class'       => 'col-6',
			'responsive'  => true,
			'action'      => 'class',
			'selector'    => '[el]',
			'options'     => array(
				''                        => __( 'Default', 'pagex' ),
				'align-items[pref]start'  => __( 'Top', 'pagex' ),
				'align-items[pref]center' => __( 'Center', 'pagex' ),
				'align-items[pref]end'    => __( 'Bottom', 'pagex' ),
			),
		),
		array(
			'id'          => 'gutters',
			'label'       => __( 'No Gutters', 'pagex' ),
			'description' => __( 'Remove the negative margins from the row and the horizontal padding from all immediate children columns.', 'pagex' ),
			'type'        => 'checkbox',
			'action'      => 'class',
			'selector'    => '[el]',
			'value'       => 'no-gutters'
		),
	);

	$elements[] = array(
		'id'       => 'row',
		'title'    => __( 'Row', 'pagex' ),
		'template' => '<div class="row" data-id="<%= pagex.genID() %>" data-type="row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div>',
		'options'  => array(
			array(
				'params' => $row_params
			)
		)
	);

	$elements[] = array(
		'id'          => 'inner-row',
		'title'       => __( 'Inner Row', 'pagex' ),
		'description' => __( 'Child row element', 'pagex' ),
		'category'    => 'content',
		'template'    => '<div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div>',
		'options'     => array(
			array(
				'params' => $row_params
			)
		)
	);

	return $elements;
}
<?php

/**
 * Container element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_container_element( $elements ) {
	$elements[] = array(
		'id'       => 'container',
		'title'    => __( 'Container', 'pagex' ),
		'template' => '<div class="container" data-id="<%= pagex.genID() %>" data-type="container"><div class="row" data-id="<%= pagex.genID() %>" data-type="row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div></div>',
		'options'  => array(
			array(
				'params' => array(
					array(
						'id'          => 'container_width',
						'title'       => __( 'Container Width', 'pagex' ),
						'label'       => __( 'Fluid container', 'pagex' ),
						'description' => __( 'Fluid container spanning the entire width of the viewport.', 'pagex' ),
						'type'        => 'checkbox',
						'action'      => 'class',
						'selector'    => '[el]',
						'value'       => 'container-fluid'
					)
				)
			)
		)
	);

	return $elements;
}
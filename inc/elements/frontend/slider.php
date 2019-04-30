<?php
/**
 * Slider element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_slider_element( $elements ) {
	$template = '<div class="pagex-slider">
		<div class="swiper-container">
		    <div class="swiper-wrapper">
		    	<% if (_.isUndefined(data.slides)) { %>
		    	<div class="swiper-slide pagex-inner-row-holder"><div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div></div>
		    	<% } else { %>
		        <% data.slides.forEach( function(slide, index) { %>
		            <div class="swiper-slide pagex-inner-row-holder"><% if (slide.content && slide.content.length) { print(slide.content) } else { %><div class="row" data-id="<%= pagex.genID() %>" data-type="inner-row"><div class="col" data-id="<%= pagex.genID() %>" data-type="column"></div></div><% } %></div>
		        <% });} %>
		    </div>
		    <div class="swiper-pagination pagex-slider-pagination"></div>
		</div>
		
		<% var type = data.slider_nav_type ? "long-arrow" : "arrow"; %>
		
		<div class="swiper-button-prev pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-<%- type %>-left-icon" /></svg></div><div class="swiper-button-next pagex-slider-navigation"><svg class="pagex-icon"><use xlink:href="#pagex-<%- type %>-right-icon" /></svg></div>
	</div>';

	$elements[] = array(
		'id'          => 'slider',
		'category'    => 'content',
		'title'       => __( 'Slider', 'pagex' ),
		'description' => __( 'Can be setup as a slider or slideshow with custom content', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					// needs for initialization of slider data
					array(
						'id'      => 'layout',
						'type'    => 'select',
						'hidden'  => true,
						'options' => array(
							'pagex_slider',
						),
					),
					array(
						'id'     => 'slides',
						'title'  => __( 'Items', 'pagex' ),
						'type'   => 'repeater',
						'params' => array(
							array(
								'type'  => 'heading',
								'title' => __( 'Slide does not accept any parameters', 'pagex' ),
							),
							// needs for initialization of empty repeater item
							array(
								'id'      => 'empty',
								'type'    => 'select',
								'hidden'  => true,
								'options' => array(
									'1'
								)
							),
							array(
								'id'       => 'content',
								'type'     => 'textarea',
								'hidden'   => true,
								'action'   => 'content',
								'selector' => '.swiper-slide',
							),
						),
					),
				),
			),
			array(
				'title'  => __( 'Slider', 'pagex' ),
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Basic Slider Settings', 'pagex' ),
					),
					array(
						'type'  => 'slider',
					),
				)
			)
		)
	);

	return $elements;
}
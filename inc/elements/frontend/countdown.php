<?php

/**
 * Countdown element
 *
 * @param $elements
 *
 * @return array
 */
function pagex_register_countdown_element( $elements ) {
	$template = '<%
	data.days = data.days ? Number(data.days) : 0;
	data.hours = data.hours ? Number(data.hours) : 0;
	data.minutes = data.minutes ? Number(data.minutes) : 0;
	
	if (data.days === 0 && data.hours === 0 && data.minutes === 0) {
		data.days = 7;
	}
	
	var now = new Date();
	now.setDate(now.getDate() + data.days);
	now.setHours(now.getHours() + data.hours);
	now.setMinutes(now.getMinutes() + data.minutes);
	
 	%>
 	<div class="pagex-countdown d-flex" data-countdown="<%= now.getTime() %>">
	    <div class="pagex-countdown-days-wrapper">
	        <div class="pagex-countdown-days pagex-countdown-date"></div>
	        <div class="pagex-countdown-label pagex-lang-str">' . __( 'Days', 'pagex' ) . '</div>
		</div>
		
		<div class="pagex-countdown-sep"></div>
	    
		<div class="pagex-countdown-hours-wrapper">
			<div class="pagex-countdown-hours pagex-countdown-date"></div>
			<div class="pagex-countdown-label pagex-lang-str">' . __( 'Hours', 'pagex' ) . '</div>
		</div>
		
		<div class="pagex-countdown-sep"></div>
		
		<div class="pagex-countdown-minutes-wrapper">
			<div class="pagex-countdown-minutes pagex-countdown-date"></div>
			<div class="pagex-countdown-label pagex-lang-str">' . __( 'Minutes', 'pagex' ) . '</div>
		</div>
		
		<div class="pagex-countdown-sep"></div>
			
		<div class="pagex-countdown-seconds-wrapper">
			<div class="pagex-countdown-seconds pagex-countdown-date"></div>
			<div class="pagex-countdown-label pagex-lang-str">' . __( 'Seconds', 'pagex' ) . '</div>
		</div>
</div>';

	$elements[] = array(
		'id'          => 'countdown',
		'category'    => 'content',
		'title'       => __( 'Countdown', 'pagex' ),
		'description' => __( 'Counting the time remaining before a selected date', 'pagex' ),
		'type'        => 'static',
		'template'    => $template,
		'options'     => array(
			array(
				'params' => array(
					array(
						'type'  => 'heading',
						'title' => __( 'Countdown', 'pagex' ),
					),
					array(
						'id'    => 'days',
						'title' => __( 'Days', 'pagex' ),
						'class' => 'col-4',
						'type'  => 'number',
					),
					array(
						'id'    => 'hours',
						'title' => __( 'Hours', 'pagex' ),
						'class' => 'col-4',
						'type'  => 'number',
					),
					array(
						'id'    => 'minutes',
						'title' => __( 'Minutes', 'pagex' ),
						'class' => 'col-4',
						'type'  => 'number',
					),

					array(
						'id'       => 'hide_d',
						'label'    => __( 'Hide Days', 'pagex' ),
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
						'action'   => 'class',
						'selector' => '.pagex-countdown-days-wrapper',
					),
					array(
						'id'       => 'hide_h',
						'label'    => __( 'Hide hours', 'pagex' ),
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
						'action'   => 'class',
						'selector' => '.pagex-countdown-hours-wrapper',
					),
					array(
						'id'       => 'hide_m',
						'label'    => __( 'Hide Minutes', 'pagex' ),
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
						'action'   => 'class',
						'selector' => '.pagex-countdown-minutes-wrapper',
					),
					array(
						'id'       => 'hide_s',
						'label'    => __( 'Hide Seconds', 'pagex' ),
						'class'    => 'col-auto',
						'type'     => 'checkbox',
						'value'    => 'd-none',
						'action'   => 'class',
						'selector' => '.pagex-countdown-seconds-wrapper',
					),
					array(
						'type' => 'clear',
					),
					array(
						'id'         => 'alignment',
						'title'      => __( 'Alignment', 'pagex' ),
						'type'       => 'select',
						'class'      => 'col-4',
						'responsive' => true,
						'action'     => 'class',
						'selector'   => '.pagex-countdown',
						'options'    => array(
							''                            => __( 'Default', 'pagex' ),
							'justify-content[pref]start'  => __( 'Left', 'pagex' ),
							'justify-content[pref]center' => __( 'Center', 'pagex' ),
							'justify-content[pref]end'    => __( 'Right', 'pagex' ),
						)
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Date', 'pagex' ),
					),
					array(
						'id'       => 'date_font',
						'type'     => 'typography',
						'selector' => '.pagex-countdown-date',
					),
					array(
						'id'       => 'date_color',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-countdown-date {color: [val]}',
						'type'     => 'color',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Date Label', 'pagex' ),
					),
					array(
						'id'       => 'label_font',
						'type'     => 'typography',
						'selector' => '.pagex-countdown-label',
					),
					array(
						'id'       => 'label_color',
						'title'    => __( 'Color', 'pagex' ),
						'action'   => 'css',
						'class'    => 'col-4',
						'selector' => '[el] .pagex-countdown-label {color: [val]}',
						'type'     => 'color',
					),
					array(
						'type'  => 'heading',
						'title' => __( 'Separator', 'pagex' ),
					),
					array(
						'id'       => 'sep_margin',
						'title'    => __( 'Margin', 'pagex' ),
						'type'     => 'text',
						'class'    => 'col-4',
						'action'   => 'css',
						'selector' => '[el] .pagex-countdown-sep {margin: [val]}',
					),
				),
			),
		),
	);

	return $elements;
}
<?php

/* 
Plugin Name: Tournaments List Widget
Plugin URI: http://www.mxpdev.com/mxp_wp_tournament_widget
Description: A widget to show list of tournaments for the current year.
Version: 0.1
Author: Michael Pranivong
Author URI: mxpranivong@gmail.com
License: 
*/
// Creating the widget 
class MXPDev_Tournaments_Widget extends WP_Widget {

	function __construct() {
		
		$this->options = array(
			array(
				'name'	=> 'title',
				'label'	=> __( 'Title', 'mxpdev' ),
				'type'	=> 'text'
			),
			array(
				'name'	=> 'year',
				'label'	=> __( 'Year', 'mxpdev' ),
				'type'	=> 'text'
			),
			array(
				'name'	=> 'detail_level',
				'label'	=> __( 'Detail Level', 'mxpdev' ),
				'type'	=> 'radio',
				'values' => array('minimum'=>'Minimum','medium'=>'Medium','maximum'=>'Maximum')
			)
		);
		parent::__construct(
			// Base ID of your widget
			'MXPDev_Tournaments_Widget', 
		
			// Widget name will appear in UI
			__('Tournament List', 'mxpdev'), 
		
			// Widget description
			array( 'description' => __( 'A widget to show list of tournaments for the current year.', 'mxpdev' ), )
				 
		);
	}
	
	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		
		// This is where you run the code and display the output
		include_once('nukedb.php');
		$rs = mxpdev_get_tournaments($instance['year']);
		echo show_tournaments($rs, $instance['detail_level']); 
		
		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		
		$default['title']			= __( 'New title', 'mxpdev' );
		$default['year']			= date('Y');
		$default['detail_level']	= 'minimum';
		
		/*if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'MXPDev_Tournaments_Widget' );
		}*/
		
		$instance = wp_parse_args($instance, $default);
		
		// Widget admin form
		
		foreach ($this->options as $val) {
			$label = '<label for="'.$this->get_field_id($val['name']).'">'.$val['label'].'</label>';
			if ($val['type']=='text') {
				echo '<p>'.$label.'<br />';
				echo '<input class="widefat" id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="text" value="'.esc_attr($instance[$val['name']]).'" /></p>';
			} else if ($val['type']=='checkbox') {
				$checked = ($instance[$val['name']]) ? 'checked="checked"' : '';
				echo '<input id="'.$this->get_field_id($val['name']).'" name="'.$this->get_field_name($val['name']).'" type="checkbox" '.$checked.' /> '.$label.'<br />';
			} else if ($val['type']=='radio') {
				echo '<p>'.$label.'<br />';
				foreach($val['values'] as $key=>$name){
					$label = '<label for="'.$this->get_field_id($val['name'].'_'.$key).'">'.$name.'</label>';
					$checked = ($instance[$val['name']] == $key) ? 'checked="checked"' : '';
					echo '<input id="'.$this->get_field_id($val['name'].'_'.$key).'" name="'.$this->get_field_name($val['name']).'" type="radio" '.$checked.' value="'.$key.'" />'.$label.'&nbsp;';
				}
				echo '<br/><br/>';
			}
		}
		?>
		<!--
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'year' ); ?>"><?php _e( 'Year:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'year' ); ?>" name="<?php echo $this->get_field_name( 'year' ); ?>" type="text" value="<?php echo esc_attr( $instance['year'] ); ?>" />
		</p-->
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		//$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		//$instance['year'] = ( ! empty( $new_instance['year'] ) ) ? strip_tags( $new_instance['year'] ) : '';
		
		foreach ($this->options as $val) {
			if ($val['type']=='text') {
				$instance[$val['name']] = strip_tags($new_instance[$val['name']]);
			} else if ($val['type']=='checkbox') {
				$instance[$val['name']] = ($new_instance[$val['name']]=='on') ? true : false;
			} else if ($val['type']=='radio') {
				$instance[$val['name']] = $new_instance[$val['name']];
			}
				
		}
		
		return $instance;
	}
} // Class MXPDev_Tournaments_Widget ends here

// Register and load the widget
function mxpdev_load_widget() {
	register_widget( 'MXPDev_Tournaments_Widget' );
}
add_action( 'widgets_init', 'mxpdev_load_widget' );

// show tournament: order by future tournament then past tournament
function show_tournaments($result, $detail_level) {
	global $db;
	
	$details = array('max_tournament_name_length' => 25, 'date_detail' => 1, 'signup_details'=> 0, 'scroll'=>1);
	switch ($detail_level) {
	case 'minimum':
		$details['max_tournament_name_length'] = 25;
		$details['date_format'] = 'm/d';
		$details['signup_detail'] = 0;
		$details['scroll'] = 1;
	break;
	case 'medium':
		$details['max_tournament_name_length'] = 35;
		$details['date_format'] = 'm/d/y';
		$details['signup_detail'] = 0;
		$details['scroll'] = 0;
	break;
	case 'maximum':
		$details['max_tournament_name_length'] = -1;
		$details['date_format'] = 'D, M j, Y g:ia';
		$details['signup_detail'] = 1;
		$details['scroll'] = 0;
	break;
	}
	
	if ($db->sql_numrows($result)) {
		$count = 0;
		$today = time();
		$content .= '<table cellpadding="5" cellspacing="5" width="100%">';
		while ($row = $db->sql_fetchrow($result)) {

			// apply tournament name length limit
			if ($details['max_tournament_name_length'] == -1) {
				$tournament_name = $row['tournament_name'];
			} else {
				$tournament_name = (strlen($row['tournament_name'])>$details['max_tournament_name_length']?substr($row['tournament_name'],0, $details['max_tournament_name_length']-3).'..':$row['tournament_name']);
			}
			
			
			$tourney_info = '';
			$count++;
			if ($row['tournament_date'] > $today) {
				$future_count++;
				if ($future_count == 3 && $details['scroll']) { // show top 2 as static, scroll the rest
					$content .= '</table><br>';
					$content .= "<marquee behavior='scroll' direction='up' height='80px' scrollamount='1' scrolldelay='10' onMouseOver='this.stop()' onMouseOut='this.start()'>";
					$content .= '<table cellpadding="2" cellspacing="2" width="100%">';
				}
			}

			// crossed out past tournaments
			if ($today >= $row['tournament_date']) $tournament_name = '<s>'.$tournament_name.'</s>';
			
			$tourney_info = '<tr><td><a href="/modules.php?name=Golf&op=tournaments_signup&tournament_id='.$row['tournament_id'].'">'.$tournament_name.'</a></td>';
			$tourney_info .= '<td valign="top">';
			if ($today >= $row['tournament_date']) $tourney_info .= '<s>';
			$tourney_info .= date($details['date_format'],$row['tournament_date']);
			if ($today >= $row['tournament_date']) $tourney_info .= '</s>';
			$tourney_info .= '</td></tr>';
	
			if ($row['tournament_date'] > $today) {
				$content .= $tourney_info;
			} else {
				// show past tournaments at the bottom
				$older .= $tourney_info;
			}
		}
		$content .= $older;
		if ($future_count >= 3 && $details['scroll']) { // show top 2 as static, scroll the rest
			$content .= "</table></marquee>".'<br><table cellpadding="2" cellspacing="2" width="100%">';
		}
	}
	$content .= '<br>';
	$content .= '<tr><td colspan="2" align="center" nowrap> See <a href="modules.php?name=Golf&op=tournaments_rules">RULES</a> for details.</td></tr>';
	$content .= "</table>";
	
	return $content;
}
<?php
/*
Plugin Name: RPTag
Plugin URI: http://kouloumbris.com/projects/wp-plugins/rptag/
Description: Recent Posts in Tag. This widgets allows you to show recent posts list in a specific tag.
Version: 1.0.1
Author: Constantinos Kouloumbris
Author URI: http://kouloumbris.com/
*/
	
class RPTag {
    var $plugin_folder = '';

    var $default_options = array(
            'title' => 'Recent Posts in Tag', 
            'count' => '15',
            'tag' => ''
    );

	function RPTag(){}

    function init() {
        if (!$options = get_option('widget_rptag'))
            $options = array();
            
        $widget_ops = array('classname' => 'widget_rptag', 'description' => 'Recent Posts in Tag');
        $control_ops = array('width' => 250, 'height' => 100, 'id_base' => 'widget_rptag');
        $name = 'RPTag';
        
        $registered = false;
        foreach (array_keys($options) as $o) {
            if (!isset($options[$o]['title']))
                continue;
                
            $id = "widget_rptag-$o";
            $registered = true;
            wp_register_sidebar_widget($id, $name, array(&$this, 'widget'), $widget_ops, array( 'number' => $o ) );
            wp_register_widget_control($id, $name, array(&$this, 'control'), $control_ops, array( 'number' => $o ) );
        }
        if (!$registered) {
            wp_register_sidebar_widget('widget_rptag-1', $name, array(&$this, 'widget'), $widget_ops, array( 'number' => -1 ) );
            wp_register_widget_control('widget_rptag-1', $name, array(&$this, 'control'), $control_ops, array( 'number' => -1 ) );
        }
    }

    function widget($args, $widget_args = 1) {
        extract($args);
        global $post;

        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array( 'number' => -1 ));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_rptag');
        if (!isset($options_all[$number]))
            return;

        $options = $options_all[$number];
		if ( !$options["count"] )
			$options["count"] = 10;
		else if ( $options["count"] < 1 )
			$options["count"] = 1;
		else if ( $options["count"] > 15 )
			$options["count"] = 15;
		
		$posts = get_posts('tag='.$options["tag"].'&numberposts='.$options["count"]);
		echo $before_widget.$before_title;
        echo $options["title"];
        echo $after_title;
		echo '<div>';
		echo '<ul>';
		foreach ( $posts as $post ) {
			echo '<li><a href="';
			the_permalink();
			echo '">';
			the_title();
			echo '</a></li>';
		}
		echo '</ul></div>';
        echo $after_widget;
    }

    function control($widget_args = 1) {

        global $wp_registered_widgets;
        static $updated = false;

        if ( is_numeric($widget_args) )
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_rptag');
        if (!is_array($options_all))
            $options_all = array();  
            
        if (!$updated && !empty($_POST['sidebar'])) {
            $sidebar = (string)$_POST['sidebar'];

            $sidebars_widgets = wp_get_sidebars_widgets();
            if (isset($sidebars_widgets[$sidebar]))
                $this_sidebar =& $sidebars_widgets[$sidebar];
            else
                $this_sidebar = array();

            foreach ($this_sidebar as $_widget_id) {
                if ('widget_rptag' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("rptag-$widget_number", $_POST['widget-id']))
                        unset($options_all[$widget_number]);
                }
            }
            foreach ((array)$_POST['widget_rptag'] as $widget_number => $posted) {
                if (!isset($posted['title']) && isset($options_all[$widget_number]))
                    continue;
                
                $options = array();
                
                $options['title'] = $posted['title'];
                $options['count'] = $posted['count']; 
                $options['tag'] = $posted['tag'];
                
                $options_all[$widget_number] = $options;
            }
            update_option('widget_rptag', $options_all);
            $updated = true;
        }

        if (-1 == $number) {
            $number = '%i%';
            $values = $this->default_options;
        }
        else {
            $values = $options_all[$number];
        }
        include("rptag-form.php");
    }
}

$rp_tag = new RPTag();
add_action('widgets_init', array($rp_tag, 'init'));

?>
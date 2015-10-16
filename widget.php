<?php
class RongdedeTransWidget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'RongdedeTranslate' );
	}

	function widget( $args, $instance ) {
		// Widget output
		echo '<aside id="recent-posts-2" class="widget widget_recent_entries">';
		echo '<h2 class="widget-title">多国语言翻译</h2>';
		echo '<ul>';
		foreach(rongdede_const::$languages as $key => $value){
			echo "<li><a href='".$this->get_transurl($key)."'>".$value."</a></li>";
		}
		echo '</ul>';
		echo '</aside>';
		//echo plugin_dir_path(__FILE__)."log.txt";
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
	}

	function form( $instance ) {
		// Output admin widget options form
		//echo "<a href='http://linrong.me'>测试一下是不是这样的。。。</a>";

	}

	function get_transurl($lang){
		global $wp;
		$current_url = home_url();
		$urlarr=parse_url($current_url);
		if(get_option('permalink_structure'))
		{
			$ul="/".$lang;
			//$ul="2222".$current_url."111";
		}
		else
		{
			$ul=$current_url."&".LANG_PARAM."=".$lang;				
		}

		return $ul;
	}



}

function myplugin_register_widgets() {
	register_widget( 'RongdedeTransWidget' );
}

//add_action( 'widgets_init', 'myplugin_register_widgets' );
add_action( 'widgets_init', 'myplugin_register_widgets' );
?>

<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class AJ_Stock_Chart extends WP_Widget {
	// class constructor
    public function __construct(){
        $widget_ops = array(
            'classname' => 'aj_stock_chart',
            'description' => 'AJ 주식 차트',
        );
        parent::__construct('aj_stock_chart', 'AJ 주식 - 차트', $widget_ops);
    }
    
    public $args = array(
        'before_widget'  => "<div class='widget'>",
        'after_widget'  => "</div>"
    );

    public function widget($args, $instance){
        global $table_prefix, $post;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $charttype = !empty($instance['charttype']) ? $instance['charttype'] : "1m";
        $showprice = !empty($instance['showprice']) ? $instance['showprice'] : "Y";
        $title = !empty($instance['title']) ? $instance['title'] : "";

        $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
        $ajsi_opt_refresh = get_option('ajsi_opt_refresh', "10000");
        $ajsi_opt_skin = get_option('ajsi_opt_reviewskin', "basic_1");
        $ajsi_opt_chart_interval = get_option('ajsi_opt_chart_interval', "1m");
        $ajsi_opt_chartvolume = get_option('ajsi_opt_chartvolume', "Y");
        $ajsi_opt_showchartopt = get_option('ajsi_opt_showchartopt', "Y");
        $ajsi_opt_chart_main_height = get_option('ajsi_opt_chart_main_height', "240");
        $ajsi_opt_chart_vol_height = get_option('ajsi_opt_chart_vol_height', "100");

        $ajsi_opt_chart_down_color = get_option('ajsi_opt_chart_down_color', "#0f9d58");
        $ajsi_opt_chart_up_color = get_option('ajsi_opt_chart_up_color', "#a52714");
        
        if(!$ajsi_opt_stockcode){
            echo "NOT REGISTER CODE";
            return;
        }

        $rndKey = rand(1000, 9999);

        wp_enqueue_script('googlechart', esc_url_raw('https://www.gstatic.com/charts/loader.js'), array(), null );
        wp_enqueue_script('googlejsapi', esc_url_raw('https://www.google.com/jsapi'), array(), null );
        wp_enqueue_script('ajsi_stockinfo_chart_js', AJSI_S3_AJ_VIEW_CHART_JS, array('jquery'), '1.0', true );
        wp_localize_script('ajsi_stockinfo_chart_js', 'chartinfo', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'code' => $ajsi_opt_stockcode,
            'rndkey' => $rndKey,
            'interval' => $charttype,
            'showprice' => $showprice,
            'refresh_interval' => $ajsi_opt_refresh,
            'iswidget' => "Y",
            'usevolume' => "N",
            'showopt' => $ajsi_opt_showchartopt,
            'colorup' => $ajsi_opt_chart_up_color,
            'colordown' => $ajsi_opt_chart_down_color,
            'mh' => $ajsi_opt_chart_main_height,
            'vh' => $ajsi_opt_chart_vol_height,
        ));

        include(AJSI_PLUGIN_PATH."views/skins/".$ajsi_opt_skin."/widget_html.php");
        $charthtml = str_replace("[RND]", $rndKey, $charthtml);

        echo $args['before_widget'];
        if (!empty( $instance['title'])) {
            echo "<h5>".apply_filters('widget_title', $instance['title'])."</h5>";
        }
        echo $charthtml;
        echo $args['after_widget'];
    }

    public function form($instance){
        global $table_prefix;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( '차트', 'ajsi_chart' );
        $charttype = !empty( $instance['charttype'] ) ? $instance['charttype'] : esc_html__( '1m', 'ajsi_chart' );
?>
	<p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
            <?php esc_attr_e( 'Title:', 'ajsi_chart' ); ?>
        </label>
        <input
            class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
            name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text"
            value="<?php echo esc_attr($title);?>">
	</p>
	<p>
        <label for="charttype">
            <?php esc_attr_e('Chart Type', 'ajsi_chart' ); ?>
        </label>
        <select name="<?php echo esc_attr($this->get_field_name('charttype'));?>" class="widefat" id="skin" type="text">
            <option value="1m" <?php if(esc_attr($charttype) == "1m"){ echo "selected"; }?>>1m 차트</option>
            <option value="1d" <?php if(esc_attr($charttype) == "1d"){ echo "selected"; }?>>일봉 차트</option>
        </select>
	</p>
<?php
    }

	// save options
    public function update( $new_instance, $old_instance){
        $instance = array();
        $instance['charttype'] = ( !empty($new_instance['charttype']) ) ? strip_tags( $new_instance['charttype'] ) : '';
        $instance['showprice'] = ( !empty($new_instance['showprice']) ) ? strip_tags( $new_instance['showprice'] ) : '';
        $instance['title'] = ( !empty($new_instance['title']) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

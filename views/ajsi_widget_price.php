<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class AJ_Stock_Price extends WP_Widget {
	// class constructor
    public function __construct(){
        $widget_ops = array(
            'classname' => 'aj_stock_price',
            'description' => 'AJ 주가 정보',
        );
        parent::__construct('aj_stock_price', 'AJ 주식 - 주가정보', $widget_ops);
    }
    
    public $args = array(
        'before_widget'  => "<div class='widget'>",
        'after_widget'  => "</div>"
    );

    public function widget($args, $instance){
        global $table_prefix, $post;
        $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
        $ajsi_opt_refresh = get_option('ajsi_opt_refresh', "10000");
        $ajsi_opt_skin = get_option('ajsi_opt_reviewskin', "basic_1");

        if(!$ajsi_opt_stockcode){
            echo "NOT REGISTER CODE";
            return;
        }

        wp_enqueue_script('ajsi_stockinfo_all_js', AJSI_S3_AJ_VIEW_JS, array('jquery'), '1.0', true );
        wp_localize_script('ajsi_stockinfo_all_js', 'getinfo', array(
            'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajsi-ajax-nonce'),
            'refresh_interval' => $ajsi_opt_refresh
        ));

        include(plugin_dir_path(__FILE__)."skins/".$ajsi_opt_skin."/widget_html.php");
        $url = AJSI_PLUGIN_PATH."data/".$ajsi_opt_stockcode.".xml";
        if($instance['showdiffprice'] == "N")
            $pricebox = $pricebox_onlyprice;

        $pricebox = str_replace("[DungRak_UP]", plugins_url("skins/".$ajsi_opt_skin."/images/up.png", __FILE__), $pricebox);
        $pricebox = str_replace("[DungRak_UPUP]", plugins_url("skins/".$ajsi_opt_skin."/images/up.png", __FILE__), $pricebox);
        $pricebox = str_replace("[DungRak_DOWN]", plugins_url("skins/".$ajsi_opt_skin."/images/down.png", __FILE__), $pricebox);
        $pricebox = str_replace("[DungRak_DOWNDOWN]", plugins_url("skins/".$ajsi_opt_skin."/images/down.png", __FILE__), $pricebox);
        $pricebox = str_replace("[DungRak_SAME]", plugins_url("skins/".$ajsi_opt_skin."/images/bohap.png", __FILE__), $pricebox);
        $pricebox = ajsi_getsetdata($url, $pricebox, $ajsi_opt_skin);

        echo $args['before_widget'];
        if (!empty( $instance['title'])) {
            echo "<h5>".apply_filters('widget_title', $instance['title'])."</h5>";
        }
        echo $pricebox;
        echo $args['after_widget'];
    }

    public function form($instance){
        global $table_prefix;

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( '주가 정보', 'ajsi_price' );
        $showdiffprice = !empty( $instance['showdiffprice'] ) ? $instance['showdiffprice'] : esc_html__( 'Y', 'ajsi_price' );
?>
	<p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
            <?php esc_attr_e( 'Title:', 'ajsi_price' ); ?>
        </label>
        <input
            class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
            name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text"
            value="<?php echo esc_attr($title);?>">
	</p>
	<p>
        <label for="showdiffprice">
            <?php esc_attr_e('전날 대비 표시', 'ajsi_price' ); ?>
        </label>
        <select name="<?php echo esc_attr($this->get_field_name('showdiffprice'));?>" class="widefat" id="skin" type="text">
            <option value="Y" <?php if(esc_attr($showdiffprice) == "Y"){ echo "selected"; }?>>보이기</option>
            <option value="N" <?php if(esc_attr($showdiffprice) == "N"){ echo "selected"; }?>>가리기</option>
        </select>
	</p>
<?php
    }

	// save options
    public function update( $new_instance, $old_instance){
        $instance = array();
        $instance['showdiffprice'] = ( !empty($new_instance['showdiffprice']) ) ? strip_tags( $new_instance['showdiffprice'] ) : '';
        $instance['title'] = ( !empty($new_instance['title']) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}

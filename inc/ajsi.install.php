<?php
function ajsi_create_plugin_database_table()
{
    global $table_prefix, $wpdb, $ajStockInfoDBVersion;
    $installed_db_ver = get_option("ajsi_db_version");

    $sql = array();
    if($installed_db_ver !== $ajStockInfoDBVersion) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql[] = "CREATE TABLE ". $table_prefix . "ajsi_stock_1m (
                id int(11) NOT NULL AUTO_INCREMENT,
                code varchar(100) NOT NULL,
                curprice int NOT NULL,
                volumn int NOT NULL,
                registered datetime NOT NULL,
                PRIMARY KEY (`id`),
                INDEX ajsi_stock_1m_code (code)
            ) " . $charset_collate . ";";
        $sql[] = ajsi_getSql($table_prefix . "ajsi_stock_1d", "1d"); 
        $sql[] = "CREATE TABLE ". $table_prefix . "ajsi_stock_data (
                id int(11) NOT NULL AUTO_INCREMENT,
                code varchar(100) NOT NULL,
                datatype text NOT NULL,
                datavalue text NOT NULL,
                updated datetime NOT NULL,
                PRIMARY KEY (`id`),
                INDEX ajsi_stock_data (code)
            ) " . $charset_collate . ";";
        $sql[] = "CREATE TABLE ". $table_prefix . "ajsi_stock_log (
                id int(11) NOT NULL AUTO_INCREMENT,
                loglevel varchar(20) NOT NULL,
                logdata text NOT NULL,
                updated datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) " . $charset_collate . ";";
    }

    if(count($sql)){
        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    add_option('ajsi_db_version', $ajStockInfoDBVersion);

    if (!wp_next_scheduled('ajsi_event_getdata')) {
        wp_schedule_event(time(), '1_min', 'ajsi_event_getdata');
    }
    register_uninstall_hook( __FILE__, 'ajsi_uninstall' );
}
function ajsi_uninstall(){
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS " .$table_prefix . " ajsi_stock_1m" );
    $wpdb->query("DROP TABLE IF EXISTS " .$table_prefix . " ajsi_stock_5m" );
    $wpdb->query("DROP TABLE IF EXISTS " .$table_prefix . " ajsi_stock_1h" );
    $wpdb->query("DROP TABLE IF EXISTS " .$table_prefix . " ajsi_stock_data" );
    $wpdb->query("DROP TABLE IF EXISTS " .$table_prefix . " ajsi_stock_log" );
    delete_option("ajsi_db_version");
}
function ajsi_deactivate(){
    wp_clear_scheduled_hook('ajsi_event_getdata');
}
function ajsi_getSql($table, $period){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    return "CREATE TABLE " . $table . " (
            id int(11) NOT NULL AUTO_INCREMENT,
            code varchar(100) NOT NULL,
            open int NOT NULL,
            high int NOT NULL,
            low int NOT NULL,
            close int NOT NULL,
            volumn int NOT NULL,
            registered datetime NOT NULL,
            PRIMARY KEY (`id`),
            INDEX ajsi_stock_" . $period . "_code (code)
        ) " . $charset_collate . ";";
}
?>

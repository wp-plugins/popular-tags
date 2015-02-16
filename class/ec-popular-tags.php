<?php

class EC_Popular_Tags {

    private static $instance;
    private static $dboption = 'ec_popular_tags_dbversion';
    private static $dbversion = '1.0.0';

    public function __construct()
    {
    	
        self::create_table();

        add_action( 'wp_footer', array( $this, 'add_tags_id_to_footer' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'popular_tags_script' ) );
        add_action( 'wp_head', array( $this, 'popular_post_ajaxurl' ) );
        add_action( 'widgets_init', array( $this, 'register_widget' ) );

        // add_action( 'admin_enqueue_scripts', array( $this, 'ec_sidebar_creator_admin_enqueue_scripts' ) );
        add_action( 'wp_ajax_ec_insert_tags_visit', array( $this, 'ec_insert_tags_visit_ajax_callback' ) );
        add_action( 'wp_ajax_nopriv_ec_insert_tags_visit', array( $this, 'ec_insert_tags_visit_ajax_callback' ) );
        // add_action( 'widgets_init', array( $this, 'ec_register_sidebar_function' ) );

    }

    function register_widget(){
        register_widget( 'Popular_Tags_Widget' );
    }

    function popular_post_ajaxurl() {
        ?>
        <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php 
    }

    function popular_tags_script() {
        if ( is_singular( 'post' ) || is_tag() ) {
            wp_enqueue_script( 'ec-popular-tags', EC_POPULAR_TAGS_URL . '/js/popular-tags.js', array(), '1.0.0', true );
        }
    }

    public static function ec_insert_tags_visit_ajax_callback() {
        global $wpdb; 

        if ( is_admin() ) {

            $table_name = $wpdb->prefix . 'tags_visit';

            $tag_ids = $_POST['tag_ids'];
            $source = $_POST['source'];
            $source_id = $_POST['source_id'];

            $tags_arr = explode(',', $tag_ids);

            if ( count( $tags_arr ) > 0 ) {
                foreach ($tags_arr as $key => $tag_id) {
                    $data = array(
                        'tag_id' => $tag_id,
                        'source' => $source,
                        'source_id' => $source_id,
                        'visit' => date('Y-m-d H:i:s')
                    );

                    $wpdb->insert( $table_name, $data );

                }
            }

            echo json_encode(1);

        }

        wp_die(); 
    }

    public function add_tags_id_to_footer() {

        $html = '';

        // tags id
        if ( is_singular( 'post' ) ) {
            $post_id = get_the_ID();
            $tags = wp_get_post_tags( $post_id );

            if ( count( $tags ) > 0 ) {
                $tag_ids = array();
                foreach ($tags as $key => $tag) {
                    $tag_ids[] = $tag->term_id;
                }
                $html = '<input type="hidden" id="ec-popular-tags-id" value="'.implode( ',', $tag_ids ).'">';
                $html .= '<input type="hidden" id="ec-popular-tags-source" value="post">';
                $html .= '<input type="hidden" id="ec-popular-tags-source_id" value="'.$post_id.'">';

            }

        } else if ( is_tag( ) ) {
            global $wp_query;
            $tag_id = $wp_query->get_queried_object_id();
            $html = '<input type="hidden" id="ec-popular-tags-id" value="'.$tag_id.'">';
            $html .= '<input type="hidden" id="ec-popular-tags-source" value="tags">';
            $html .= '<input type="hidden" id="ec-popular-tags-source_id" value="'.$tag_id.'">';
        }

        echo $html;
    }
    

    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

    public static function create_table() {

        global $wpdb;

        $installed_ver = get_option( self::$dboption );

        if ( $installed_ver != self::$dbversion ) {

            $table_name = $wpdb->prefix . 'tags_visit';

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              tag_id mediumint(9) NOT NULL,
              source varchar(50) NOT NULL,
              source_id mediumint(9) NOT NULL,
              visit datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              UNIQUE KEY id (id),
              INDEX (visit)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

        }

    }

}
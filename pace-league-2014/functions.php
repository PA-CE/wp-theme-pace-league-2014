<?php

remove_action( 'wp_head', 'rsd_link');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'wp_head', 'wp_shortlink_wp_head');
remove_action( 'wp_head', 'wp_generator');

add_action('init', 'pace_holding_init', 0);


function pace_holding_init() {

    global $current_user, $cart_status; get_currentuserinfo();

    register_nav_menus(
        array(
            'header-menu' => __( 'Main Menu' )
        )
    );



    if(function_exists('wpmd_is_notdevice')){

        if(!wpmd_is_notdevice()){

            add_filter( 'show_admin_bar', '__return_false');

        }

    }

    $cart_status = array('Abandoned', 'Viewed', 'View with Purchase Order');

}

// Theme Setup
add_action( 'after_setup_theme', 'pace_holding_setup' );

function pace_holding_setup() {



    define('NO_HEADER_TEXT', true );
    define('HEADER_TEXTCOLOR', '');


    add_editor_style('/less/editor-style.less');

    add_post_type_support('page', 'excerpt');

    add_theme_support( 'post-thumbnails' );

    set_post_thumbnail_size( 690, 120, true ); // Normal page banner thumbnails

    add_image_size( 'pace_holding_gallery_item', 304, 304, true);

    add_image_size( 'product-single-cover', 175, 500);
    add_image_size( 'product-small-cover', 70, 180);
    add_image_size( 'product-archive-cover', 115, 400);

    add_image_size( 'slider-block-large', 475, 345, true);

    add_image_size( 'video-block-background-slider', 230, 150, true);
    add_image_size( 'video-block-background', 250, 150, true);

    add_image_size( 'video-block-background', 250, 150, true);

    add_image_size( 'video-archive-thumbnail', 625, 367, true);


}

//Widget Setup

add_action( 'widgets_init', 'pace_holding_sidebar_setup' );

function pace_holding_sidebar_setup(){



}

//asset Setup

add_action('wp_print_scripts', 'pace_holding_scriptSetup');

function pace_holding_scriptSetup(){

    if(is_admin())
        return false;

    global $post, $wp_query, $current_user; get_currentuserinfo();

    $term       = $taxonomy = "";
    $scripts    = array();
    $temp_url   = get_template_directory_uri();

//    $addthis = pace_holding_get_theme_option('sharethis');

    $scripts = array(
        'register' => array( //register is just scripts you want registered
            'front' => ''
        ),
        'enqueue' => array( //enqueue assumes you want them registered aswell
            'admin' => array( //admin side of wp
                'custom' => array( //default.group
                    'scripts' => ''
                )
            ),
            'front' => array( //front side of wp\
//                'prototype' => array( //default.group
//                    'scripts' => array(
//                        'base' => array(
//                            'src'       => $temp_url.'/js/prototype.1.7.js',
//                            'deps'      => false,
//                            'var'       => false,
//                            'footer'    => false
//                        )
//                    )
//                ),
                'jquery' => array( //default.group
                    'scripts' => array(


                    )
                ),
                'defaults' => array( //default.group
                    'scripts' => array(
                        'main.min'=> array(
                            'src'       => $temp_url.'/public/build/js/app.min.js',
                            'deps'      => false,
                            'var'       => false,
                            'footer'    => true
                        )
                    )
                )
            )
        ),
        'conditional' => array(
            'front' => ''
        )
    );

    if(isset($wp_query->query)){
        $taxonomyDetails = $wp_query->get_queried_object();
    }

    if(isset($post)){

        $slug 		= basename(get_permalink($post->ID));
        $url 		= get_template_directory_uri();
        $filename 	= '/assets/js/default.'.$slug.'.js';

        $postType 	= get_post_type($post->ID);
        $typeName	= '/assets/js/default.'.$postType.'.js';

    }


    if (!is_admin() && !is_login_page()) {

        if(is_array($scripts['register']['front'])){

            foreach($scripts['register']['front'] as $key => $value){

                foreach($value['scripts'] as $handle => $args){

                    if((empty($args['active']) || $args['active'])){
                        wp_register_script( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['footer']);
                    }



                }
            }
        }

        if(is_array($scripts['enqueue']['front'])){

            //build scripts based on $scripts array
            foreach($scripts['enqueue']['front'] as $key => $value){

                foreach($value['scripts'] as $handle => $args){

                    if((empty($args['active']) || $args['active'])){
                        wp_register_script( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['footer']);
                        wp_enqueue_script(  $key.'.'.$handle);

                    }
                }
            }
        }

        if(is_array($scripts['conditional']['front'])){

            //build scripts based on $scripts array
            foreach($scripts['conditional']['front'] as $key => $value){

                foreach($value['conditional'] as $function => $arg){

                    if($arg === null){

                        //if not true break
                        if(!call_user_func($function)){
                            continue 2;
                        }

                    } else {

                        //if note equal arg break
                        if(call_user_func($function) != $arg ){
                            continue 2;
                        }

                    }
                }

                foreach($value['scripts'] as $handle => $args){
                    if((!isset($args['active']) || $args['active'])){
                        wp_register_script( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['footer']);
                        wp_enqueue_script(  $key.'.'.$handle);
                    }
                }
            }
        }

        if(file_exists(TEMPLATEPATH.$filename)){

            wp_register_script($slug, $url.$filename, false,false, false);
            wp_enqueue_script($slug);

        }

        //post type dependant
        if(file_exists(TEMPLATEPATH.$typeName)){

            wp_register_script($postType, $url.$typeName, false,false, false);
            wp_enqueue_script($postType);

        }

        if($post){

            $template 		= substr(get_post_meta($post->ID, '_wp_page_template', TRUE), 0, -4);
            $templateName	= '/assets/js/'.$template.'.js';

            //template dependant
            if(file_exists(TEMPLATEPATH.$templateName)){

                wp_register_script($template, $url.$templateName, false,false, false);
                wp_enqueue_script($template);

            }

        }

        if($taxonomyDetails){

            $taxonomy 		= $taxonomyDetails->taxonomy;
            $taxonomyName	= '/assets/js/'.$taxonomy.'.js';

            //taxonomy dependant
            if(file_exists(TEMPLATEPATH.$taxonomyName)){

                wp_register_script($taxonomy, $url.$taxonomyName, false,false, false);
                wp_enqueue_script($taxonomy);

            }

        }


        if($taxonomyDetails){

            $term 		= $taxonomyDetails->taxonomy;
            $termName 	= '/assets/js/'.$taxonomy.'.js';

            //term dependant
            if(file_exists(TEMPLATEPATH.$termName)){

                wp_register_script($term, $url.$termName, false,false, false);
                wp_enqueue_script($term);

            }

        }

        if( $term && $taxonomy ){

            $mix 		= $term.'-'.$taxonomy;
            $mixName	= '/assets/js/'.$mix.'.js';


            //mix
            if(file_exists(TEMPLATEPATH.$mixName)){

                wp_register_script($mix, $url.$mixName, false,false, false);
                wp_enqueue_script($mix);

            }

        }

        if(file_exists(TEMPLATEPATH.'/assets/js/home.js') && $post->ID == get_option('page_on_front')){

            wp_register_script('home',  get_template_directory_uri().'/assets/js/home.js', false,false, false);
            wp_enqueue_script('home');

        }

        if(file_exists(TEMPLATEPATH.'/assets/js/default.author.js') && is_author()){

            wp_register_script('default-author',  get_template_directory_uri().'/assets/js/default.author.js', false,false, false);
            wp_enqueue_script('default-author');

        }
    }

}

add_action('wp_print_styles', 'pace_holding_stylesetup');

function pace_holding_stylesetup() {

    global $post, $wp_query;

    $taxonomyDetails = $wp_query->get_queried_object();

    $term = $taxonomy = "";
    $temp_url   = get_template_directory_uri();

    $slug 		= basename(get_permalink($post->ID));
    $url 		= get_template_directory_uri();
    $filename 	= '/assets/css/default.'.$slug.'.css';

    $postType 	= get_post_type($post->ID);
    $typeName	= '/assets/css/default.'.$postType.'.css';

    $style = array(
        'register' => false,
        'enqueue' => array( //enqueue assumes you want them registered aswell
            'admin' => array( //admin side of wp
                '' => '',
            ),
            'front' => array( //front side of wp
                'plugins' => array( //default.group
                    'styles' => array(
//                        'jquery.ui' => array(
//                            'src'       => $temp_url.'/css/smoothness/jquery-ui-1.10.3.custom.min.css',
//                            'deps'      => false,
//                            'var'       => false,
//                            'type'      => 'screen, projection, print'
//                        ),

                    )
                ),
                'custom' => array( //default.group
                    'styles' => array(
                        'app' => array(
                            'src'       => $temp_url.'/public/build/css/app.css',
                            'deps'      => false,
                            'var'       => false,
                            'type'      => 'screen, projection, print'
                        ),
                        'default' => array(
                            'src'       => $temp_url.'/style.css',
                            'deps'      => false,
                            'var'       => false,
                            'type'      => 'screen, projection, print'
                        )





                    )

                )
            )
        )
    );


    if (!is_admin() && !is_login_page()) {

        if(is_array($style['register']['front'])){

            foreach($style['register']['front'] as $key => $value){

                foreach($value['styles'] as $handle => $args){
                    wp_register_style( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['type']);

                }
            }

        }

        if(is_array($style['enqueue']['front'])){

            //build scripts based on $scripts array
            foreach($style['enqueue']['front'] as $key => $value){

                foreach($value['styles'] as $handle => $args){
                    wp_register_style( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['type']);
                    wp_enqueue_style(  $key.'.'.$handle);
                }
            }

        }

        if(isset($style['conditional'])){

            if(is_array($style['conditional']['front']) ){

                //build scripts based on $scripts array
                foreach($scripts['conditional']['front'] as $key => $value){

                    foreach($value['conditional'] as $function => $arg){

                        if($arg === null){

                            //if not true break
                            if(!call_user_func($function)){
                                continue 2;
                            }

                        } else {

                            //if note equal arg break
                            if(call_user_func($function) != $arg ){
                                continue 2;
                            }

                        }
                    }

                    foreach($value['styles'] as $handle => $args){
                        wp_register_style( $key.'.'.$handle,  $args['src'], $args['deps'], $args['var'], $args['type']);
                        wp_enqueue_style(  $key.'.'.$handle);
                    }
                }

            }

        }
    }

    //page dependant css
    if(file_exists(TEMPLATEPATH.$filename)){

        wp_register_style($slug, $url.$filename,false, '', 'screen, projection');
        wp_enqueue_style($slug);

    }

    //post type dependant
    if(file_exists(TEMPLATEPATH.$typeName)){

        wp_register_style($postType, $url.$typeName,false, '', 'screen, projection');
        wp_enqueue_style($postType);

    }

    //template dependant
    if($post){

        $template 		= substr(get_post_meta($post->ID, '_wp_page_template', TRUE), 0, -4);
        $templateName	= '/assets/css/'.$template.'.css';

        if(file_exists(TEMPLATEPATH.$templateName)){

            wp_register_style($template, $url.$templateName,false, '', 'screen, projection');
            wp_enqueue_style($template);

        }

    }

    //taxonomy dependant
    if($taxonomyDetails){

        $taxonomy 		= $taxonomyDetails->slug;
        $taxonomyName	= '/assets/css/'.$taxonomy.'.css';

        if(file_exists(TEMPLATEPATH.$taxonomyName)){

            wp_register_style($taxonomy, $url.$taxonomyName,false, '', 'screen, projection');
            wp_enqueue_style($taxonomy);

        }

    }


    if($taxonomyDetails){

        $term 		= $taxonomyDetails->taxonomy;
        $termName 	= '/assets/css/'.$taxonomy.'.css';

        //term dependant
        if(file_exists(TEMPLATEPATH.$termName)){

            wp_register_style($term, $url.$termName,false, '', 'screen, projection');
            wp_enqueue_style($term);

        }

    }

    if( $term && $taxonomy ){

        $mix 		= $term.'-'.$taxonomy;
        $mixName	= '/assets/css/'.$mix.'.css';

        //mix
        if(file_exists(TEMPLATEPATH.$mixName)){

            wp_register_style($mix, $url.$mixName,false, '', 'screen, projection');
            wp_enqueue_style($mix);

        }

    }


}

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

add_shortcode('match-listing', 'match_tables');

function match_tables($attr, $content){

    global $post;

    extract(shortcode_atts(array(
        'refresh' => 'true',
        'updating' => ''
    ), $attr));

    if($refresh){

        $updating = 'Table will automatically update every 60 seconds';

    ?>
    <script type="text/javascript">

        function updateMatches(){

            $.ajax({
                url: WPPATH + "/wp-admin/admin-ajax.php",
                type:'POST',
                cache: false,
                data:  {
                    action: 'get_match_results'
                },
                dataType: 'JSON',
                success:function(results){

                    $('.match-table-container').html(results.matches_string);
                    $('.match-list-updated').text('Last updated @'+ results.updated_at)
                }
            });

            setTimeout(updateMatches, 60000);
        }

        updateMatches();

    </script>

    <?php

    }

    return sprintf('<div class="match-table-container">%s</div><div><small>%s, <span class="match-list-updated">last updated @%s</span></small></div>', get_post_meta($post->ID, 'matches', true), $updating, date('d.m.Y H:i:s'));

}

function get_match_results(){

    $matches_String = get_post_meta(get_option('page_on_front'), 'matches', true);

    echo json_encode(array('matches_string' => $matches_String, 'updated_at' => date('d.m.Y H:i:s')));

    die();

}


add_action('wp_ajax_get_match_results', 'get_match_results');
add_action('wp_ajax_nopriv_get_match_results', 'get_match_results');

function ac_2014_javascript_header(){

    global $post;

    ?>

    <script type="text/javascript">
        //<![CDATA[
        var templatepath = '<?php bloginfo('template_directory'); ?>/assets/';
        var WPPATH = '<?php bloginfo('wpurl'); ?>';

        <?php if($post->ID){ ?>

        var post_id = <?php echo $post->ID; ?>;

        <?php } ?>

        //]]>
    </script>


<?php

}

add_action('wp_head', 'ac_2014_javascript_header');

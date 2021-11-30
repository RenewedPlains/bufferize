<?php
/**
 * Plugin Name: Bufferize
 * Plugin URI: http://tschaki.com/bufferize
 * Description: Bufferize
 * Version: 1.0
 * Author: Mario Freuler
 * Author URI: http://freuli.ch
 * Text Domain: bufferize
 */



define('WYSIWYG_META_BOX_ID', 'my-editor');
define('WYSIWYG_EDITOR_ID', 'myeditor'); //Important for CSS that this is different
define('WYSIWYG_META_KEY', 'extra-content');
add_action( 'edit_form_after_editor', 'wysiwyg_render_meta_box' );


function wysiwyg_render_meta_box() {
    global $post;
    $meta_box_id = WYSIWYG_META_BOX_ID;
    $editor_id = WYSIWYG_EDITOR_ID;
    echo "<style>
            #post-body-content {
            width: 100%;
            }
            #postdivrich {
                width: calc(50% - 10px);
                float: left;
            }
            #wp-myeditor-wrap {
            width: calc(50% - 10px);
            float: right;
            margin-top: 20px;
            }
            .wanted-title, .replacewith-title {
            position: relative;
            top: 30px;
            }
            </style>
            <script type='text/javascript'>
            jQuery(function($){
                var fix_wp_editor_height = $('#mceu_30').height();
                $('#mceu_90, #mceu_90 iframe').height(fix_wp_editor_height);
                $('#$meta_box_id #editor-toolbar > a').click(function(){
                    $('#$meta_box_id #editor-toolbar > a').removeClass('active');
                    $(this).addClass('active');
                });
                if($('#$meta_box_id #edButtonPreview').hasClass('active')){
                    $('#$meta_box_id #ed_toolbar').hide();
                }
                $('#$meta_box_id #edButtonPreview').click(function(){
                    $('#$meta_box_id #ed_toolbar').hide();
                });
                $('#$meta_box_id #edButtonHTML').click(function(){
                    $('#$meta_box_id #ed_toolbar').show();
                });
                $('#media-buttons a').bind('click', function(){
                    var customEditor = $(this).parents('#$meta_box_id');
                    if(customEditor.length > 0){
                        edCanvas = document.getElementById('$editor_id');
                    } else {
                        edCanvas = document.getElementById('content');
                    }
                });
            });
            </script>";

    $content = get_post_meta($post->ID, WYSIWYG_META_KEY, true);
    the_editor($content, $editor_id);
    echo "<div style='clear:both; display:block;'></div>";
}

add_action('save_post', 'wysiwyg_save_meta');
function wysiwyg_save_meta()
{

    $editor_id = WYSIWYG_EDITOR_ID;
    $meta_key = WYSIWYG_META_KEY;

    if (isset($_REQUEST[$editor_id]))
        update_post_meta($_REQUEST['post_ID'], WYSIWYG_META_KEY, $_REQUEST[$editor_id]);

}

function bufferize_custom_post_type() {
    $labels = array(
        'name' => _x( 'Bufferize', 'Bufferize', 'bufferize' ),
        'singular_name' => _x( 'Bufferize', 'Bufferize', 'bufferize' ),
        'menu_name' => __( 'Bufferize', 'bufferize' ),
        'all_items' => __( 'Alle Bufferizes', 'bufferize' ),
        'view_item' => __( 'Bufferize anzeigen', 'bufferize' ),
        'add_new_item' => __( 'Neues Bufferize', 'bufferize' ),
        'add_new' => __( 'Neues Bufferize', 'bufferize' ),
        'edit_item' => __( 'Bufferize bearbeiten', 'bufferize' ),
        'update_item' => __( 'Bufferize aktualisieren', 'bufferize' ),
        'search_items' => __( 'Suche Bufferize', 'bufferize' ),
        'not_found' => __( 'Nicht gefunden', 'bufferize' ),
        'not_found_in_trash' => __( 'Nicht gefunden im Papierkorb', 'bufferize' )
    );

    $args = array(
        'label' => __( 'bufferize', 'bufferize' ),
        'description' => __( 'Bufferize', 'bufferize' ),
        'labels' => $labels,
        'supports' => array( 'title', 'editor', 'revisions' ),
        'taxonomies' => array( ),
        'hierarchical' => false,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => false,
    );
    register_post_type( 'bufferize', $args );
}
add_action( 'init', 'bufferize_custom_post_type', 0 );


function bufferize_page() {
    _e('<h1>Bufferize</h1><p>Ändere mit Bufferize den Inhalt deiner Webseite! <br />Manche Elemente in Footer, 
    Sidebar oder Header können so beispielsweise bestens ersetzt oder entfernt werden, 
    ohne den Templatecode anpassen zu müssen. <br /><br />', 'bufferize');
    _e('<h2>Anleitung</h2>
    Suche die entsprechende Stelle auf deiner Webseite, kopiere mit F12 (Entwicklerwerkzeuge im Browser) das entsprechende 
    HTML Element und füge es ins <strong>Gesucht</strong> Feld ein. Füge anschliessend unter 
    <strong>Ersetzt</strong> den neuen Inhalt (Text oder HTML) ein oder lasse das Feld leer.</p>', 'bufferize');
}
if($_GET['post_type'] === 'bufferize' && $_SERVER['SCRIPT_NAME'] === '/wp-admin/edit.php') {
    add_action('wp_after_admin_bar_render', 'bufferize_page');
}
add_action( 'init', 'replacer', 0 );


function replacer() {




        add_filter('template_include', function($template){
            ob_start(function($buffer){
                $query = new WP_Query( array( 'post_type' => 'bufferize', 'post_status' => array('publish' ) ) );
                $posts = $query->posts;
                foreach($posts as $posty) {
                    $buffer = str_replace($posty->post_content, get_post_meta($posty->ID, 'extra-content', true), $buffer);
                }
                return $buffer;
            });
            return $template;
        }, 1, 2);



}

add_filter('admin_init','start_buffer_EN',1);
function start_buffer_EN($template) {
    ob_start('end_buffer_EN');
    return $template;
}
function end_buffer_EN($buffer) {
    return str_replace('<div id="wp-content-media-buttons" class="wp-media-buttons"><button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="content"><span class="wp-media-buttons-icon"></span> Add Media</button></div>','<h1 class="wanted-title">Gesucht</h1>', $buffer);
}
add_filter('admin_init','start_buffer_d',1);
function start_buffer_d($template) {
    ob_start('end_buffer_d');
    return $template;
}
function end_buffer_d($buffer) {
    return str_replace('<div id="wp-myeditor-media-buttons" class="wp-media-buttons"><button type="button" class="button insert-media add_media" data-editor="myeditor"><span class="wp-media-buttons-icon"></span> Add Media</button></div>','<h1 class="replacewith-title">Ersetzen durch</h1>', $buffer);
}


/*
add_filter('template_include','start_buffer_EN',1);
function start_buffer_EN($template) {
    ob_start('end_buffer_EN');
    return $template;
}
function end_buffer_EN($buffer) {
    return str_replace('<div id="wp-content-media-buttons" class="wp-media-buttons"><button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="content"><span class="wp-media-buttons-icon"></span> Add Media</button></div>','I DONT KNOW', $buffer);
}


*/
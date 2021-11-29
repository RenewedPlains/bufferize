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
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
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
        'publicly_queryable' => false,
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

add_filter('template_include','start_buffer_EN',1);
function start_buffer_EN($template) {
    ob_start('end_buffer_EN');
    return $template;
}
function end_buffer_EN($buffer) {
    return str_replace('<div class="wp-block-group__inner-container"><h2>Recent Posts</h2><ul class="wp-block-latest-posts__list wp-block-latest-posts"><li><a href="http://buffer.local/hello-world/">Hello world!</a></li>
</ul></div>','I DONT KNOW', $buffer);
}



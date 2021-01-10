<?php

 /**
  * Includes Rep landing pages list and new in Dashboard Admin
  */

 if (!defined('ABSPATH'))
     exit;
 
 if (is_admin()) {
     add_action( 'admin_menu', 'add_rep_landing_page_to_menu' );
 }


 function add_rep_landing_page_to_menu() {

     add_menu_page(
         __( 'Rep Landing Page', 'textdomain' ),
         'Rep Landing Pages',
         'manage_options',
         'rep_landing_page',
         'list_rep_landing_page_html',
         'dashicons-groups',
         7
     );

     add_submenu_page(
         'rep_landing_page',
         'Add Rep Landing Page',
         'Add Rep Landing Page',
         'manage_options',
         'add_rep_landing_page',
         'add_rep_landing_page_html'
     );
     function admin_rep_acf_form_head() {
         acf_form_head();
     }

     add_action( 'admin_init', 'admin_rep_acf_form_head' );

     function my_acf_save_post_update_title( $post_id ) {

         if( $_POST['action'] === "editpost" ) {
             return;
         }
         if( $full_name = get_field( 'full_name', $post_id) ) {
             $the_post = array (
                 'ID'           => $post_id,
                 'post_title'   => $full_name,
                 'post_name'    => sanitize_title( $full_name )
             );
             wp_update_post( $the_post );
         }

     }

     add_action( 'acf/save_post', 'my_acf_save_post_update_title', 15 );

 }

 function add_rep_landing_page_html() {

    ?><h2>Add new Rep Landing Page</h2><?php

    acf_form( array (
        'post_id'		        => 'new_post',
        'new_post'		        => array (
            'post_type'         => 'page',
            'post_status'       => 'publish',
            'page_template'     => 'RepLandingPage.php',
            'post_title'        => 'New Rep Landing Page' ,
        ) ,
        'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',
        'updated_message'       => __( "A New Landing page for rep was created", 'acf' ),
        'submit_value'		    => 'Create a new Landing Page',

    ));

 }

function list_rep_landing_page_html() {

    load_class( 'rep_landing_pages_list.class.php' );

    ?><h2>Rep Landing Pages</h2><?php

    $landing_Pages_List = new \Rep_Landing_Pages_List();
    $landing_Pages_List->prepare_items();
    $landing_Pages_List->display();

}

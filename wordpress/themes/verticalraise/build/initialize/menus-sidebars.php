<?php

register_nav_menus( array(
    'mainmenu' => __( 'Main Menu'),
    'footermenu' => __('Footer Menu'),
    'myacc' => __('My Account Menu'),
    'invitemenu' => __('Invite Menu'),
    'invitemenusingle' =>  __('Invite Menu for Landing Page'),
    'invitemenuparent' =>  __('Invite Menu Parent')
));

register_sidebar(array('name'=>'Logo',
    'id' => 'logo',
    'before_widget' => '<div class="logo">',
    'after_widget' => '</div>',
    'before_title' => '<h2 style="display:none;">',
    'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Phone Number',
    'id' => 'phone_number',
    'before_widget' => '<div class="phone_num">',
    'after_widget' => '</div>',
    'before_title' => '<h2 style="display:none;">',
    'after_title' => '</h2>',
));

register_sidebar(array('name'=>'Home Row 1 Title',
    'id' => 'home_row1title',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<h1 style="display: none;">',
    'after_title' => '</h1>',
));

register_sidebar(array('name'=>'Home Row 1 Text',
    'id' => 'home_row1texts',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<h1 style="display:none;">',
    'after_title' => '</h1>',
));

register_sidebar(array('name'=>'Home Row 2 Title',
    'id' => 'home_row2title',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<h1 style="display:none;">',
    'after_title' => '</h1>',
));

register_sidebar(array('name'=>'Home Row 2 Images',
    'id' => 'home_row2images',
    'before_widget' => '<div>',
    'after_widget' => '</div>',
    'before_title' => '<span style="display:none;">',
    'after_title' => '</span>',
));

register_sidebar(array('name'=>'Home Row 2 Lists',
    'id' => 'home_row2lists',
    'before_widget' => '',
    'after_widget' => '',
    'before_title' => '<h1 style="display:none;">',
    'after_title' => '</h1>',
));

register_sidebar(array('name'=>'Home Row 2 Button',
    'id' => 'home_row2butons',
    'before_widget' => '<div class="signup_button">',
    'after_widget' => '</div>',
    'before_title' => '<h1 style="display:none;">',
    'after_title' => '</h1>',
));

// register_sidebar(array('name'=>'Footer Main Links',
//     'id' => 'footer_main_links',
//     'before_widget' => '<div class="footernav">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2 style="display:none;">',
//     'after_title' => '</h2>',
// ));

// register_sidebar(array('name'=>'Footer Contact Us',
//     'id' => 'footer_contact_us',
//     'before_widget' => '<div class="footercontactus">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2 style="display:none;">',
//     'after_title' => '</h2>',
// ));

// register_sidebar(array('name'=>'Footer Social Links',
//     'id' => 'footer_social_links',
//     'before_widget' => '<div class="social_nav">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2 style="display:none;">',
//     'after_title' => '</h2>',
// ));

// register_sidebar(array('name'=>'Footer Copyrights',
//     'id' => 'footer_copyrights',
//     'before_widget' => '<div class="footer_copyright">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2 style="display:none;">',
//     'after_title' => '</h2>',
// ));

register_sidebar(array('name'=>'Blog Sidebar',
    'id' => 'blog_sidebar',
    'before_widget' => '<div class="sidebar_content">',
    'after_widget' => '</div>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
));

// register_sidebar(array('name'=>'Contact Sidebar',
//     'id' => 'contact_sidebar',
//     'before_widget' => '<div class="contact_sidebar_content">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2>',
//     'after_title' => '</h2>',
// ));

// register_sidebar(array('name'=>'Myaccount Sidebar',
//     'id' => 'myaccount_sidebar',
//     'before_widget' => '<div class="myaccount_sidebar_content">',
//     'after_widget' => '</div>',
//     'before_title' => '<h2>',
//     'after_title' => '</h2>',
// ));

register_sidebar(array('name'=>'Bio Title',
    'id' => 'bio_title',
    'before_widget' => '<div class="bio_content">',
    'after_widget' => '</div>',
    'before_title' => '<h1>',
    'after_title' => '</h1>',
));

register_sidebar(array('name'=>'Bio',
    'id' => 'bio',
    'before_widget' => '<div class="col span_6_of_12"><div class="single_bio">',
    'after_widget' => '</div></div>',
    'before_title' => '<h2 style="display: none;">',
    'after_title' => '</h2>',
));
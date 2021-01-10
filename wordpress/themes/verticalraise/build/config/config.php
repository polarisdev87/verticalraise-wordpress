<?php

/* --------------------
  |
  | Third Party API Credentials
  |
  /*-------------------- */

/**
 * Facebook
 */
define( '_FACEBOOK_CLIENT_ID', '243050316270970' );                           // Client ID
define( '_FACEBOOK_CLIENT_SECRET', '97588faac1bd14a91bd8ff45e63e64fa' );      // Client Secret
define( '_FACEBOOK_APP_ID', '243050316270970' );

/**
 * SendGrid
 */
define( '_SENDGRID_APIKEY', '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' ); // APIkey

/**
 * Twilio
 */
define( '_TWILIO_ACCOUNT_ID', "AC037719c74be43e6c9d6fda5cd21deaf3" );         // Account ID
define( '_TWILIO_AUTH_TOKEN', "49373418695f4059a546c5aab15a8cfc" );           // Autho Token
define( '_TWILIO_FROM_NUMBER', "+18317772924" );                              // Twilio From Number

/**
 * MailGun
 */
define( '_MAILGUN_PUBKEY', 'pubkey-54cbc3e6cb4208d64ece65bfdadaa3ba' );       // Pubkey

/**
 * Stripe
 */
define( '_STRIPE_DEV_SECRET_KEY', 'sk_test_siM606LU15EpxpeVMAlaszY7' );       // Dev Secret Key
define( '_STRIPE_DEV_PUBLISHABLE_KEY', 'pk_test_PBlrYL5GlMwuzYXkE00UGZNS' );  // Dev Publishable Key
//define( '_STRIPE_DEV_SECRET_KEY', 'sk_test_peMer0SN4Jutx7Q5UVG69fBX' );
//define( '_STRIPE_DEV_PUBLISHABLE_KEY', 'pk_test_JBqM8cV9xNQnnja9eTh2SZmr' );

define( '_STRIPE_SECRET_KEY', 'sk_live_g5oLva88B44012j3NFSLjHjM' );           // Live Secret Key
define( '_STRIPE_PUBLISHABLE_KEY', 'pk_live_7JGTDWcZZvLf6gVCCwxUB9op' );      // Live Publishable Key


/**
 * Google
 */
define( '_GOOGLE_URL_DEV_KEY', 'AIzaSyCI1F-SnWRvWa_tgQ3PJw1r7bCGrW-7FPs' );    // Dev URL Shortener API Key
define( '_GOOGLE_URL_LIVE_KEY', 'AIzaSyCI1F-SnWRvWa_tgQ3PJw1r7bCGrW-7FPs' );   // Live URL Shortener API Key
define( '_GOOGLE_API_KEY', 'AIzaSyCI1F-SnWRvWa_tgQ3PJw1r7bCGrW-7FPs' );

/**
 * google oauth
 */
define( '_GOOGLE_CLIENT_ID', '585827087782-4tbshouqc1dre9ocvl5shi2ell4nqc4p.apps.googleusercontent.com' );
define( '_GOOGLE_CLIENT_SECRET', '45cV4gwAUDk05XrCxxYxl6Mx' );
define( '_GOOGLE_REFRESH_TOKEN', '1/3GXj9lApwWzn9dNvGmxjFCho32-_6uKyqd-Z6lzoX4M' );

/**
 * Google Analytics
 */
define( '_GOOGLE_UA_CODE', 'UA-116573233-1' );                     // Google Analytics Code
define( '_GOOGLE_AWC_CODE', 'AW-739641915' );                      // Google Adwords Conversion Code
define( '_GOOGLE_AWC_CODE2', 'AW-739641915/7Lc6CP7w_J8BELuU2OAC' ); // Google Adwords Conversion Code 2

define( '_GOOGLE_UA_CODE_DEV', 'UA-151520786-1' );                     // Dev Google Analytics Code


/**
 * EmailListVerify
 */
define( '_EMAIL_LIST_VERIFY_API_KEY', '6c8O7waP3ldA1kMCausWv' ); // API Key

/**
 * TheChecker.co
 */
define( '_THE_CHECKER_API_KEY', 'd3cb3ea13b6817ef4f65da12f726eee69b8702c498f4c3b2a70ddedaf9630bad' ); // API Key

/**
 * NeverBounce
 */
define( '_NEVERBOUNCE_API_DEV_KEY', 'secret_bfe0c26b9eb815e0f76f1b7491d68c8b' );
define( '_NEVERBOUNCE_API_LIVE_KEY', 'secret_16bf31a035edebd12ac2f9160f4bb5e5' );

/**
 * Envoyer
 */
define( '_ENVOYER_HEARTBEAT_ENDPOINT', 'http://beats.envoyer.io/heartbeat/cHyU5PFAj4yWPJ7' );     // Envoyer Endpoint URL
define( '_ENVOYER_HEARTBEAT_ENDPOINT_DEV', 'http://beats.envoyer.io/heartbeat/cHyU5PFAj4yWPJ7' ); // Envoyer Endpoint URL

/**
 * Cloudsponge
 */
define( '_CLOUDSPONGE_API_KEY', 'oKLp27laIsh6X460GLMwHw' );

/**
 * ClouldFlare
 */
define( '_CLOUDFLARE_ZONE', '0e39739fc8af8a1da5609046864200e9' );
define( '_CLOUDFLARE_AUTH_KEY', 'b57207212423ccbb4bcf9801bae6b44cb55fc' );
define( '_CLOUDFLARE_AUTH_EMAIL', 'info@wefund4u.com' );


/* --------------------
  |
  | Global Variables
  |
  /*-------------------- */

define( '_SITE_NAME', 'Vertical Raise');

// Theme Related
define( '_THEME_FOLDER', 'VR' );
define( '_THEME_PATH', '/wp-content/themes/' . _THEME_FOLDER );
define( '_THEME_IMAGES_PATH', '/assets/images' );

// Fundraising
define( '_PARTICIPATION_GOAL', 500 );

// Mailing
define( '_MAILGUN', '0' ); // 1 for on, 0 for off
define( '_CHECK_EXTERNAL_EMAIL_VALIDATOR', true ); // Email validator API on or off
define( '_DEFAULT_FROM_NAME', 'Wefund4u' ); // Default From Name for SMS & Email
define( '_EMAIL_INVITE_LIMIT', 200 ); // Max number of emails that can be sent per submit
define( '_EMAIL_PARTICIPANT_INVITE_LIMIT', 100 ); // Max number of emails that can be sent by participant
define( '_SUGGESTED_EMAIL_AMOUNT', 20); // Suggested number of emails for participants to send

// SMS
define( '_SMS_INVITE_LIMIT', 50 ); // Max number of phone numbers that can be texted per submit

define( '_ENCRYPTION_KEY', 'D83E384614FE752DD45CAB423A79E' ); // For Encryption protocols
define( '_RUN_DB_SETUP', '1' ); // 1 for on, 0 for off

// Social Media
define( '_SOCIAL_MEDIA_FACEBOOK_PAGE_URL', 'https://www.facebook.com/VerticalRaise/' );
define( '_SOCIAL_MEDIA_GOOGLE_PLUS_URL', 'https://plus.google.com/u/3/107542633069038923236' );
define( '_SOCIAL_MEDIA_INSTAGRAM_URL', 'https://www.instagram.com/verticalraise/' );
define( '_SOCIAL_MEDIA_TWITTER_URL', 'https://twitter.com/VerticalRaise' );
define( '_SOCIAL_MEDIA_LINKEDIN_URL', 'https://www.linkedin.com/company/18562617/' );

// Heartbeat
define( '_HEARTBEAT_ENDPOINT', _ENVOYER_HEARTBEAT_ENDPOINT );
define( '_HEARTBEAT_ENDPOINT_DEV', _ENVOYER_HEARTBEAT_ENDPOINT_DEV );

// Short URLs
define( '_SHORTURL_LOCAL', 'http://shorturl.local.wordpress.test/' );
define( '_SHORTURL_DEV', 'https://shorturl.verticalraise-dev.com/' );
define( '_SHORTURL', 'https://vraise.org/' );

define( '_SHORTURL_BASE_LOCAL', 'shorturl.local.wordpress.test' );
define( '_SHORTURL_BASE_DEV', 'shorturl.verticalraise-dev.com' );
define( '_SHORTURL_BASE', 'vraise.org' );

// Cron Emails
define( '_ENDED_CAMPAIGNS_TO_EMAIL', 'endedcampaigns@verticalraise.com' );
define( '_CRON_FROM_EMAIL', 'support@verticalraise.com' );

// Transactional Emails
define( '_TRANSACTIONAL_FROM_EMAIL', 'support@verticalraise.com' );

// Global to Emails
define( '_ADMIN_TO_EMAIL', 'support@verticalraise.com' );
define( '_SUPPORT_TO_EMAIL', 'support@verticalraise.com' );

// Email Signature
define( '_SIGNATURE_OFFICE_PHONE_NUMBER', '(888) 853-0355' );
define( '_SIGNATURE_FAX_NUMBER', '(208) 625-2004' );
define( '_SIGNATURE_EMAIL', 'support@verticalraise.com' );

// Payment Processing
define( '_CHECK_BY_MAIL_PAYEE', 'ONLINE TEAM FUNDRAISER' );

// Contact Form 7
define('_DEV_BAND_FUNDRAISER_CF7_SC', '[contact-form-7 id="54206" title="Band Landing Page"]');
define('_PROD_BAND_FUNDRAISER_CF7_SC', '[contact-form-7 id="117545" title="Band Landing Page"]');

//Page Template Subdir
// defining the sub-directory so that it can be easily accessed from elsewhere as well.
define( 'WPSE_PAGE_TEMPLATE_SUB_DIR', 'page-templates' );

define('SPORT_SCOPE_FUNDRAISER_APPROVED_EMAIL', 'support@verticalraise.com');
define('SPORT_SCOPE_FUNDRAISER_17_DAYS_LEFT', 'vr@sportscope.com');
// TEST
//define('_TEST_DEFINE', '');
//define('_TEST_EMPTY', '');
//define('_TEST_VALUE', 'value');

<?php
/**
 * Check COOKIE statue in Browser.
 */

namespace classes\app\cookie;

class Check_Cookie
{
   
    public function __construct() {
       
    }

    public function display() {
       echo '
            <div class="overlay-cookie">
                <div class="cookie_alert">
                    <div class="content" >
                        <h1>Please enable cookies</h1>
                        <span>To be able to use our services, register and login you must have cookies enabled. Please go into your browser settings and enable cookies.</span>
                    </div>
                    <button class="custom_button cookie_alert_close">Close</button>
                </div>
            </div>
            <script>
                function supportsCookies() {
                    try {
                        // Create cookie
                        document.cookie = "cookietest=1";
                        var ret = document.cookie.indexOf("cookietest=") != -1;
                        // Delete cookie
                        document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
                        return ret;
                    } catch (e) {
                        return false;
                    }
                }

                jQuery(document).ready(function () {
                    //check cookie enabled in browser        
                    var checkCookie = supportsCookies();
                    if (!checkCookie) {
                        jQuery(".overlay-cookie").show();
                    } else {
                        jQuery(".overlay-cookie").hide();
                    }

                    jQuery(".cookie_alert_close").click(function () {
                        jQuery(".overlay-cookie").hide();
                    })
                })


            </script>';
    }

    
}
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/*

define('_NEVERBOUNCE_API_DEV_KEY', 'public_d271988ebb7a1ef08fd0e3ef2041e080');
?>
<html>
    <head>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.js"></script>
    </head>
    <body>
        <form>

            <input type="text" name="firstname" id="firstname" placeholder="First Name" autocomplete="off"/>
            <input type="text" name="lastname" id="lastname" placeholder="Last Name" autocomplete="off"/>
            <input type="email" name="email" id="email_id" placeholder="Email" autocomplete="off"/>
            <!--<textarea  name="email" id="email_id" placeholder="Email"></textarea>-->


        </form>

        <script type="text/javascript">

            _NBSettings = {

                /**
                 * User's public api key; This is available from the apps page.
                 */

/*
                apiKey: '<?php echo _NEVERBOUNCE_API_DEV_KEY ?>',
                apiOnly: false,
                autoFieldHookup: true,

            };


        </script>


        <script>
            $(window).load(function () {
            
            */

                /*
                 // Get the DOM node
                 var field = document.querySelector('#email_id');
                 
                 // Register field with the widget and broadcast nb:registration event
                 // If ommitted the second argument will assume the value of true
                 _nb.fields.registerListener(field, true);
                 */


                /*
                 $("#email_id").on("blur", function () {
                 
                 _nb.api.getValidatePublic($(this).val(),
                 function (result) {
                 // Returns a Result object
                 console.log("support@neverbounce.com", result)
                 },
                 function (err) {
                 // Returns error message as string
                 console.log(err)
                 }
                 )
                 })
                 */


/*


                document.querySelector('body').addEventListener('nb:registered', function (event) {

                    // Get field using id from registered event
                    let field = document.querySelector('[data-nb-id="' + event.detail.id + '"]');

                    // Handle clear events; i.e. hide feedback
                    field.addEventListener('nb:clear', function (e) {
                        // Do stuff when input changes or when API responds with an error
                        console.log("AAAA")
                    });

                    // Handle loading status (API request has been made)
                    field.addEventListener('nb:loading', function (e) {
                        // Do stuff while waiting on API response
                        console.log("BBBB")
                    });

                    // Handle results (API call has succeeded)
                    field.addEventListener('nb:result', function (e) {
                        console.log("CCCC", e.detail.result)
                        // Check the result
                        if (e.detail.result.isError()) {
                            // Get error message
                            var error = e.details.error;
                            // If an error occurs we suggest treating the email as an unknown and allowing them to continue.
                        } else if (e.detail.result.is(_nb.settings.getAcceptedStatusCodes())) {
                            // Do stuff for good email
                        } else {
                            // Do stuff for bad email
                        }
                    });

                    // Handle soft results (fails regex; doesn't bother making API request)
                    field.addEventListener('nb:soft-result', function (e) {
                        // Do stuff when input doesn't even look like an email (i.e. missing @ or no .com/.net/etc...)
                        console.log("DDDD")
                    });
                });

            })
        </script>

        <script type="text/javascript" src="https://cdn.neverbounce.com/widget/dist/NeverBounce.js"></script>

    </body>
</html>

<?php */
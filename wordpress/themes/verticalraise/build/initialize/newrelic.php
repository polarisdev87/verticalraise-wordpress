<?php

// Set the app name
if (extension_loaded('newrelic')) {
    $name = 'VerticalRaise' . _SERVER_TYPE;
    newrelic_set_appname($name, "", true);
}
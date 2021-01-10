<?php

namespace classes\app\loaders;

class Notification_Template_Loader
{
    private $path; // Template directory path
    
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Loads the html email template for the given type.
     * @param string $type      The email template to load
     * @param string $extension The file extension
     * @return mixed Returns email template html if found, otherwise false
     */
    public function load_template($type, $extension) {
        $file = $this->path . $type . $extension;

        if ( file_exists($file) ) {
            return file_get_contents($file);
        } else {
            return false;
        }
    }
    
    /**
     * Takes the html template and mass replaces all macros with the array key-value pairs supplied.
     * @param  string $template_html Literally the entire html template
     * @return string $content       The html email template with all macros replaced with the key-value pairs.
     */
    public function load_content($template_html, $args) {
        $pattern = '[%s]';
        
        foreach ( $args as $key => $val ) {
            $varMap[sprintf($pattern, $key)] = $val;
        }

        $content = strtr($template_html, $varMap);
        
        return $content;
    }

}
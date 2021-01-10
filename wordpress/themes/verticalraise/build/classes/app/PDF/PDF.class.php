<?php

namespace classes\app\PDF;

/**
 * The main PDF class. The class takes HTML input and outputs a PDF file.
 * @requires package HTML2PDF
 */
require_once( get_template_directory() . '/html2pdf/vendor/autoload.php' );

class PDF
{
    
    /**
     * The HTML2PDF object.
     * @var string $html2pdf 
     */
    protected $html2pdf;
    
    /**
     * Location of PDF templates.
     * @var string $pdfs_path
     */
    protected $pdfs_path;
    
    /**
     * Class constructor.
     */
    protected function __construct() {

        $this->html2pdf  = new \HTML2PDF('P','A4','en', true, 'UTF-8', array(15, 15, 15, 15));

        $this->pdfs_path = get_template_directory() . '/pdfs/templates/';
        
    }

    /**
     * Loads the html pdf template for the given type.
     * @param  string $type     The pdf template to load
     * @return mixed  Returns   pdf template html if found, otherwise false
     */
    protected function load_template($type) {
        $file = $this->pdfs_path . $type . '.pdf.html';
        
        if ( file_exists($file) ) {
            return file_get_contents($file);
        } else {
            return false;
        }
    }
    
    /**
     * Takes the html template and mass replaces all macros with the array key-value pairs supplied.
     * @param   string $template_html   Literally the entire html template
     * @return  string $content         The html email template with all macros replaced with the key-value pairs.
     */
    protected function load_content($template_html, $args) {
        $pattern = '[%s]';
        
        foreach ( $args as $key => $val ) {
            $varMap[sprintf($pattern, $key)] = $val;
        }

        $content = strtr($template_html, $varMap);
        
        return $content;
    }
    
    /**
     * Prep the content for output by enclosing it within <page></page>.
     * @param   string $_content The entire content string
     * @return  string $content  The entire content string wrapped in <page></page>
     */
    protected function prep_content($_content) {
        $content = "<page>{$_content}</page>";
        
        return $content;
    }
    
    /**
     * Output the PDF for the user.
     * @param string $content   The PDF content.
     * @param string $file_name The file name to give the PDF.
     * @param bool   $print     Prompt the user to print the PDF.
     */
    protected function output($content, $file_name, $print = false, $font = null) {
        
        // Write the HTML to the PDF
        $this->html2pdf->WriteHTML($content);
        
        // Prompt the user to print the PDF
        if ( $print == true ) {
            $this->html2pdf->pdf->IncludeJS("print(true);"); 
        }
        
        // Set a default font
        if ( !empty($font) ) {
            $this->html2pdf->setDefaultFont($font);
        }

        // Output the PDF
        $this->html2pdf->Output($file_name);
        die();
    }

}
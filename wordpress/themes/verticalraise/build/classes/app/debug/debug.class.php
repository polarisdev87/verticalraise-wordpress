<?php

namespace classes\app\debug;

class Debug
{

    /**
     * @var bool $init Turns the class on or off.
     */
    private $init;
    
    /**
     * Class constructor.
     */
    public function __construct($init) {
        $this->init = $init;
    }
    
    /**
     * Print debug info to screen.
     * @param any    $variable The variable being passed through to output
     * @param string $name     The name of the variable
     * @param string $type     The type of variable: variable or array
     */
    public function debug($variable, $name = '') {
        
        // Debug must be enabled
        if ( $this->init == false ) {
            return false;
        }

        // Start the output ============================
        echo "<div style='background-color: #ffffff;'>";
        
        if ( is_array($variable) ) {
            $this->output_array($variable, $name);          // Array
        } else {
            $this->output_variable($variable, $name);       // Variable
        }
        
        echo "</div>";
        // End the output ==============================
    }
    
    private function output_array($array, $name) {
        echo "{$name}:";
        echo "<br>";
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
    
    private function output_variable($variable, $name){
        echo "{$name}: {$variable}<br>";
    }
    
}
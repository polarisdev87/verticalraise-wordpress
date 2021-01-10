<?php

namespace classes\app\System_configuration;

// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

use classes\app\System_configuration\Load_Json_File;

class Config_Comparations
{

    private $loadJson;
    private $jsonData;

    public function __construct() {
        $this->loadJson = new Load_Json_File;
        $this->load();
    }

    public function load() {
        $data = array();
        foreach ( $this->loadJson->load_json() as $key => $json_data ) {
            foreach ( $json_data as $arr_key => $val ) {
                $data += $val;
            }
        }

        $this->jsonData = $data;
    }

    public function check( $key, $value, $type = null ) {
        //system
        if ( isset( $type ) && !empty( $type ) ) {
            if ( $type == "status" ) {
                if ( isset( $this->jsonData[$key] ) && $this->jsonData[$key][0] == $value ) {
                    return true;
                }
            } else if ( $type == "version" ) {
                if ( isset( $this->jsonData[$key] ) && $this->jsonData[$key][1] == $value ) {
                    return true;
                }
            }
            return false;
        } else {
            if ( $key == 'rewrite_rules' ) {
                if ( isset( $this->jsonData[$key] ) && json_encode( $this->jsonData[$key] ) == $value ) {
                    return true;
                }
            }
            if ( isset( $this->jsonData[$key] ) && $this->jsonData[$key] == $value ) {
                return true;
            }
            return false;
        }
    }

}

<?php

namespace classes\app\System_configuration;

// Get specific libraries: http://sg.php.net/manual/en/function.extension-loaded.php

class Load_Json_File
{

    /** Folder that holds configuration files * */
    public $folder = '/data/';

    /** List the configuration files to load * */
    public $config;
    public $jsonData;

    public function __construct() {
        $this->config['system'] = 'system_configuration.json';
        $this->config['plugin'] = 'plugins.json';
    }

    /**
     * Load the json configuration files.
     * @return array
     */
    public function load_json() {
        $data = $this->compile( $this->config );
        return $data;
    }

    public function plugin_config_load() {
        $data = $this->load_json();
        return $data['plugin'];
    }

    /**
     * Load the configuration file.
     */
    private function load( $file ) {
        $file = $this->get_file_path( $file );
        $data = $this->decode( $file );

        return $data;
    }

    /**
     * Get the file path.
     * @return string
     */
    private function get_file_path( $file ) {
        return get_template_directory() . $this->folder . $file;
    }

    /**
     * Decode the json file.
     * @return json
     */
    private function decode( $file ) {
        $contents = file_get_contents( $file );
        $json     = json_decode( $contents, true );

        return $json;
    }

    /**
     * Compile the data into an array.
     * @return array
     */
    private function compile() {
        $data = array();
        foreach ( $this->config as $key => $config ) {
            $data[$key] = $this->load( $config );
        }

        return $data;
    }

}

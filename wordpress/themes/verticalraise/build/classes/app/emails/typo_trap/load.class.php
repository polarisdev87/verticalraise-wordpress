<?php

namespace classes\app\emails\typo_trap;

class Load
{
    ## TODO ##
    ## Broadbend: @bendbroadband.com, @bendcable.com, or @chamberscable.com.
    ## Pioneer: @pld.com
    ## Yelcot: @yelcot.net
    ## United.net
    ## Wildblue.net
    ## Exede.net
    ## Live.com
    ## Msn.com
    ## Basicisp: @basicisp.net (Canada)
    ## Isp.com: @isp.com
    ## Hughes.net: @hughes.net
    ## Optimum.net
    ## More providers: https://www.practicalecommerce.com/32-Leading-Internet-Service-Providers
    ## Generate typos: https://www.namewest.com/simple-typo/?q=facebook.com&missed=on&swap=on&hwrong=on&double=on&doubleh=on&vwrong=on&alike=on&vowel=on

    /**
     * List of provider typos.
     */
    private $providers = [
        "btinternet",
        "cableone",
        "centurylink",
        "charter",
        "comcast",
        "earthlink",
        "facebook",
        "frontier",
        "gmail",
        "hotmail",
        "icloud",
        "netzero",
        "ntworld",
        "outlook",
        "prodigy",
        "roadrunner",
        "suddenlink",
        "tiscali",
        "verizon",
        "windstream",
        "yahoo",
        "yandex"
    ];

    /**
     * Load the data files.
     * @param string $type
     *
     * @return array
     */
    public function load( $type ) {
        switch ( $type ) {
            case "phrases":
                return $this->load_providers();
                break;
            case "exact":
                return $this->load_global_exact_list();
                break;
            case "address_roles":
                return $this->load_role_address();
                break;
            case "blocking_domains":
                return $this->blocking_domains_list();
                break;
        }
    }

    /**
     * Load a global list of "exact" match typos.
     * @return array
     */
    private function load_global_exact_list() {
        include( get_template_directory() . "/classes/app/emails/typo_trap/data/global_exact_list.php" );
        return $data;
    }

    /**
     * Load specific provider "phrase" match typos.
     * @return array
     */
    private function load_providers() {
        $array = array();

        foreach ( $this->providers as $provider ) {
            include( get_template_directory() . "/classes/app/emails/typo_trap/data/providers/{$provider}.provider.php" );
            $array = array_merge( $array, $data );
        }

        return $array;
    }

    private function load_role_address() {
        include( get_template_directory() . "/classes/app/emails/typo_trap/data/sendgrid_role_address_lists.php" );
        return $data;
    }

    private function blocking_domains_list() {
        include( get_template_directory() . "/classes/app/emails/typo_trap/data/blocking_domain_list.php" );
        return $data;
    }

}

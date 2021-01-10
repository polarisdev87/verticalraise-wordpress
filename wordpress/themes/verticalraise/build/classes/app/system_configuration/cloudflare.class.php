<?php

namespace classes\app\System_configuration;

use classes\app\System_configuration\Config_Comparations;
use classes\app\System_configuration\Load_Json_File;

class Cloudflare
{

    private $domain_url;
    private $api_get_urls;

    public function __construct() {
        $this->compare    = new Config_Comparations;
        $this->load_json  = new Load_Json_File;
        $this->domain_url = 'https://api.cloudflare.com/client/v4/zones/' . _CLOUDFLARE_ZONE;

        $this->api_get_urls = array(
            'dns_records'              => '/dns_records/?per_page=50',
            'pagerules'                => '/pagerules?order=status&direction=asc&match=all',
            'challenge_passage'        => '/settings/challenge_ttl',
            'waf'                      => '/settings/waf',
            'browser_check'            => '/settings/browser_check',
            'security_level'           => '/settings/security_level',
            'rate_limits'              => '/rate_limits',
            'cache_level'              => '/settings/cache_level',
            'browser_cache_ttl'        => '/settings/browser_cache_ttl',
            'always_online'            => '/settings/always_online',
            'development_mode'         => '/settings/development_mode',
            'mobile_redirect'          => '/settings/mobile_redirect',
            'rocket_loader'            => '/settings/rocket_loader',
            'mirage'                   => '/settings/mirage',
            'brotli'                   => '/settings/brotli',
            'viewer'                   => '/amp/viewer',
            'polish'                   => '/settings/polish',
            'minify'                   => '/settings/minify',
            'railguns'                 => '/railguns',
            'privacy_pass'             => '/settings/privacy_pass',
            'ssl'                      => '/settings/ssl',
            'ssl_verification'         => '/ssl/verification/?retry=true',
            'always_use_https'         => '/settings/always_use_https',
            'security_header'          => '/settings/security_header',
            'tls_client_auth'          => '/settings/tls_client_auth',
            'min_tls_version'          => '/settings/min_tls_version',
            'opportunistic_encryption' => '/settings/opportunistic_encryption',
            'opportunistic_onion'      => '/settings/opportunistic_onion',
            'tls_1_3'                  => '/settings/tls_1_3',
            'automatic_https_rewrites' => '/settings/automatic_https_rewrites',
            'ipv6'                     => '/settings/ipv6',
            'websockets'               => '/settings/websockets',
            'pseudo_ipv4'              => '/settings/pseudo_ipv4',
            'ip_geolocation'           => '/settings/ip_geolocation',
            'response_buffering'       => '/settings/response_buffering',
            'true_client_ip_header'    => '/settings/true_client_ip_header',
            'ip_block'                 => '/custom_pages/ip_block',
            'waf_block'                => '/custom_pages/waf_block',
            '500_errors'               => '/custom_pages/500_errors',
            '1000_errors'              => '/custom_pages/1000_errors',
            'always_online_page'       => '/custom_pages/always_online',
            'basic_challenge'          => '/custom_pages/basic_challenge',
            'waf_challenge'            => '/custom_pages/waf_challenge',
            'country_challenge'        => '/custom_pages/country_challenge',
            'under_attack'             => '/custom_pages/under_attack',
            'email_obfuscation'        => '/settings/email_obfuscation',
            'server_side_exclude'      => '/settings/server_side_exclude',
            'hotlink_protection'       => '/settings/hotlink_protection'
        );
    }

    public function get_response( $url, $param ) {
        if ( $param == 'railguns' ) {
            $url = 'https://api.cloudflare.com/client/v4/railguns/';
        }
        if ( $param == 'dns_records' || $param == 'pagerules' ) {
            $url = $this->domain_url . $this->api_get_urls[$param];
        }

        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "GET",
                CURLOPT_HTTPHEADER     => array(
                    "Content-Type:application/json",
                    "X-Auth-Key:" . _CLOUDFLARE_AUTH_KEY,
                    "X-Auth-Email:" . _CLOUDFLARE_AUTH_EMAIL
                )
            )
        );

        $_response = curl_exec( $curl );
        $response  = json_decode( $_response );

        if ( $response->success ) {
            switch ( $param ) {
                case 'dns_records':
                case 'pagerules':
                    return $response->result;
                    break;
                case 'waf':
                case 'browser_check':
                case 'always_online':
                case 'development_mode':
                case 'rocket_loader':
                case 'mirage':
                case 'brotli':
                case 'privacy_pass':
                case 'railguns':
                case 'ssl':
                case 'tls_client_auth':
                case 'opportunistic_encryption':
                case 'ipv6':
                case 'websockets':
                case 'pseudo_ipv4':
                case 'ip_geolocation':
                case 'always_use_https':
                case 'opportunistic_onion':
                case 'tls_1_3':
                case 'automatic_https_rewrites':
                case 'response_buffering':
                case 'true_client_ip_header':
                case 'email_obfuscation':
                case 'server_side_exclude':
                case 'hotlink_protection':
                    if ( !empty( $response->result ) && $response->result->value == "on" )
                        return 'true';
                    break;
                case 'ssl_verification':
                    if ( !empty( $response->result ) ) {
                        return json_encode( $response->result );
                    }
                    break;

                case 'security_header':
                    if ( !empty( $response->result ) && $response->result->value->strict_transport_security->enabled )
                        return 'true';
                    break;
                case 'min_tls_version':
                    if ( !empty( $response->result ) )
                        return $response->result->value;
                    break;
                case 'security_level':
                case 'cache_level':
                    if ( !empty( $response->result->value ) )
                        return $response->result->value;
                    break;

                case 'challenge_passage':
                case 'browser_cache_ttl':

                    if ( !empty( $response->result->value ) && $response->result->value > 0 )
                        return $response->result->value;
                    break;

                case 'mobile_redirect';

                    if ( isset( $response->result->value ) && $response->result->value->status == "on" )
                        return 'true';
                    break;

                case 'rate_limits';

                    if ( $response->result_info->total_count > 0 )
                        return $response->result_info->total_count;
                    break;
                case 'viewer';
                    if ( $response->result->enabled )
                        return 'true';
                    break;
                case 'polish';
                    if ( !empty( $response->result->value ) && $response->result->value != "off" )
                        return $response->result->value;
                    break;
                case 'minify';
                    return json_encode( $response->result->value );
                    break;
                case 'ip_block':
                case 'waf_block':
                case '500_errors':
                case '1000_errors':
                case 'always_online_page':
                case 'basic_challenge':
                case 'waf_challenge':
                case 'country_challenge':
                case 'under_attack':
                    if ( !empty( $response->result ) && $response->result->url != NULL )
                        return $response->result->url;
                    break;
            }

            return 'false';
        }
        return 'false';
    }

    public function check_cloudflare_api( $param ) {
        $url   = $this->domain_url . $this->api_get_urls[$param];
        $value = $this->get_response( $url, $param );
        return $this->value_wrapper( $param, $value );
    }

    /**
     * Check Dns records
     * @param string $param
     * @return string $param, array $response, array $config_dns
     */
    public function check_cloudflare_dns_records( $param ) {
        // get response
        $url      = $this->domain_url . $this->api_get_urls[$param];
        $response = $this->get_response( $url, $param );
        // get json
        $config_json = $this->load_json->load_json();
        $config_dns  = $config_json['system']['cloudflare']['dns'];

        return $this->dns_records_wrapper( $param, $response, $config_dns );
    }
    
    public function check_cloudflare_page_rules( $param ) {
        $url      = $this->domain_url . $this->api_get_urls[$param];
        $response = $this->get_response( $url, $param );
        return $this->page_rules_wrapper( $param, $response );
    }
    
    private function page_rules_wrapper( $param, $response ) {
        $html = '<table class="dns_records ">';
        $html .= '<tr>';
        $html .= '<td>Priority</td>';
        $html .= '<td>Url</td>';
        $html .= '<td>Action</td>';
        $html .= '<td>Active Status</td>';
        $html .= '</tr>';
        $n = 0;
        foreach ( $response as $item ) {
            $n++;
            $html  .= '<tr>';
            $html  .= '<td>' . $n . '</td>';
            $html  .= '<td>' . $item->targets[0]->constraint->value . '</td>';
            $html  .= '<td>' . $item->actions[0]->id . ": " . $item->actions[0]->value . '</td>';
            $html  .= '<td>' . $item->status . '</td>';
            $html  .= '<tr>';
        }
        
        $html .='</table>';
        return $html;
    }

    /**
     * Display DNS Records table
     * @param type $param
     * @param type $response
     * @param type $config_dns
     * @return string
     */
    private function dns_records_wrapper( $param, $response, $config_dns ) {
        $html = '<table class="dns_records ">';
        $html .= '<tr>';
        $html .= '<td>Type</td>';
        $html .= '<td>Name</td>';
        $html .= '<td>Content</td>';
        $html .= '<td>Zone Name</td>';
        $html .= '</tr>';
        foreach ( $response as $record ) {
            $res_array = array(
                'type'      => $record->type,
                'name'      => $record->name,
                'content'   => $record->content,
                'zone_name' => $record->zone_name,
            );
            $match     = false;
            foreach ( $config_dns as $config_record ) {
                if ( $res_array['type'] == $config_record['type'] && $res_array['name'] == $config_record['name'] &&  $res_array['content'] == $config_record['content'] ) {
                    $match = true;
                }
            }
            $class = ($match) ? "expected" : "unexpected";
            $html  .= '<tr>';
            $html  .= '<td class="' . $class . '">' . $res_array['type'] . '</td>';
            $html  .= '<td class="' . $class . '">' . $res_array['name'] . '</td>';
            $html  .= '<td class="' . $class . '">' . $res_array['content'] . '</td>';
            $html  .= '<td class="' . $class . '">' . $res_array['zone_name'] . '</td>';
            $html  .= '<tr>';
        }
        $html .= '</table>';
        return $html;
    }

    private function ssl_verification_records_wrapper( $data ) {
        $html = '<table class="ssl_verification">';
        $html .= '<tr>';
        $html .= '<td width="10%">Cetification Status</td>';
        $html .= '<td width="10%">Signature</td>';
        $html .= '<td width="25%">Record Name</td>';
        $html .= '<td width="30%">Record Target</td>';
        $html .= '<td width="10%">Verification Status</td>';
        $html .= '<td width="10%">Verification Type</td>';
        $html .= '</tr>';

        foreach ( $data as $item ) {
            $html .= '<tr>';
            $html .= '<td width="10%">' . $item->certificate_status . '</td>';
            $html .= '<td width="10%">' . $item->signature . '</td>';
            $html .= '<td width="25%">' . $item->verification_info->record_name . '</td>';
            $html .= '<td width="35%">' . $item->verification_info->record_target . '</td>';
            $html .= '<td width="10%">' . $item->verification_status . '</td>';
            $html .= '<td width="10%">' . $item->verification_type . '</td>';
            $html .= '<tr>';
        }
        $html .= '</table>';
        return $html;
    }


    /**
     * Display compared value
     * @param type $key
     * @param type $value
     * @return string
     */
    private function value_wrapper( $key, $value ) {

        if ( $this->compare->check( $key, $value ) ) {
            $class = "expected";
        } else {
            $class = "unexpected";
        }

        // Special load value
        switch ( $key ) {
            case 'ssl_verification' :
                $data = json_decode( $value );
                if ( $data ) {
                    return $this->ssl_verification_records_wrapper( $data );
                } else {
                    return '<span class="checkmark ' . $class . '" style="font-weight:900">&#x2713; </span> <span class="' . $class . '" >' . $value . '</span>';
                }
                break;
            case '':
                break;
            default:
                return '<span class="checkmark ' . $class . '" style="font-weight:900">&#x2713; </span> <span class="' . $class . '" >' . $value . '</span>';
        }
    }

}

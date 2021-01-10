<?php

/**
 * Get fundraiser ended date.
 */

namespace classes\app\fundraiser;

class Fundraiser_Ended {

    private $fundraiser_Id;             // Fundraiser ID

    public function __construct( $f_id ) {
        $this->fundraiser_Id = $f_id;
    }

    /**
     * Get Fundraiser Ended Date.
     * @return string
     */
    public function get_fundraiser_enddate() {

	    if ( $this->f_enddate() - $this->current_date() <= 0 ) {
		    if ( $this->is_sport_scope_integrated() ) {
			    if ( $this->check_end_ssi() ) {
				    return '<h5>Campaign Ended</h5>';
			    } else {
				    return '';
			    }
		    }
	    }

        $date_diff = ($this->f_enddate() - $this->current_date()) / ( 60 * 60 * 24 );

        if ( $date_diff > 0 ) {
            $dayleft = ceil($date_diff);
            if ( $dayleft < 3 ) {
                return "<h5 id=\"enddate_counter\" data-enddate=\"{$this->f_enddate()}\">Campaign Ends soon</h5>";
            } else {
                return "<h5><b>" . $dayleft . "</b> days left</h5>";
            }
        } else {
            $dayleft = ceil($date_diff);
            if ( $dayleft == 0 ) {
                return '<h5>Campaign Ended yesterday</h5>';
            } else {
                return '<h5>Campaign Ended</h5>';
            }
        }
    }

    public function check_end() {
	    if ( $this->is_sport_scope_integrated() && $this->is_in_ssi_extra_period() ) {
		    return $this->check_end_ssi();
	    }

		$is_ended = $this->f_enddate() - $this->current_date();
		$ended = ( $is_ended <= 0 ) ? true : false;

        return $ended;
    }

	public function check_normal_end() {
		$ended = ( $this->f_enddate() - $this->current_date() <= 0) ? true : false;
		return $ended;
	}

	public function is_sport_scope_integrated() {
		return get_post_meta( $this->fundraiser_Id, 'sport_scope_integrated', true );
	}

	public function check_end_ssi() {
		//$ended = ( $this->f_secondary_end_date() - $this->current_date() <= 0 ) ? true : false;

		$is_ended = $this->f_secondary_end_date() - $this->current_date();
		$ended = ( $is_ended <= 0 ) ? true : false;

		return $ended;
	}

	public function is_in_ssi_extra_period(){

    	if( $this->is_sport_scope_integrated() && $this->f_enddate() - $this->current_date() <= 0 && $this->f_secondary_end_date() - $this->current_date() > 0){
    		return true;
	    }
	    return false;
	}

	public function f_secondary_end_date() {
		return strtotime( get_post_meta( $this->fundraiser_Id, 'secondary_end_date', true ), current_time( 'timestamp' ) );
	}

    public function match_enddate($cron_date = null) {
        if(!empty($cron_date)) {
            $matched = ( $this->f_enddate() - $this->pick_date($cron_date) == 0) ? true : false;
        } else {
            $matched = ( $this->f_enddate() - $this->current_date() == 0) ? true : false;
        }

        return $matched;
    }

    public function f_enddate() {
        return strtotime(get_post_meta($this->fundraiser_Id, 'end_date', true), current_time('timestamp'));
    }

    public function current_date() {
        return strtotime(current_time("Ymd", 0));
    }

    public function pick_date($date) {
        return strtotime($date, current_time('timestamp'));
    }

}

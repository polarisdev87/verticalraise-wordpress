<?php


namespace classes\app\fundraiser;

use classes\app\fundraiser\Fundraiser_Media;
use classes\app\stripe\Stripe_Form;
use classes\models\tables\Donation_Comments;
use classes\app\sidebar\Sidebar;
use classes\models\tables\Subgroups;

class Fundraiser
{
    /**
     * @var int $id Fundraiser Id
     * @var string $title Fundraiser Title
     */
    protected $id;
    protected $title;
    protected $image;
    protected $stripeConnect;
    protected $accountId;
    protected $coachCode;
    protected $ourFee;
    protected $showProgressbar;
    protected $goal;
    protected $raised;
    protected $teamName;
    protected $participants;
    protected $author;
    protected $supporters;
    protected $partPercentile;

    public static function getFundraiser( $id )
    {
        $fundraiser = new Fundraiser( $id );
        return $fundraiser;
    }

    public function __construct( $id )
    {
        $this->setId($id);
    }

    public function setId( $id ){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return get_the_title( $this->getId() );
    }

    public function getImage(){
        $fundraise_mediaObj = new Fundraiser_Media();
        return $fundraise_mediaObj->get_fundraiser_logo_stripe( $this->getId() );
    }

    public function getStripeConnect(){
        return get_post_meta( $this->getId(), 'stripe_connect', true ) == 1;
    }

    public function getAccountId(){
        if ( $this->getStripeConnect() ) {
            $stripe_connect = new Stripe_Form();
            $get_account    = $stripe_connect->get_account_id( $this->getId() );
            $account_id     = $get_account->stripe_account_id;
            return $account_id;
        }
    }

    public function getOurFee(){
        return get_post_meta( $this->getId(), 'our_fee', true );
    }

    public function getCoachCode(){
        return get_post_meta( $this->getId(), 'coach_code', true );
    }

    public function getParticipants(){
        $participants_f = get_fundraiser_participants( $this->getId() );
        $participant_array = array();
        foreach ( $participants_f as $item ) {
            $participant_array[$item->ID] = $item->display_name;
        }
        return $participant_array;
    }

    public function getTeamName(){
        return get_post_meta( $this->getId(), 'team_name', true );
    }

    public function getRaised(){
        $payments = new \Payment_Records();
        return $payments->get_total_by_fundraiser_id( $this->getId() );
    }

    public function getGoal(){
        $goal = get_post_meta($this->getId(), 'fundraising_goal' , true);
        $raised = $this->getRaised();
        if( $goal < $raised ){
            return $raised + 100;
        }
        return $goal;
    }

    public function getAuthor(){
        return get_post_field( 'post_author', $this->getId(), true );
    }

    public function getSupporters(){
        $goal     = new \Goals;
        $p_supporters       = $goal->get_donators( $this->getId() );

        $sidebar = new Sidebar();

        $donation_comments = new Donation_Comments();
        $comments          = $donation_comments->get_by_fundraiser_id( $this->getId() );

        foreach ( $p_supporters as $key => $supporter ) {

            // Donation date
            $donation_date = $sidebar->donation_date($supporter['time']);

            // Days ago
            $days_ago = $sidebar->days_ago($donation_date);

            // Donation amount
            $donation_amount = $sidebar->format_donation_amount($supporter['amount']);

            // Donator name
            $donator_name = $sidebar->donator_name($supporter['name'], $supporter['anonymous']);

            $default_avatar = (is_mobile_new()) ? get_template_directory_uri() . "/assets/images/small-user-avatar.png" : get_template_directory_uri() . "/assets/images/user-avatar.png";
            $supporter_avatar = (!isset($comments[$supporter['id']]) || $comments[$supporter['id']]['avatar_url'] == 'default') ? $default_avatar : $comments[$supporter['id']]['avatar_url'];

            $p_supporters[$key]['donation_date']    = $donation_date;
            $p_supporters[$key]['days_ago']         = $days_ago;
            $p_supporters[$key]['donation_amount']  = $donation_amount;
            $p_supporters[$key]['donator_name']     = $donator_name;
            $p_supporters[$key]['default_avatar']   = $default_avatar;
            $p_supporters[$key]['supporter_avatar'] = $supporter_avatar;
        }


        foreach ( (array)$comments as $key => $comment ) {
            foreach ( $p_supporters as $key2 => $supporter ) {
                if ( $comment['d_id'] == $supporter['id'] ) {
                    $p_supporters[$key2]['comment'] = $comment['comment'];
                }
            }
        }
        return $p_supporters;

    }

    public function getPartPercentile(){
        return ( $this->getRaised() / $this->getGoal() ) * 100;
    }

    public function getShowProgressbar(){
        return get_field( 'show_progressbar', $this->getId() , true);
    }

    public function getPrimaryContactName(){
    	return get_post_meta($this->getId(), "con_name", true);
    }

    public function getNumberPhone(){
    	return get_post_meta($this->getId(), 'phone', true);
    }

    public function getEmail(){
		return get_post_meta($this->getId(), 'email', true);
    }

    public function getOrgType(){
    	return get_post_meta($this->getId(), 'org_type', true);
    }

    public function getForceConnect(){
    	return get_post_meta($this->getId(), 'force_connect', true);
    }

    public function getTaxId(){
    	return get_post_meta($this->getId(), 'tax_id', true);
    }

    public function getCheckPay(){
    	return get_post_meta($this->getId(), 'check_pay', true);
    }

    public function getBankAccountName(){
    	return get_post_meta($this->getId(), 'bank_account_name', true );
    }


    public function getHearAboutUs() {
    	return get_post_meta($this->getId(), 'hear_about_us', true );
    }

	public function getCoachName() {
		return get_post_meta( $this->getId(), 'coach_name', true );
	}

	public function getCoachEmail() {
		return get_post_meta( $this->getId(), 'coach_email', true );
	}

	public function getPaymentOption() {
		return get_post_meta( $this->getId(), 'stripe_connect', true );
	}

	public function getMailingAddress(){
    	return get_post_meta($this->getId(), 'mailing_address', true);
	}

	public function getStreet(){
    	return get_post_meta($this->getId(), 'street', true);
	}

	public function getCity(){
    	return get_post_meta($this->getId(), 'city', true);
	}

	public function getState(){
    	return get_post_meta($this->getId(), 'state', true);
	}

	public function getZipCode() {
		return get_post_meta( $this->getId(), 'zipcode', true );
	}

	public function getFundraisingGoal() {
		return get_post_meta( $this->getId(), 'fundraising_goal', true );
	}

	public function getEstimatedTeamSize() {
		return get_post_meta( $this->getId(), 'est_team_size', true );
	}

	public function getStartDate() {
		$str = get_post_meta( $this->getId(), 'start_date', true );
		$date = \DateTime::createFromFormat("Ymd", $str);
		return $date->format("m/d/Y");
	}

	public function getEndDate() {
		$str =  get_post_meta( $this->getId(), 'end_date', true );
		$date = \DateTime::createFromFormat("Ymd", $str);
		return $date->format("m/d/Y");
	}

	public function getCampaignMsg() {
		return get_post_meta( $this->getId(), 'campaign_msg', true );
	}

	public function getSubgroups(){
    	$subgroups_table = new Subgroups();
    	$subgroups = $subgroups_table->getSubgroups($this->getId());
    	return $subgroups;
	}

	public function __toJSON() {

		$arr = [
			'con_name'          => $this->getPrimaryContactName(),
			'phone'             => $this->getNumberPhone(),
			'email'             => $this->getEmail(),
			'org_type'          => $this->getOrgType(),
			'stripe_connect'    => $this->getStripeConnect(),
			'force_connect'     => $this->getForceConnect(),

			'tax_id'            => $this->getTaxId(),

			'hear_about_us'     => $this->getHearAboutUs(),
			'coach_name'        => $this->getCoachName(),
			'coach_email'       => $this->getCoachEmail(),
			'coach_code'        => $this->getCoachCode(),
			'bank_account_name' => $this->getBankAccountName(),
			'our_fee'           => $this->getOurFee(),


			'payment_option'    => $this->getPaymentOption(),

			'check_pay'         => $this->getCheckPay(),
			'mailing_address'   => $this->getMailingAddress(),


			'street'            => $this->getStreet(),
			'city'              => $this->getCity(),
			'state'             => $this->getState(),
			'zipcode'           => $this->getZipCode(),

			'fundraiser_name'   => $this->getTitle(),
			'team_name'         => $this->getTeamName(),
			'fundraising_goal'  => $this->getFundraisingGoal(),
			'est_team_size'     => $this->getEstimatedTeamSize(),
			'start_date'        => $this->getStartDate(),
			'end_date'          => $this->getEndDate(),
			'campaign_msg'      => $this->getCampaignMsg(),
			'subgroups'         => $this->getSubgroups(),
		];

		return json_encode( $arr );

	}

}
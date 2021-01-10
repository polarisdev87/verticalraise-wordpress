<?php


namespace classes\app\participant;

use classes\app\fundraiser\Fundraiser;
use classes\app\sidebar\Sidebar;
use classes\models\tables\Donation_Comments;

load_class("payment_records.class.php");
class Participant
{
    protected $id;
    protected $isParticipant;
    protected $goal;
    protected $raised;
    protected $fundraiser;
    protected $type;
    protected $isAdmin;
    protected $userInfo;
    protected $partPercentile;

    public static function getParticipant($id, $fundraiser){
        $participant = new Participant($id, $fundraiser);
        return $participant;
    }

    public function __construct( $id, $fundraiser ){
        $this->setId($id);
        $this->setFundraiser($fundraiser);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Fundraiser
     */
    public function getFundraiser()
    {
        return $this->fundraiser;
    }

    /**
     * @param Fundraiser $fundraiser
     */
    public function setFundraiser($fundraiser)
    {
        $this->fundraiser = $fundraiser;
    }

    public function getGoal() {
        $participationGoal = _PARTICIPATION_GOAL;
        $raised = $this->getRaised();
        if ( $raised > $participationGoal ) {
            return $raised + 100;
        }
        return $participationGoal;
    }

    public function getRaised(){
        $payments = new \Payment_Records();
        return $payments->get_total_by_user_id( $this->getId(), $this->getFundraiser()->getId() );

    }

    public function getIsParticipant(){
        $participants = get_fundraiser_participant_ids( $this->getFundraiser()->getId() );
        foreach ($participants as $participant){
            if ( $participant['uid'] == $this->getId() ) {
                return true;
            }
        }
        return false;
    }

    public function getIsAdmin(){
        $author_id = get_post_field( 'post_author', $this->getFundraiser()->getId() );
        if ($this->getId() == $author_id ) {
            return true;
        }
        $campaigns = json_decode( get_user_meta( $this->getId(), 'campaign_sadmin', true ) );
        return in_array($this->getFundraiser()->getId(), $campaigns);
    }

    public function getUserInfo(){
        return get_userdata( $this->getId() );
    }

    public function getType(){
        if ( $this->getId() == '0' || $this->getIsAdmin() ){
            return 'team';
        }
        return 'participant';
    }

    public function getPartPercentile(){
        return ( $this->getRaised() / $this->getGoal() ) * 100;
    }


    public function getSupporters(){

        $payments = new \Payment_Records();

        $p_supporters       = $payments->get_all_payments_by_fundraiser_uid( $this->getId(), $this->getFundraiser()->getId() );
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
}
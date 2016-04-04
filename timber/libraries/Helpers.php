<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.1
 * @package     Timber
 */

namespace Timber\Libraries;

/**
 * Some tasks extends app business logic packed as helper class
 *
 * @since 1.0
 */
class Helpers {

    /**
     * Instance of timber app
     *
     * @since 1.0
     * @access private
     * @var object $this->timber
     */
    private $timber;

    /**
     * Holds an instance of this class
     *
     * @since 1.0
     * @access private
     * @var object self::$instance
     */
    private static $instance;

    /**
     * Create instance of this class or return existing instance
     *
     * @since 1.0
     * @access public
     * @return object an instance of this class
     */
    public static function instance()
    {
        if ( !isset(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set class dependencies
     *
     * @since 1.0
     * @access public
     * @param object $timber
     * @return object
     */
    public function setDepen($timber)
    {
        $this->timber = $timber;
        return $this;
    }

    /**
     * Configure class
     *
     * @since 1.0
     * @access public
     */
    public function config()
    {
        //silence is golden
    }

    /**
     * Measure progress by dates
     *
     * @since 1.0
     * @access public
     * @param string  $start_data
     * @param string  $end_date
     * @param  boolean $today
     * @return integer
     */
    public function measureProgressByDates($start_data, $end_date, $today = false)
    {
        try {

            if( !$today ){
                $today_time = time();
            }
            $start_time = strtotime($start_data);
            $end_time = strtotime($end_date);

            if( $start_time >= $end_time ){
                return 100;
            }

            $result = (($today_time - $start_time) / ($end_time - $start_time)) * 100;
            $result = ($result > 100) ? 100 : $result;
            $result = ($result <= 0) ? 0 : $result;
            return round($result);

        } catch (Exception $e) {
            return 100;
        }
    }

    /**
     * Fix project status
     *
     * @since 1.0
     * @access public
     * @param integer $project_id
     * @param integer $status
     * @param string $start_at
     * @param string $end_at
     * @return integer
     */
    public function fixProjectStatus($project_id, $status, $start_at, $end_at)
    {
        $new_status = $status;
        $today = date('Y-m-d');

        //(1-Pending) (2-In Progress) (3-Overdue) (4-Done) (5-Archived)

        if( ($today < $start_at) && ($today < $end_at) && !(in_array($status, array(4, 5))) ){
            $new_status = 1;
        }

        if( ($today >= $start_at) && ($today < $end_at) && !(in_array($status, array(4, 5))) ){
            $new_status = 2;
        }

        if( ($today > $start_at) && ($today > $end_at) && !(in_array($status, array(4, 5))) ){
            $new_status = 3;
        }


        if( $new_status != $status ){
            $this->timber->project_model->updateProjectById(array(
                'pr_id' => $project_id,
                'status' => $new_status,
            ));
        }

        return $new_status;
    }

    /**
     * Fix task status
     *
     * @since 1.0
     * @access public
     * @param integer $task_id
     * @param integer $status
     * @param string $start_at
     * @param string $end_at
     * @return integer
     */
    public function fixTaskStatus($task_id, $status, $start_at, $end_at)
    {

        $new_status = $status;
        $today = date('Y-m-d');

        //(1-Pending) (2-In Progress) (3-Overdue) (4-Done)

        if( ($today < $start_at) && ($today < $end_at) && !(in_array($status, array(4))) ){
            $new_status = 1;
        }

        if( ($today >= $start_at) && ($today < $end_at) && !(in_array($status, array(4))) ){
            $new_status = 2;
        }

        if( ($today > $start_at) && ($today > $end_at) && !(in_array($status, array(4))) ){
            $new_status = 3;
        }

        if( $new_status != $status ){
            $this->timber->task_model->updateTaskById(array(
                'ta_id' => $task_id,
                'status' => $new_status,
            ));
        }

        return $new_status;
    }

    /**
     * Fix milestone status
     *
     * @since 1.0
     * @access public
     * @param integer $project_id
     * @param integer $milestone_id
     * @param integer $status
     * @return integer
     */
    public function fixMilestoneStatus($project_id, $milestone_id, $status)
    {
        //(1-Pending) (2-In Progress) (3-Overdue) (4-Done)
        $new_status = $status;
        $tasks_status = array(
            'all' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'overdue' => 0,
            'done' => 0,
        );
        $tasks = $this->timber->task_model->getTasksBy(array(
            'pr_id' => $project_id,
            'mi_id' => $milestone_id
        ));
        foreach ( $tasks as $task ) {
            $task_status = $this->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);
            $tasks_status['all'] += 1;

            if(1 == $task_status){
                $tasks_status['pending'] += 1;
            }elseif(2 == $task_status){
                $tasks_status['in_progress'] += 1;
            }elseif(3 == $task_status){
                $tasks_status['overdue'] += 1;
            }elseif(4 == $task_status){
                $tasks_status['done'] += 1;
            }

        }

        if( ($tasks_status['all'] == $tasks_status['pending']) ){
            $new_status = 1;
        }

        if( ($tasks_status['all'] == $tasks_status['done']) && (count($tasks) != 0 ) ){
            $new_status = 4;
        }

        if( ($tasks_status['all'] == $tasks_status['overdue']) && (count($tasks) != 0 ) ){
            $new_status = 3;
        }

        if( ($tasks_status['in_progress'] > 0) || ($tasks_status['pending'] > 0) || ($tasks_status['all'] == $tasks_status['in_progress']) && ($tasks_status['all'] != $tasks_status['pending']) ){
            $new_status = 2;
        }

        if(($tasks_status['pending'] == 0) && ($tasks_status['in_progress'] == 0) && ($tasks_status['overdue'] > 0) && ($tasks_status['done'] > 0)){
            $new_status = 3;
        }

        if( $new_status != $status ){
            $this->timber->milestone_model->updateMilestoneById(array(
                'mi_id' => $milestone_id,
                'status' => $new_status,
            ));
        }

        return $new_status;
    }

    /**
     * Fix ticket status
     *
     * @since 1.0
     * @access public
     * @param integer $ticket_id
     * @param integer $status
     * @return integer
     */
    public function fixTicketStatus($ticket_id, $status)
    {
        //(1-Pending) (2-Opened) (3-Closed)
        $new_status = $status;

        $ticket_replies = $this->timber->ticket_model->getTicketsBy(array(
            'parent_id' => $ticket_id,
        ));

        if( (count($ticket_replies) == 0) && ($status != 3) ){
            $new_status = 1;
        }
        if( (count($ticket_replies) > 0) && ($status != 3) ){
            $new_status = 2;
        }

        if( $new_status != $status ){
            $this->timber->ticket_model->updateTicketById(array(
                'ti_id' => $ticket_id,
                'status' => $new_status,
            ));
        }

        return $new_status;
    }

    /**
     * Get country by id
     *
     * @since 1.0
     * @access public
     * @param string $id
     * @return string
     */
    public function getCountryFromID($id)
    {
        $countries = $this->getCountries();
        return ( (isset($countries[$id])) ? $countries[$id] : "");
    }

    /**
     * Get a list of countries
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function getCountries()
    {
        return array(
            'AF' => $this->timber->translator->trans('Afghanistan'),
            'AL' => $this->timber->translator->trans('Albania'),
            'DZ' => $this->timber->translator->trans('Algeria'),
            'AS' => $this->timber->translator->trans('American Samoa'),
            'AD' => $this->timber->translator->trans('Andorra'),
            'AO' => $this->timber->translator->trans('Angola'),
            'AI' => $this->timber->translator->trans('Anguilla'),
            'AQ' => $this->timber->translator->trans('Antarctica'),
            'AG' => $this->timber->translator->trans('Antigua and Barbuda'),
            'AR' => $this->timber->translator->trans('Argentina'),
            'AM' => $this->timber->translator->trans('Armenia'),
            'AW' => $this->timber->translator->trans('Aruba'),
            'AU' => $this->timber->translator->trans('Australia'),
            'AT' => $this->timber->translator->trans('Austria'),
            'AZ' => $this->timber->translator->trans('Azerbaijan'),
            'BS' => $this->timber->translator->trans('Bahamas'),
            'BH' => $this->timber->translator->trans('Bahrain'),
            'BD' => $this->timber->translator->trans('Bangladesh'),
            'BB' => $this->timber->translator->trans('Barbados'),
            'BY' => $this->timber->translator->trans('Belarus'),
            'BE' => $this->timber->translator->trans('Belgium'),
            'BZ' => $this->timber->translator->trans('Belize'),
            'BJ' => $this->timber->translator->trans('Benin'),
            'BM' => $this->timber->translator->trans('Bermuda'),
            'BT' => $this->timber->translator->trans('Bhutan'),
            'BO' => $this->timber->translator->trans('Bolivia'),
            'BA' => $this->timber->translator->trans('Bosnia and Herzegovina'),
            'BW' => $this->timber->translator->trans('Botswana'),
            'BV' => $this->timber->translator->trans('Bouvet Island'),
            'BR' => $this->timber->translator->trans('Brazil'),
            'BQ' => $this->timber->translator->trans('British Antarctic Territory'),
            'IO' => $this->timber->translator->trans('British Indian Ocean Territory'),
            'VG' => $this->timber->translator->trans('British Virgin Islands'),
            'BN' => $this->timber->translator->trans('Brunei'),
            'BG' => $this->timber->translator->trans('Bulgaria'),
            'BF' => $this->timber->translator->trans('Burkina Faso'),
            'BI' => $this->timber->translator->trans('Burundi'),
            'KH' => $this->timber->translator->trans('Cambodia'),
            'CM' => $this->timber->translator->trans('Cameroon'),
            'CA' => $this->timber->translator->trans('Canada'),
            'CT' => $this->timber->translator->trans('Canton and Enderbury Islands'),
            'CV' => $this->timber->translator->trans('Cape Verde'),
            'KY' => $this->timber->translator->trans('Cayman Islands'),
            'CF' => $this->timber->translator->trans('Central African Republic'),
            'TD' => $this->timber->translator->trans('Chad'),
            'CL' => $this->timber->translator->trans('Chile'),
            'CN' => $this->timber->translator->trans('China'),
            'CX' => $this->timber->translator->trans('Christmas Island'),
            'CC' => $this->timber->translator->trans('Cocos [Keeling] Islands'),
            'CO' => $this->timber->translator->trans('Colombia'),
            'KM' => $this->timber->translator->trans('Comoros'),
            'CG' => $this->timber->translator->trans('Congo - Brazzaville'),
            'CD' => $this->timber->translator->trans('Congo - Kinshasa'),
            'CK' => $this->timber->translator->trans('Cook Islands'),
            'CR' => $this->timber->translator->trans('Costa Rica'),
            'HR' => $this->timber->translator->trans('Croatia'),
            'CU' => $this->timber->translator->trans('Cuba'),
            'CY' => $this->timber->translator->trans('Cyprus'),
            'CZ' => $this->timber->translator->trans('Czech Republic'),
            'CI' => $this->timber->translator->trans('Côte d’Ivoire'),
            'DK' => $this->timber->translator->trans('Denmark'),
            'DJ' => $this->timber->translator->trans('Djibouti'),
            'DM' => $this->timber->translator->trans('Dominica'),
            'DO' => $this->timber->translator->trans('Dominican Republic'),
            'NQ' => $this->timber->translator->trans('Dronning Maud Land'),
            'DD' => $this->timber->translator->trans('East Germany'),
            'EC' => $this->timber->translator->trans('Ecuador'),
            'EG' => $this->timber->translator->trans('Egypt'),
            'SV' => $this->timber->translator->trans('El Salvador'),
            'GQ' => $this->timber->translator->trans('Equatorial Guinea'),
            'ER' => $this->timber->translator->trans('Eritrea'),
            'EE' => $this->timber->translator->trans('Estonia'),
            'ET' => $this->timber->translator->trans('Ethiopia'),
            'FK' => $this->timber->translator->trans('Falkland Islands'),
            'FO' => $this->timber->translator->trans('Faroe Islands'),
            'FJ' => $this->timber->translator->trans('Fiji'),
            'FI' => $this->timber->translator->trans('Finland'),
            'FR' => $this->timber->translator->trans('France'),
            'GF' => $this->timber->translator->trans('French Guiana'),
            'PF' => $this->timber->translator->trans('French Polynesia'),
            'TF' => $this->timber->translator->trans('French Southern Territories'),
            'FQ' => $this->timber->translator->trans('French Southern and Antarctic Territories'),
            'GA' => $this->timber->translator->trans('Gabon'),
            'GM' => $this->timber->translator->trans('Gambia'),
            'GE' => $this->timber->translator->trans('Georgia'),
            'DE' => $this->timber->translator->trans('Germany'),
            'GH' => $this->timber->translator->trans('Ghana'),
            'GI' => $this->timber->translator->trans('Gibraltar'),
            'GR' => $this->timber->translator->trans('Greece'),
            'GL' => $this->timber->translator->trans('Greenland'),
            'GD' => $this->timber->translator->trans('Grenada'),
            'GP' => $this->timber->translator->trans('Guadeloupe'),
            'GU' => $this->timber->translator->trans('Guam'),
            'GT' => $this->timber->translator->trans('Guatemala'),
            'GG' => $this->timber->translator->trans('Guernsey'),
            'GN' => $this->timber->translator->trans('Guinea'),
            'GW' => $this->timber->translator->trans('Guinea-Bissau'),
            'GY' => $this->timber->translator->trans('Guyana'),
            'HT' => $this->timber->translator->trans('Haiti'),
            'HM' => $this->timber->translator->trans('Heard Island and McDonald Islands'),
            'HN' => $this->timber->translator->trans('Honduras'),
            'HK' => $this->timber->translator->trans('Hong Kong SAR China'),
            'HU' => $this->timber->translator->trans('Hungary'),
            'IS' => $this->timber->translator->trans('Iceland'),
            'IN' => $this->timber->translator->trans('India'),
            'ID' => $this->timber->translator->trans('Indonesia'),
            'IR' => $this->timber->translator->trans('Iran'),
            'IQ' => $this->timber->translator->trans('Iraq'),
            'IE' => $this->timber->translator->trans('Ireland'),
            'IM' => $this->timber->translator->trans('Isle of Man'),
            'IL' => $this->timber->translator->trans('Israel'),
            'IT' => $this->timber->translator->trans('Italy'),
            'JM' => $this->timber->translator->trans('Jamaica'),
            'JP' => $this->timber->translator->trans('Japan'),
            'JE' => $this->timber->translator->trans('Jersey'),
            'JT' => $this->timber->translator->trans('Johnston Island'),
            'JO' => $this->timber->translator->trans('Jordan'),
            'KZ' => $this->timber->translator->trans('Kazakhstan'),
            'KE' => $this->timber->translator->trans('Kenya'),
            'KI' => $this->timber->translator->trans('Kiribati'),
            'KW' => $this->timber->translator->trans('Kuwait'),
            'KG' => $this->timber->translator->trans('Kyrgyzstan'),
            'LA' => $this->timber->translator->trans('Laos'),
            'LV' => $this->timber->translator->trans('Latvia'),
            'LB' => $this->timber->translator->trans('Lebanon'),
            'LS' => $this->timber->translator->trans('Lesotho'),
            'LR' => $this->timber->translator->trans('Liberia'),
            'LY' => $this->timber->translator->trans('Libya'),
            'LI' => $this->timber->translator->trans('Liechtenstein'),
            'LT' => $this->timber->translator->trans('Lithuania'),
            'LU' => $this->timber->translator->trans('Luxembourg'),
            'MO' => $this->timber->translator->trans('Macau SAR China'),
            'MK' => $this->timber->translator->trans('Macedonia'),
            'MG' => $this->timber->translator->trans('Madagascar'),
            'MW' => $this->timber->translator->trans('Malawi'),
            'MY' => $this->timber->translator->trans('Malaysia'),
            'MV' => $this->timber->translator->trans('Maldives'),
            'ML' => $this->timber->translator->trans('Mali'),
            'MT' => $this->timber->translator->trans('Malta'),
            'MH' => $this->timber->translator->trans('Marshall Islands'),
            'MQ' => $this->timber->translator->trans('Martinique'),
            'MR' => $this->timber->translator->trans('Mauritania'),
            'MU' => $this->timber->translator->trans('Mauritius'),
            'YT' => $this->timber->translator->trans('Mayotte'),
            'FX' => $this->timber->translator->trans('Metropolitan France'),
            'MX' => $this->timber->translator->trans('Mexico'),
            'FM' => $this->timber->translator->trans('Micronesia'),
            'MI' => $this->timber->translator->trans('Midway Islands'),
            'MD' => $this->timber->translator->trans('Moldova'),
            'MC' => $this->timber->translator->trans('Monaco'),
            'MN' => $this->timber->translator->trans('Mongolia'),
            'ME' => $this->timber->translator->trans('Montenegro'),
            'MS' => $this->timber->translator->trans('Montserrat'),
            'MA' => $this->timber->translator->trans('Morocco'),
            'MZ' => $this->timber->translator->trans('Mozambique'),
            'MM' => $this->timber->translator->trans('Myanmar [Burma]'),
            'NA' => $this->timber->translator->trans('Namibia'),
            'NR' => $this->timber->translator->trans('Nauru'),
            'NP' => $this->timber->translator->trans('Nepal'),
            'NL' => $this->timber->translator->trans('Netherlands'),
            'AN' => $this->timber->translator->trans('Netherlands Antilles'),
            'NT' => $this->timber->translator->trans('Neutral Zone'),
            'NC' => $this->timber->translator->trans('New Caledonia'),
            'NZ' => $this->timber->translator->trans('New Zealand'),
            'NI' => $this->timber->translator->trans('Nicaragua'),
            'NE' => $this->timber->translator->trans('Niger'),
            'NG' => $this->timber->translator->trans('Nigeria'),
            'NU' => $this->timber->translator->trans('Niue'),
            'NF' => $this->timber->translator->trans('Norfolk Island'),
            'KP' => $this->timber->translator->trans('North Korea'),
            'VD' => $this->timber->translator->trans('North Vietnam'),
            'MP' => $this->timber->translator->trans('Northern Mariana Islands'),
            'NO' => $this->timber->translator->trans('Norway'),
            'OM' => $this->timber->translator->trans('Oman'),
            'PC' => $this->timber->translator->trans('Pacific Islands Trust Territory'),
            'PK' => $this->timber->translator->trans('Pakistan'),
            'PW' => $this->timber->translator->trans('Palau'),
            'PS' => $this->timber->translator->trans('Palestinian Territories'),
            'PA' => $this->timber->translator->trans('Panama'),
            'PZ' => $this->timber->translator->trans('Panama Canal Zone'),
            'PG' => $this->timber->translator->trans('Papua New Guinea'),
            'PY' => $this->timber->translator->trans('Paraguay'),
            'YD' => $this->timber->translator->trans('People\'s Democratic Republic of Yemen'),
            'PE' => $this->timber->translator->trans('Peru'),
            'PH' => $this->timber->translator->trans('Philippines'),
            'PN' => $this->timber->translator->trans('Pitcairn Islands'),
            'PL' => $this->timber->translator->trans('Poland'),
            'PT' => $this->timber->translator->trans('Portugal'),
            'PR' => $this->timber->translator->trans('Puerto Rico'),
            'QA' => $this->timber->translator->trans('Qatar'),
            'RO' => $this->timber->translator->trans('Romania'),
            'RU' => $this->timber->translator->trans('Russia'),
            'RW' => $this->timber->translator->trans('Rwanda'),
            'RE' => $this->timber->translator->trans('Réunion'),
            'BL' => $this->timber->translator->trans('Saint Barthélemy'),
            'SH' => $this->timber->translator->trans('Saint Helena'),
            'KN' => $this->timber->translator->trans('Saint Kitts and Nevis'),
            'LC' => $this->timber->translator->trans('Saint Lucia'),
            'MF' => $this->timber->translator->trans('Saint Martin'),
            'PM' => $this->timber->translator->trans('Saint Pierre and Miquelon'),
            'VC' => $this->timber->translator->trans('Saint Vincent and the Grenadines'),
            'WS' => $this->timber->translator->trans('Samoa'),
            'SM' => $this->timber->translator->trans('San Marino'),
            'SA' => $this->timber->translator->trans('Saudi Arabia'),
            'SN' => $this->timber->translator->trans('Senegal'),
            'RS' => $this->timber->translator->trans('Serbia'),
            'CS' => $this->timber->translator->trans('Serbia and Montenegro'),
            'SC' => $this->timber->translator->trans('Seychelles'),
            'SL' => $this->timber->translator->trans('Sierra Leone'),
            'SG' => $this->timber->translator->trans('Singapore'),
            'SK' => $this->timber->translator->trans('Slovakia'),
            'SI' => $this->timber->translator->trans('Slovenia'),
            'SB' => $this->timber->translator->trans('Solomon Islands'),
            'SO' => $this->timber->translator->trans('Somalia'),
            'ZA' => $this->timber->translator->trans('South Africa'),
            'GS' => $this->timber->translator->trans('South Georgia and the South Sandwich Islands'),
            'KR' => $this->timber->translator->trans('South Korea'),
            'ES' => $this->timber->translator->trans('Spain'),
            'LK' => $this->timber->translator->trans('Sri Lanka'),
            'SD' => $this->timber->translator->trans('Sudan'),
            'SR' => $this->timber->translator->trans('Suriname'),
            'SJ' => $this->timber->translator->trans('Svalbard and Jan Mayen'),
            'SZ' => $this->timber->translator->trans('Swaziland'),
            'SE' => $this->timber->translator->trans('Sweden'),
            'CH' => $this->timber->translator->trans('Switzerland'),
            'SY' => $this->timber->translator->trans('Syria'),
            'ST' => $this->timber->translator->trans('São Tomé and Príncipe'),
            'TW' => $this->timber->translator->trans('Taiwan'),
            'TJ' => $this->timber->translator->trans('Tajikistan'),
            'TZ' => $this->timber->translator->trans('Tanzania'),
            'TH' => $this->timber->translator->trans('Thailand'),
            'TL' => $this->timber->translator->trans('Timor-Leste'),
            'TG' => $this->timber->translator->trans('Togo'),
            'TK' => $this->timber->translator->trans('Tokelau'),
            'TO' => $this->timber->translator->trans('Tonga'),
            'TT' => $this->timber->translator->trans('Trinidad and Tobago'),
            'TN' => $this->timber->translator->trans('Tunisia'),
            'TR' => $this->timber->translator->trans('Turkey'),
            'TM' => $this->timber->translator->trans('Turkmenistan'),
            'TC' => $this->timber->translator->trans('Turks and Caicos Islands'),
            'TV' => $this->timber->translator->trans('Tuvalu'),
            'UM' => $this->timber->translator->trans('U.S. Minor Outlying Islands'),
            'PU' => $this->timber->translator->trans('U.S. Miscellaneous Pacific Islands'),
            'VI' => $this->timber->translator->trans('U.S. Virgin Islands'),
            'UG' => $this->timber->translator->trans('Uganda'),
            'UA' => $this->timber->translator->trans('Ukraine'),
            'SU' => $this->timber->translator->trans('Union of Soviet Socialist Republics'),
            'AE' => $this->timber->translator->trans('United Arab Emirates'),
            'GB' => $this->timber->translator->trans('United Kingdom'),
            'US' => $this->timber->translator->trans('United States'),
            'ZZ' => $this->timber->translator->trans('Unknown or Invalid Region'),
            'UY' => $this->timber->translator->trans('Uruguay'),
            'UZ' => $this->timber->translator->trans('Uzbekistan'),
            'VU' => $this->timber->translator->trans('Vanuatu'),
            'VA' => $this->timber->translator->trans('Vatican City'),
            'VE' => $this->timber->translator->trans('Venezuela'),
            'VN' => $this->timber->translator->trans('Vietnam'),
            'WK' => $this->timber->translator->trans('Wake Island'),
            'WF' => $this->timber->translator->trans('Wallis and Futuna'),
            'EH' => $this->timber->translator->trans('Western Sahara'),
            'YE' => $this->timber->translator->trans('Yemen'),
            'ZM' => $this->timber->translator->trans('Zambia'),
            'ZW' => $this->timber->translator->trans('Zimbabwe'),
            'AX' => $this->timber->translator->trans('Åland Islands'),
        );
    }
}
<?php
/**
 * Timber - Ultimate Freelancer Platform
 *
 * @author      Clivern <support@clivern.com>
 * @copyright   2015 Clivern
 * @link        http://clivern.com
 * @license     http://codecanyon.com/license
 * @version     1.0
 * @package     Timber
 */

namespace Timber\Controllers;

/**
 * Calendar Controller
 *
 * @since 1.0
 */
class Calendar {

    /**
     * Current used services
     *
     * @since 1.0
     * @access private
     * @var object
     */
    private $services;

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
        $this->timber->filter->setDepen($timber)->config();
        $this->services = new \Timber\Services\Base($this->timber);
        return $this;
    }

    /**
     * Run common tasks before rendering
     *
     * @since 1.0
     * @access public
     * @return boolean
     */
    public function renderFilters()
    {
        $this->timber->filter->issueDetect();
        $this->timber->filter->configLibs();
        $this->timber->security->cookieCheck();
        $this->services->Common->renderFilter(array('staff', 'admin'), '/admin/calendar');

        if( !($this->timber->access->checkPermission('view.calendar')) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
    }

    /**
     * Render
     *
     * @since 1.0
     * @access public
     */
    public function render()
    {
        return $this->timber->render( 'calendar', $this->getData() );
    }

    /**
     * Bind and get data
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function getData()
    {
        $data = array();

        $data = array_merge($data,
            $this->services->CalendarData->currentUserData(),
            $this->services->Common->subPageName( $this->timber->translator->trans('Calendar') . " | " ),
            $this->services->Common->runtimeScripts( 'calendar' ),
            $this->services->Common->injectScripts(array(
                'projectsEvents' => $this->services->CalendarData->projectsEvents(),
                'tasksEvents' => $this->services->CalendarData->tasksEvents(),
                'projectsEventsColor' => '#ecf0f1',
                'projectsEventsTextColor' => '#2c3e50',
                'tasksEventsColor' => '#bdc3c7',
                'tasksEventsTextColor' => '#2980b9',
                'calEvent_id'  => $this->timber->translator->trans('ID'),
                'calEvent_iden' => $this->timber->translator->trans('Identifier'),
                'calEvent_type' => $this->timber->translator->trans('Type'),
                'calEvent_mi_id' => $this->timber->translator->trans('Milestone ID'),
                'calEvent_mi_title' => $this->timber->translator->trans('Milestone Title'),
                'calEvent_pr_id' => $this->timber->translator->trans('Project ID'),
                'calEvent_owner_id' => $this->timber->translator->trans('Owner ID'),
                'calEvent_assign_to' => $this->timber->translator->trans('Assignee ID'),
                'calEvent_assign_to_name' => $this->timber->translator->trans('Assignee Name'),
                'calEvent_assign_to_email' => $this->timber->translator->trans('Assignee Email'),
                'calEvent_title' => $this->timber->translator->trans('Title'),
                'calEvent_description' => $this->timber->translator->trans('Description'),
                'calEvent_status' => $this->timber->translator->trans('Status'),
                'calEvent_progress' => $this->timber->translator->trans('Progress'),
                'calEvent_priority' => $this->timber->translator->trans('Priority'),
                'calEvent_start_at' => $this->timber->translator->trans('Start at'),
                'calEvent_end_at' => $this->timber->translator->trans('End at'),
                'calEvent_created_at' => $this->timber->translator->trans('Created at'),
                'calEvent_updated_at' => $this->timber->translator->trans('Updated at'),
                'calEvent_currency' => $this->timber->translator->trans('Currency'),
                'calEvent_reference' => $this->timber->translator->trans('Reference'),
                'calEvent_ref_id' => $this->timber->translator->trans('Reference ID'),
                'calEvent_version' => $this->timber->translator->trans('Version'),
                'calEvent_budget' => $this->timber->translator->trans('Budget'),
                'calEvent_tax_value' => $this->timber->translator->trans('Tax Value'),
                'calEvent_tax_type' => $this->timber->translator->trans('Tax Type'),
                'calEvent_discount_value' => $this->timber->translator->trans('Discount Value'),
                'calEvent_discount_type' => $this->timber->translator->trans('Discount Type'),
                'calEvent_attach' => $this->timber->translator->trans('Attachments'),
                'calEvent_owners' => $this->timber->translator->trans('Owners'),
                'calEvent_staff' => $this->timber->translator->trans('Staff'),
                'calEvent_clients' => $this->timber->translator->trans('Clients'),
                'calEvent_staff_ids' => $this->timber->translator->trans('Staff IDs'),
                'calEvent_clients_ids' => $this->timber->translator->trans('Clients IDs'),
            ))
        );

        return $data;
    }
}
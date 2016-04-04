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

namespace Timber\Services;

/**
 * Calendar Data Services
 *
 * @since 1.0
 */
class CalendarData extends \Timber\Services\Base {

    /**
     * Class Constructor
     *
     * @since 1.0
     * @access public
     * @param object $timber
     */
    public function __construct($timber)
    {
        parent::__construct($timber);
    }

    /**
     * Projects Events
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function projectsEvents()
    {

        $user_id = $this->timber->security->getId();
        $events = array();
        $new_events = array();
        $projects = $this->timber->project_model->getProjects( false, false, 'desc', 'created_at' );

        $i = 1;
        foreach ($projects as $key => $project) {

            $events[$i]['id'] = $i;
            $events[$i]['iden'] = 'p';
            $events[$i]['type'] = $this->timber->translator->trans('Project: ');
            $events[$i]['currency'] = $this->timber->config('_site_currency_symbol');

            $events[$i]['pr_id'] = $project['pr_id'];
            $events[$i]['title'] = $project['title'];
            $events[$i]['reference'] = $project['reference'];
            $events[$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
            $events[$i]['description'] = $project['description'];
            $events[$i]['version'] = $project['version'];
            $events[$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
            $events[$i]['budget'] = $project['budget'];
            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);
            $events[$i]['status'] = $project['status'];

            $events[$i]['nice_status'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
            );

            $events[$i]['owner_id'] = $project['owner_id'];
            $events[$i]['tax'] = $project['tax'];
            $tax = explode('-', $project['tax']);
            $events[$i]['tax_value'] = $tax[0];
            $events[$i]['tax_type'] = $tax[1];
            $events[$i]['discount'] = $project['discount'];
            $discount = explode('-', $project['discount']);
            $events[$i]['discount_value'] = $discount[0];
            $events[$i]['discount_type'] = $discount[1];
            $events[$i]['attach'] = $project['attach'];
            $events[$i]['created_at'] = $project['created_at'];
            $events[$i]['updated_at'] = $project['updated_at'];
            $events[$i]['start_at'] = $project['start_at'];
            $events[$i]['end_at'] = $project['end_at'];
            $events[$i]['start'] = $project['start_at'];
            $events[$i]['end'] = $project['end_at'];

            # add email, grav_id
            $events[$i]['owners'] = array();
            $events[$i]['staff'] = array();
            $events[$i]['clients'] = array();
            $events[$i]['staff_ids'] = array();
            $events[$i]['clients_ids'] = array();

            $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
                'pr_id' => $project['pr_id'],
                'me_key' => 'project_staff_members'
            ));

            $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
                'pr_id' => $project['pr_id'],
                'me_key' => 'project_clients_members'
            ));

            $owner_data = $this->timber->user_model->getUserById( $project['owner_id'] );

            if( (false !== $owner_data) && (is_object($owner_data)) ){
                $owner_data = $owner_data->as_array();
                $events[$i]['owners'][] = array(
                    'email' => $owner_data['email'],
                    'grav_id' => $owner_data['grav_id'],
                    'full_name' => trim( $owner_data['first_name'] . " " . $owner_data['last_name'] )
                );
            }


            if( (false !== $clients) && (is_object($clients)) ){

                $clients = $clients->as_array();
                $clients_ids = unserialize($clients['me_value']);

                foreach ($clients_ids as $clients_id) {
                    $client_data = $this->timber->user_model->getUserById( $clients_id );
                    if( (false === $client_data) || !(is_object($client_data)) ){ continue; }
                    $events[$i]['clients_ids'][] = $clients_id;
                    $client_data = $client_data->as_array();
                    $events[$i]['clients'][] = array(
                        'email' => $client_data['email'],
                        'grav_id' => $client_data['grav_id'],
                        'full_name' => trim( $client_data['first_name'] . " " . $client_data['last_name'] )
                    );
                }
            }

            if( (false !== $staff) && (is_object($staff)) ){

                $staff = $staff->as_array();
                $staff_ids = unserialize($staff['me_value']);

                foreach ($staff_ids as $staff_id) {
                    $staff_data = $this->timber->user_model->getUserById( $staff_id );
                    if( (false === $staff_data) || !(is_object($staff_data)) ){ continue; }
                    $events[$i]['staff_ids'][] = $staff_id;
                    $staff_data = $staff_data->as_array();
                    $events[$i]['staff'][] = array(
                        'email' => $staff_data['email'],
                        'grav_id' => $staff_data['grav_id'],
                        'full_name' => trim( $staff_data['first_name'] . " " . $staff_data['last_name'] )
                    );
                }

            }

            if( ($this->timber->access->getRule() == 'staff') && !(in_array($user_id, $events[$i]['staff_ids'])) ){
                unset($events[$i]);
            }else{
                $new_events[] = $events[$i];
            }


            $i += 1;
        }


        return json_encode($new_events);
    }

    /**
     * Tasks Events
     *
     * @since 1.0
     * @access public
     * @return string
     */
    public function tasksEvents()
    {

        $user_id = $this->timber->security->getId();
        $events = array();
        $new_events = array();
        $tasks = $this->timber->task_model->getTasks( false, false, 'desc', 'created_at' );

        $i = 1;
        foreach ($tasks as $key => $task) {

            if( ($this->timber->access->getRule() == 'staff') && ($user_id != $task['assign_to']) ){
                continue;
            }

            $events[$i]['id'] = $i;
            $events[$i]['iden'] = 't';
            $events[$i]['type'] = $this->timber->translator->trans('Task: ');

            $events[$i]['ta_id'] = $task['ta_id'];
            $events[$i]['mi_id'] = $task['mi_id'];
            $events[$i]['mi_title'] = "";

            $milestone = $this->timber->milestone_model->getMilestoneById($task['mi_id']);

            if( (false !== $milestone) && (is_object($milestone)) ){
                $milestone = $milestone->as_array();
                $events[$i]['mi_title'] = $milestone['title'];
            }

            $events[$i]['pr_id'] = $task['pr_id'];
            $events[$i]['owner_id'] = $task['owner_id'];
            $events[$i]['assign_to'] = $task['assign_to'];
            $events[$i]['assign_to_name'] = "";
            $events[$i]['assign_to_email'] = "";
            $events[$i]['assign_to_grav_id'] = "";
            $assign_to = $this->timber->user_model->getUserById( $task['assign_to'] );

            if( (false !== $assign_to) && (is_object($assign_to)) ){
                $assign_to = $assign_to->as_array();
                $events[$i]['assign_to_email'] = $assign_to['email'];
                $events[$i]['assign_to_grav_id'] = $assign_to['grav_id'];
                $events[$i]['assign_to_name'] = trim( $assign_to['first_name'] . " " . $assign_to['last_name'] );
            }

            $events[$i]['title'] = $task['title'];
            $events[$i]['description'] = $task['description'];

            $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);
            $events[$i]['status'] = $task['status'];
            $events[$i]['progress'] = $this->timber->helpers->measureProgressByDates($task['start_at'], $task['end_at']);
            $events[$i]['priority'] = $task['priority'];
            $events[$i]['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $task['status']
            );
            $events[$i]['nice_priority'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $task['priority']
            );
            $events[$i]['start_at'] = $task['start_at'];
            $events[$i]['end_at'] = $task['end_at'];
            $events[$i]['start'] = $task['start_at'];
            $events[$i]['end'] = $task['end_at'];
            $events[$i]['created_at'] = $task['created_at'];
            $events[$i]['updated_at'] = $task['updated_at'];

            $new_events[] = $events[$i];

            $i += 1;
        }

        return json_encode($new_events);
    }

    /**
     * Get Current User Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function currentUserData()
    {
        $data = array();
        $user_id = $this->timber->security->getId();
        $user = $this->timber->user_model->getUserById($user_id);

        if( (false === $user) || !(is_object($user)) ){
            $this->timber->security->endSession();
            $this->timber->redirect( $this->timber->config('request_url') . '/500' );
        }

        $user = $user->as_array();
        foreach ($user as $key => $value) {
            $data['current_user_' . $key] = $value;
        }

        return $data;
    }
}
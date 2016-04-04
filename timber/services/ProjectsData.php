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
 * Projects Data Services
 *
 * @since 1.0
 */
class ProjectsData extends \Timber\Services\Base {

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
     * Get Projects Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function listData()
    {
        # Get & Bind Members
        $data = array();
        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        if( $this->timber->access->getRule() == 'admin' ){
            $records_start = ( (isset($_POST['records_start'])) && (filter_var($_POST['records_start'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['records_start'], FILTER_SANITIZE_NUMBER_INT) : 0;
            $records_offset = 6;

            $projects = $this->timber->project_model->getProjects( $records_start, $records_offset, 'desc', 'created_at' );
        }else{
            $projects = $this->timber->project_model->getProjects( false, false, 'desc', 'created_at' );
        }

        $i = 1;
        $data['projects'] = array();

        foreach ($projects as $key => $project) {

            $data['projects'][$i]['pr_id'] = $project['pr_id'];
            $data['projects'][$i]['title'] = $project['title'];
            $data['projects'][$i]['reference'] = $project['reference'];
            $data['projects'][$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
            $data['projects'][$i]['description'] = $project['description'];
            $data['projects'][$i]['version'] = $project['version'];
            $data['projects'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
            $data['projects'][$i]['budget'] = $project['budget'];
            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);
            $data['projects'][$i]['status'] = $project['status'];

            $data['projects'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
            );

            $data['projects'][$i]['owner_id'] = $project['owner_id'];
            $data['projects'][$i]['tax'] = $project['tax'];
            $tax = explode('-', $project['tax']);
            $data['projects'][$i]['tax_value'] = $tax[0];
            $data['projects'][$i]['tax_type'] = $tax[1];
            $data['projects'][$i]['discount'] = $project['discount'];
            $discount = explode('-', $project['discount']);
            $data['projects'][$i]['discount_value'] = $discount[0];
            $data['projects'][$i]['discount_type'] = $discount[1];
            $data['projects'][$i]['attach'] = $project['attach'];
            $data['projects'][$i]['created_at'] = $project['created_at'];
            $data['projects'][$i]['updated_at'] = $project['updated_at'];
            $data['projects'][$i]['start_at'] = $project['start_at'];
            $data['projects'][$i]['end_at'] = $project['end_at'];

            $data['projects'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'];
            $data['projects'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/projects/edit/' . $project['pr_id'];
            $data['projects'][$i]['delete_link'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete';

            # add email, grav_id
            $data['projects'][$i]['owners'] = array();
            $data['projects'][$i]['staff'] = array();
            $data['projects'][$i]['clients'] = array();
            $data['projects'][$i]['staff_ids'] = array();
            $data['projects'][$i]['clients_ids'] = array();

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
                $data['projects'][$i]['owners'][] = array(
                    'email' => $owner_data['email'],
                    'grav_id' => $owner_data['grav_id'],
                    'full_name' => trim( $owner_data['first_name'] . " " . $owner_data['last_name'] )
                );
            }

            $data['projects'][$i]['clients_ids'] = array();
            $data['projects'][$i]['clients'] = array();
            if( (false !== $clients) && (is_object($clients)) ){

                $clients = $clients->as_array();
                $clients_ids = unserialize($clients['me_value']);

                foreach ($clients_ids as $clients_id) {
                    $client_data = $this->timber->user_model->getUserById( $clients_id );
                    if( (false === $client_data) || !(is_object($client_data)) ){ continue; }
                    $data['projects'][$i]['clients_ids'][] = $clients_id;
                    $client_data = $client_data->as_array();
                    $data['projects'][$i]['clients'][] = array(
                        'email' => $client_data['email'],
                        'grav_id' => $client_data['grav_id'],
                        'full_name' => trim( $client_data['first_name'] . " " . $client_data['last_name'] )
                    );
                }
            }

            $data['projects'][$i]['staff_ids'] = array();
            $data['projects'][$i]['staff'] = array();
            if( (false !== $staff) && (is_object($staff)) ){

                $staff = $staff->as_array();
                $staff_ids = unserialize($staff['me_value']);

                foreach ($staff_ids as $staff_id) {
                    $staff_data = $this->timber->user_model->getUserById( $staff_id );
                    if( (false === $staff_data) || !(is_object($staff_data)) ){ continue; }
                    $data['projects'][$i]['staff_ids'][] = $staff_id;
                    $staff_data = $staff_data->as_array();
                    $data['projects'][$i]['staff'][] = array(
                        'email' => $staff_data['email'],
                        'grav_id' => $staff_data['grav_id'],
                        'full_name' => trim( $staff_data['first_name'] . " " . $staff_data['last_name'] )
                    );
                }

            }

            if( ($this->timber->access->getRule() == 'staff') && !(in_array($this->timber->security->getId(), $data['projects'][$i]['staff_ids'])) ){
                unset($data['projects'][$i]);
            }

            if( ($this->timber->access->getRule() == 'client') && !(in_array($this->timber->security->getId(), $data['projects'][$i]['clients_ids'])) ){
                unset($data['projects'][$i]);
            }

            $i += 1;
        }

        return $data;
    }

    /**
     * Add Projects Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function addData()
    {

        $data = array();

        # Bind Actions
        $data['add_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add';
        $data['edit_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit';
        $data['delete_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete';

        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();

        $data['members_list'] = array();
        $data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

        $users = $this->timber->user_model->getUsers();

        $i = 0;
        foreach ($users as $key => $user) {

            $data['members_list'][$i]['us_id'] = $user['us_id'];
            $data['members_list'][$i]['user_name'] = $user['user_name'];
            $data['members_list'][$i]['first_name'] = $user['first_name'];
            $data['members_list'][$i]['last_name'] = $user['last_name'];
            $data['members_list'][$i]['full_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $data['members_list'][$i]['access_rule'] = $user['access_rule'];
            $data['members_list'][$i]['status'] = $user['status'];
            $data['members_list'][$i]['grav_id'] = $user['grav_id'];
            $data['members_list'][$i]['email'] = $user['email'];

            $i += 1;
        }

        return $data;
    }

    /**
     * Edit Projects Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function editData($project_id)
    {

        $project_id = ( (boolean) filter_var($project_id, FILTER_VALIDATE_INT) ) ? filter_var($project_id, FILTER_SANITIZE_NUMBER_INT) : false;
        if( false === $project_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }
        $data = array();

        # Bind Actions
        $data['add_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add';
        $data['edit_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit';
        $data['delete_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete';

        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $project = $this->timber->project_model->getProjectById( $project_id );

        if( (false === $project) || !(is_object($project)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $project = $project->as_array();

        $data['project_pr_id'] = $project['pr_id'];
        $data['project_title'] = $project['title'];

        if( !empty($data['project_title']) ){
            $data['site_sub_page'] = $data['project_title']  . " | ";
        }

        $data['project_reference'] = $project['reference'];
        $data['project_ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
        $data['project_description'] = $project['description'];
        $data['project_version'] = $project['version'];
        $data['project_progress'] = $project['progress'];
        $data['project_budget'] = $project['budget'];
        $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);
        $data['project_status'] = $project['status'];

        $data['project_nice_status'] = str_replace(
            array('1','2','3','4','5'),
            array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
        );

        $data['project_owner_id'] = $project['owner_id'];
        $data['project_tax'] = $project['tax'];
        $data['project_discount'] = $project['discount'];

        $tax = explode('-', $project['tax']);
        $discount = explode('-', $project['discount']);

        $data['project_tax_value'] = $tax[0];
        $data['project_tax_type'] = $tax[1];
        $data['project_discount_value'] = $discount[0];
        $data['project_discount_type'] = $discount[1];

        $data['project_attach'] = $project['attach'];
        $data['project_created_at'] = $project['created_at'];
        $data['project_updated_at'] = $project['updated_at'];
        $data['project_start_at'] = $project['start_at'];
        $data['project_end_at'] = $project['end_at'];

        $data['project_staff_ids'] = array();
        $data['project_clients_ids'] = array();

        $staff = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $project['pr_id'],
            'me_key' => 'project_staff_members'
        ));

        $clients = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $project['pr_id'],
            'me_key' => 'project_clients_members'
        ));

        if( (false !== $staff) && (is_object($staff)) ){
            $staff = $staff->as_array();
            $data['project_staff_ids'] = unserialize($staff['me_value']);
        }

        if( (false !== $clients) && (is_object($clients)) ){
            $clients = $clients->as_array();
            $data['project_clients_ids'] = unserialize($clients['me_value']);
        }

        $data['members_list'] = array();
        $data['taxes_list'] = unserialize($this->timber->config('_site_tax_rates'));

        $users = $this->timber->user_model->getUsers();

        $i = 0;
        foreach ($users as $key => $user) {

            $data['members_list'][$i]['us_id'] = $user['us_id'];
            $data['members_list'][$i]['user_name'] = $user['user_name'];
            $data['members_list'][$i]['first_name'] = $user['first_name'];
            $data['members_list'][$i]['last_name'] = $user['last_name'];
            $data['members_list'][$i]['full_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $data['members_list'][$i]['access_rule'] = $user['access_rule'];
            $data['members_list'][$i]['status'] = $user['status'];
            $data['members_list'][$i]['grav_id'] = $user['grav_id'];
            $data['members_list'][$i]['email'] = $user['email'];

            $i += 1;
        }

        return $data;
    }

    /**
     * View Projects Data
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function viewData($project_id)
    {

        $project_id = ( (boolean) filter_var($project_id, FILTER_VALIDATE_INT) ) ? filter_var($project_id, FILTER_SANITIZE_NUMBER_INT) : false;
        $tab = ( (isset($_GET['tab'])) && (in_array($_GET['tab'], array('stats', 'files', 'tasks', 'milestones', 'tickets'))) ) ? $_GET['tab'] : 'stats';

        if( false === $project_id){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $data = array();

        # Bind Actions
        $data['add_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add';
        $data['edit_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit';
        $data['delete_project_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete';

        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $user_id = $this->timber->security->getId();
        $project = $this->timber->project_model->getProjectById( $project_id );

        if( (false === $project) || !(is_object($project)) ){
            $this->timber->redirect( $this->timber->config('request_url') . '/404' );
        }

        $project = $project->as_array();

        $data['project_tab'] = $tab;
        $data['project_pr_id'] = $project['pr_id'];
        $data['project_title'] = $project['title'];

        if( !empty($data['project_title']) ){
            $data['site_sub_page'] = $data['project_title']  . " | ";
        }

        $data['project_reference'] = $project['reference'];
        $data['project_ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
        $data['project_description'] = $project['description'];
        $data['project_version'] = $project['version'];
        $data['project_progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
        $data['project_budget'] = $project['budget'];
        $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);
        $data['project_status'] = $project['status'];

        $data['project_nice_status'] = str_replace(
            array('1','2','3','4','5'),
            array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
        );

        $data['project_owner_id'] = $project['owner_id'];
        $data['project_tax'] = $project['tax'];
        $data['project_discount'] = $project['discount'];


        $tax = explode('-', $project['tax']);
        $discount = explode('-', $project['discount']);

        $data['project_tax_value'] = $tax[0];
        $data['project_tax_type'] = $tax[1];
        $data['project_discount_value'] = $discount[0];
        $data['project_discount_type'] = $discount[1];

        $data['project_attach'] = $project['attach'];
        $data['project_created_at'] = $project['created_at'];
        $data['project_updated_at'] = $project['updated_at'];
        $data['project_start_at'] = $project['start_at'];
        $data['project_end_at'] = $project['end_at'];

        $data['project_owners'] = array();
        $data['project_staff'] = array();
        $data['project_clients'] = array();
        $data['project_staff_ids'] = array($project['owner_id']);
        $data['project_staff_data'] = array();

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
            $data['project_owners'][] = array(
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
                $client_data = $client_data->as_array();
                $data['project_clients'][] = array(
                    'email' => $client_data['email'],
                    'grav_id' => $client_data['grav_id'],
                    'full_name' => trim( $client_data['first_name'] . " " . $client_data['last_name'] )
                );
            }
        }

        if( (false !== $staff) && (is_object($staff)) ){

            $staff = $staff->as_array();
            $staff_ids = unserialize($staff['me_value']);
            $data['project_staff_ids'] = array_merge($data['project_staff_ids'], $staff_ids);
            foreach ($staff_ids as $staff_id) {
                $staff_data = $this->timber->user_model->getUserById( $staff_id );
                if( (false === $staff_data) || !(is_object($staff_data)) ){ continue; }
                $staff_data = $staff_data->as_array();
                $data['project_staff'][] = array(
                    'email' => $staff_data['email'],
                    'grav_id' => $staff_data['grav_id'],
                    'full_name' => trim( $staff_data['first_name'] . " " . $staff_data['last_name'] )
                );
            }

        }

        $data['project_stats_url'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'] . '?tab=stats';
        $data['project_milestones_url'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'] . '?tab=milestones';
        $data['project_tasks_url'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'] . '?tab=tasks';
        $data['project_files_url'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'] . '?tab=files';
        $data['project_tickets_url'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'] . '?tab=tickets';

        if( 'files' == $tab ){
            $data = $this->projectFilesData($project['pr_id'], $data);
        }elseif( 'stats' == $tab ){
            $data = $this->projectStatsData($project['pr_id'], $data);
        }elseif( 'tasks' == $tab ){
            $data = $this->projectTasksData($project['pr_id'], $data);
        }elseif( 'milestones' == $tab ){
            $data = $this->projectMilestonesData($project['pr_id'], $data);
        }elseif( 'tickets' == $tab ){
            $data = $this->projectTicketsData($project['pr_id'], $data);
        }

        $data['members_list'] = array();
        $users = $this->timber->user_model->getUsers();
        $i = 0;
        foreach ($users as $key => $user) {

            $data['members_list'][$i]['us_id'] = $user['us_id'];
            $data['members_list'][$i]['user_name'] = $user['user_name'];
            $data['members_list'][$i]['first_name'] = $user['first_name'];
            $data['members_list'][$i]['last_name'] = $user['last_name'];
            $data['members_list'][$i]['full_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $data['members_list'][$i]['access_rule'] = $user['access_rule'];
            $data['members_list'][$i]['status'] = $user['status'];
            $data['members_list'][$i]['grav_id'] = $user['grav_id'];
            $data['members_list'][$i]['email'] = $user['email'];

            if( in_array($user['us_id'], $data['project_staff_ids']) ){
                $data['project_staff_data'][$i] = $data['members_list'][$i];
            }

            $i += 1;
        }

        return $data;
    }

    /**
     * Get Project Stats
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function projectStatsData($project_id, $data)
    {

        $data['tasks_stats'] = $this->getProjectTasksStats($project_id);
        $data['tickets_stats'] = $this->getProjectTicketsStats($project_id);
        $data['milestones_stats'] = $this->getProjectMilestonesStats($project_id);
        $data['files_stats'] = $this->getProjectFilesStats($project_id);

        return $data;
    }

    /**
     * Get Project Tasks
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function projectTasksData($project_id, $data)
    {

        $task_id = ( (isset($_GET['task_id'])) && ((boolean) filter_var($_GET['task_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_GET['task_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $sub_tab = ( (isset($_GET['sub_tab'])) && (in_array($_GET['sub_tab'], array('list', 'edit', 'add'))) ) ? $_GET['sub_tab'] : 'list';

        $data['project_task_id'] = $task_id;
        $data['project_sub_tab'] = $sub_tab;

        $data['edit_task_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit_task';
        $data['add_task_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add_task';
        $data['delete_task_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_task';
        $data['mark_task_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/mark_task';

        // Edit Task Page
        // Only admins can access this page
        if( ('edit' == $sub_tab) && (false !== $task_id) ){

            if( !($this->timber->access->getRule() == 'admin') ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $task = $this->timber->task_model->getTaskById($task_id);

            if( (false === $task) || !(is_object($task)) ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $task = $task->as_array();

            $data['task_ta_id'] = $task['ta_id'];
            $data['task_mi_id'] = $task['mi_id'];
            $data['task_pr_id'] = $task['pr_id'];
            $data['task_owner_id'] = $task['owner_id'];
            $data['task_assign_to'] = $task['assign_to'];
            $data['task_title'] = $task['title'];
            $data['task_description'] = $task['description'];
            $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);
            $data['task_status'] = $task['status'];
            $data['task_priority'] = $task['priority'];
            $data['task_start_at'] = $task['start_at'];
            $data['task_end_at'] = $task['end_at'];
            $data['task_created_at'] = $task['created_at'];
            $data['task_updated_at'] = $task['updated_at'];

            $milestones = $this->timber->milestone_model->getMilestonesBy(array(
                'pr_id' => $project_id
            ));

            $i = 1;
            $data['milestones'] = array();

            foreach ($milestones as $key => $milestone) {

                $data['milestones'][$i]['mi_id'] = $milestone['mi_id'];
                $data['milestones'][$i]['title'] = $milestone['title'];

                $i += 1;
            }

        // Add Task Page
        // Only admins can access this page
        }elseif('add' == $sub_tab){

            if( !($this->timber->access->getRule() == 'admin') ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $milestones = $this->timber->milestone_model->getMilestonesBy(array(
                'pr_id' => $project_id
            ));

            $i = 1;
            $data['milestones'] = array();

            foreach ($milestones as $key => $milestone) {

                $data['milestones'][$i]['mi_id'] = $milestone['mi_id'];
                $data['milestones'][$i]['title'] = $milestone['title'];

                $i += 1;
            }

        }else{

            $tasks = $this->timber->task_model->getTasksBy(array(
                'pr_id' => $project_id
            ));

            $i = 1;
            $data['tasks'] = array();

            foreach ($tasks as $key => $task) {

                $data['tasks'][$i]['ta_id'] = $task['ta_id'];
                $data['tasks'][$i]['mi_id'] = $task['mi_id'];
                $data['tasks'][$i]['mi_title'] = "";

                $milestone = $this->timber->milestone_model->getMilestoneById($task['mi_id']);

                if( (false !== $milestone) && (is_object($milestone)) ){
                    $milestone = $milestone->as_array();
                    $data['tasks'][$i]['mi_title'] = $milestone['title'];
                }

                $data['tasks'][$i]['pr_id'] = $task['pr_id'];
                $data['tasks'][$i]['owner_id'] = $task['owner_id'];
                $data['tasks'][$i]['assign_to'] = $task['assign_to'];
                $data['tasks'][$i]['assign_to_name'] = "";
                $data['tasks'][$i]['assign_to_email'] = "";
                $data['tasks'][$i]['assign_to_grav_id'] = "";

                $assign_to = $this->timber->user_model->getUserById( $task['assign_to'] );

                if( (false !== $assign_to) && (is_object($assign_to)) ){
                    $assign_to = $assign_to->as_array();
                    $data['tasks'][$i]['assign_to_email'] = $assign_to['email'];
                    $data['tasks'][$i]['assign_to_grav_id'] = $assign_to['grav_id'];
                    $data['tasks'][$i]['assign_to_name'] = trim( $assign_to['first_name'] . " " . $assign_to['last_name'] );
                }

                $data['tasks'][$i]['title'] = $task['title'];
                $data['tasks'][$i]['description'] = $task['description'];
                $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);
                $data['tasks'][$i]['status'] = $task['status'];
                $data['tasks'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($task['start_at'], $task['end_at']);
                $data['tasks'][$i]['priority'] = $task['priority'];

                $data['tasks'][$i]['nice_status'] = str_replace(
                    array('1','2','3','4'),
                    array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $task['status']
                );
                $data['tasks'][$i]['nice_priority'] = str_replace(
                    array('1','2','3','4'),
                    array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $task['priority']
                );

                $data['tasks'][$i]['start_at'] = $task['start_at'];
                $data['tasks'][$i]['end_at'] = $task['end_at'];
                $data['tasks'][$i]['created_at'] = $task['created_at'];
                $data['tasks'][$i]['updated_at'] = $task['updated_at'];

                $data['tasks'][$i]['edit_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $project_id .'?tab=tasks&sub_tab=edit&task_id=' . $task['ta_id'];
                $data['tasks'][$i]['trash_link'] =  $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_task';

                $i += 1;
            }

            $data['tasks_count'] = count($data['tasks']);

        }

        return $data;
    }

    /**
     * Get Project Milestones
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function projectMilestonesData($project_id, $data)
    {

        $miles_id = ( (isset($_GET['miles_id'])) && ((boolean) filter_var($_GET['miles_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_GET['miles_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $sub_tab = ( (isset($_GET['sub_tab'])) && (in_array($_GET['sub_tab'], array('list', 'edit', 'add'))) ) ? $_GET['sub_tab'] : 'list';

        $data['project_miles_id'] = $miles_id;
        $data['project_sub_tab'] = $sub_tab;

        $data['edit_milestone_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit_milestone';
        $data['add_milestone_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add_milestone';
        $data['delete_milestone_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_milestone';

        // Edit Milestone Page
        // Only admins can access this page
        if( ('edit' == $sub_tab) && (false !== $miles_id) ){

            if( !($this->timber->access->getRule() == 'admin') ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $milestone = $this->timber->milestone_model->getMilestoneById($miles_id);

            if( (false === $milestone) || !(is_object($milestone)) ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $milestone = $milestone->as_array();

            $data['milestone_mi_id'] = $milestone['mi_id'];
            $data['milestone_pr_id'] = $milestone['pr_id'];
            $data['milestone_owner_id'] = $milestone['owner_id'];
            $data['milestone_title'] = $milestone['title'];
            $data['milestone_description'] = $milestone['description'];
            $milestone['status'] = $this->timber->helpers->fixMilestoneStatus($project_id, $milestone['mi_id'], $milestone['status']);
            $data['milestone_status'] = $milestone['status'];
            $data['milestone_priority'] = $milestone['priority'];
            $data['milestone_created_at'] = $milestone['created_at'];
            $data['milestone_updated_at'] = $milestone['updated_at'];

        // Add Milestone Page
        // Only admins can access this page
        }elseif('add' == $sub_tab){

            if( !($this->timber->access->getRule() == 'admin') ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

        }elseif('add' != $sub_tab){

            $milestones = $this->timber->milestone_model->getMilestonesBy(array(
                'pr_id' => $project_id
            ));

            $i = 1;
            $data['milestones'] = array();

            foreach ($milestones as $key => $milestone) {

                $data['milestones'][$i]['mi_id'] = $milestone['mi_id'];
                $data['milestones'][$i]['pr_id'] = $milestone['pr_id'];
                $data['milestones'][$i]['owner_id'] = $milestone['owner_id'];
                $data['milestones'][$i]['title'] = $milestone['title'];
                $data['milestones'][$i]['description'] = $milestone['description'];
                $milestone['status'] = $this->timber->helpers->fixMilestoneStatus($project_id, $milestone['mi_id'], $milestone['status']);
                $data['milestones'][$i]['status'] = $milestone['status'];
                $data['milestones'][$i]['priority'] = $milestone['priority'];
                $data['milestones'][$i]['created_at'] = $milestone['created_at'];
                $data['milestones'][$i]['updated_at'] = $milestone['updated_at'];

                $data['milestones'][$i]['nice_status'] = str_replace(
                    array('1','2','3','4'),
                    array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $milestone['status']
                );
                $data['milestones'][$i]['nice_priority'] = str_replace(
                    array('1','2','3','4'),
                    array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $milestone['priority']
                );

                $data['milestones'][$i]['edit_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $project_id .'?tab=milestones&sub_tab=edit&miles_id=' . $milestone['mi_id'];
                $data['milestones'][$i]['trash_link'] =  $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_milestone';

                $i += 1;
            }

            $data['milestones_count'] = count($data['milestones']);

        }

        return $data;
    }

    /**
     * Get Project Tickets
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function projectTicketsData($project_id, $data)
    {
        $tick_id = ( (isset($_GET['tick_id'])) && ((boolean) filter_var($_GET['tick_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_GET['tick_id'], FILTER_SANITIZE_NUMBER_INT) : false;
        $sub_tab = ( (isset($_GET['sub_tab'])) && (in_array($_GET['sub_tab'], array('list', 'edit', 'add', 'view'))) ) ? $_GET['sub_tab'] : 'list';

        $data['project_tick_id'] = $tick_id;
        $data['project_sub_tab'] = $sub_tab;

        $data['add_ticket_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/add_ticket';
        $data['edit_ticket_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/edit_ticket';
        $data['reply_ticket_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/reply_ticket';
        $data['delete_ticket_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_ticket';
        $data['mark_ticket_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/mark_ticket';

        // Edit Ticket Page
        // Admin and owner
        if( ('edit' == $sub_tab) && (false !== $tick_id) ){

            $ticket = $this->timber->ticket_model->getTicketById($tick_id);

            if( (false === $ticket) || !(is_object($ticket)) ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $ticket = $ticket->as_array();

            if( !($this->timber->access->getRule() == 'admin') && !( ($this->timber->access->getRule() == 'staff') && ($this->timber->security->getId() == $ticket['owner_id']) ) && !( ($this->timber->access->getRule() == 'client') && ($this->timber->security->getId() == $ticket['owner_id']) ) ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $data['ticket_ti_id'] = $ticket['ti_id'];
            $data['ticket_pr_id'] = $ticket['pr_id'];
            $data['ticket_parent_id'] = $ticket['parent_id'];
            $data['ticket_reference'] = $ticket['reference'];
            $data['ticket_owner_id'] = $ticket['owner_id'];
            $ticket['status'] = $this->timber->helpers->fixTicketStatus($ticket['ti_id'], $ticket['status']);
            $data['ticket_status'] = $ticket['status'];
            $data['ticket_type'] = $ticket['type'];
            $data['ticket_depth'] = $ticket['depth'];
            $data['ticket_subject'] = $ticket['subject'];
            $data['ticket_content'] = $ticket['content'];
            $data['ticket_attach'] = $ticket['attach'];
            $data['ticket_created_at'] = $ticket['created_at'];
            $data['ticket_updated_at'] = $ticket['updated_at'];

            $data['ticket_attachments_ids'] = array();
            $data['ticket_attachments'] = array();
            $data['ticket_attachments_count'] = 0;
            # Attachments
            if( $ticket['attach'] == 'on' ){
                $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                    'rec_id' => $ticket['ti_id'],
                    'rec_type' => 10,
                    'me_key' => 'ticket_attachments_data'
                ));

                if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                    $attachments_ids = $attachments_ids->as_array();
                    $data['ticket_attachments_ids'] = unserialize($attachments_ids['me_value']);

                    foreach ($data['ticket_attachments_ids'] as $key => $value) {
                        $file = $this->timber->file_model->getFileById($value);
                        $data['ticket_attachments'][] = $file->as_array();
                    }
                    $data['ticket_attachments_count'] = count($data['ticket_attachments']);
                }
            }

            $data['ticket_attachments_ids'] = implode(',', $data['ticket_attachments_ids']);

        }elseif( ('view' == $sub_tab) && (false !== $tick_id) ){

            $ticket = $this->timber->ticket_model->getTicketByMultiple(array(
                'ti_id' => $tick_id,
                'depth' => '1'
            ));

            if( (false === $ticket) || !(is_object($ticket)) ){
                $this->timber->redirect( $this->timber->config('request_url') . '/404' );
            }

            $ticket = $ticket->as_array();

            $data['ticket_ti_id'] = $ticket['ti_id'];
            $data['ticket_pr_id'] = $ticket['pr_id'];
            $data['ticket_parent_id'] = $ticket['parent_id'];
            $data['ticket_reference'] = $ticket['reference'];
            $data['ticket_owner_id'] = $ticket['owner_id'];
            $ticket['status'] = $this->timber->helpers->fixTicketStatus($ticket['ti_id'], $ticket['status']);
            $data['ticket_status'] = $ticket['status'];
            $data['ticket_type'] = $ticket['type'];
            $data['ticket_depth'] = $ticket['depth'];
            $data['ticket_subject'] = $ticket['subject'];
            $data['ticket_content'] = $ticket['content'];
            $data['ticket_attach'] = $ticket['attach'];
            $data['ticket_created_at'] = $ticket['created_at'];
            $data['ticket_updated_at'] = $ticket['updated_at'];

            $data['ticket_nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('Opened'),  $this->timber->translator->trans('Closed')), $ticket['status']
            );
            $data['ticket_nice_type'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Inquiry'), $this->timber->translator->trans('Suggestion'),  $this->timber->translator->trans('Normal Bug'), $this->timber->translator->trans('Critical Bug'), $this->timber->translator->trans('Security Bug')), $ticket['type']
            );

            $data['ticket_edit_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $ticket['pr_id'] . '?tab=tickets&sub_tab=edit&tick_id=' . $ticket['ti_id'];

            $data['ticket_email'] = "";
            $data['ticket_grav_id'] = "";
            $data['ticket_name'] = "";

            $ticket_owner = $this->timber->user_model->getUserById( $ticket['owner_id'] );

            if( (false !== $ticket_owner) && (is_object($ticket_owner)) ){
                $ticket_owner = $ticket_owner->as_array();
                $data['ticket_email'] = $ticket_owner['email'];
                $data['ticket_grav_id'] = $ticket_owner['grav_id'];
                $data['ticket_name'] = trim( $ticket_owner['first_name'] . " " . $ticket_owner['last_name'] );
            }

            $data['ticket_attachments_ids'] = array();
            $data['ticket_attachments'] = array();
            $data['ticket_attachments_count'] = 0;

            # Attachments
            if( $ticket['attach'] == 'on' ){
                $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                    'rec_id' => $ticket['ti_id'],
                    'rec_type' => 10,
                    'me_key' => 'ticket_attachments_data'
                ));

                if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                    $attachments_ids = $attachments_ids->as_array();
                    $data['ticket_attachments_ids'] = unserialize($attachments_ids['me_value']);

                    foreach ($data['ticket_attachments_ids'] as $key => $value) {
                        $file = $this->timber->file_model->getFileById($value);
                        $data['ticket_attachments'][] = $file->as_array();
                    }
                    $data['ticket_attachments_count'] = count($data['ticket_attachments']);
                }
            }

            $data['ticket_attachments_ids'] = implode(',', $data['ticket_attachments_ids']);

            # Replies
            $data['ticket_replies'] = array();
            $ticket_replies = $this->timber->ticket_model->getTicketsBy(array(
                'parent_id' => $tick_id,
            ), false, false, 'asc');

            $i = 1;
            foreach ($ticket_replies as $key => $ticket_reply) {

                $data['ticket_replies'][$i]['ti_id'] = $ticket_reply['ti_id'];
                $data['ticket_replies'][$i]['pr_id'] = $ticket_reply['pr_id'];
                $data['ticket_replies'][$i]['parent_id'] = $ticket_reply['parent_id'];
                $data['ticket_replies'][$i]['reference'] = $ticket_reply['reference'];
                $data['ticket_replies'][$i]['owner_id'] = $ticket_reply['owner_id'];
                $data['ticket_replies'][$i]['status'] = $ticket_reply['status'];
                $data['ticket_replies'][$i]['type'] = $ticket_reply['type'];
                $data['ticket_replies'][$i]['depth'] = $ticket_reply['depth'];
                $data['ticket_replies'][$i]['subject'] = $ticket_reply['subject'];
                $data['ticket_replies'][$i]['content'] = $ticket_reply['content'];
                $data['ticket_replies'][$i]['attach'] = $ticket_reply['attach'];
                $data['ticket_replies'][$i]['created_at'] = $ticket_reply['created_at'];
                $data['ticket_replies'][$i]['updated_at'] = $ticket_reply['updated_at'];

                $data['ticket_replies'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $ticket_reply['pr_id'] . '?tab=tickets&sub_tab=edit&tick_id=' . $ticket_reply['ti_id'];
                $data['ticket_replies'][$i]['trash_link'] =  $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_ticket';

                $data['ticket_replies'][$i]['email'] = "";
                $data['ticket_replies'][$i]['grav_id'] = "";
                $data['ticket_replies'][$i]['name'] = "";

                $ticket_owner = $this->timber->user_model->getUserById( $ticket_reply['owner_id'] );

                if( (false !== $ticket_owner) && (is_object($ticket_owner)) ){
                    $ticket_owner = $ticket_owner->as_array();
                    $data['ticket_replies'][$i]['email'] = $ticket_owner['email'];
                    $data['ticket_replies'][$i]['grav_id'] = $ticket_owner['grav_id'];
                    $data['ticket_replies'][$i]['name'] = trim( $ticket_owner['first_name'] . " " . $ticket_owner['last_name'] );
                }


                $data['ticket_replies'][$i]['attachments_ids'] = array();
                $data['ticket_replies'][$i]['attachments'] = array();
                $data['ticket_replies'][$i]['attachments_count'] = 0;

                # Attachments
                if( $ticket_reply['attach'] == 'on' ){
                    $attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
                        'rec_id' => $ticket_reply['ti_id'],
                        'rec_type' => 10,
                        'me_key' => 'ticket_attachments_data'
                    ));

                    if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
                        $attachments_ids = $attachments_ids->as_array();
                        $data['ticket_replies'][$i]['attachments_ids'] = unserialize($attachments_ids['me_value']);

                        foreach ($data['ticket_replies'][$i]['attachments_ids'] as $key => $value) {
                            $file = $this->timber->file_model->getFileById($value);
                            $data['ticket_replies'][$i]['attachments'][] = $file->as_array();
                        }
                        $data['ticket_replies'][$i]['attachments_count'] = count($data['ticket_replies'][$i]['attachments']);
                    }
                }
                $data['ticket_replies'][$i]['attachments_ids'] = implode(',', $data['ticket_replies'][$i]['attachments_ids']);

                $i += 1;
            }

        }elseif('add' != $sub_tab){

            # Replies
            $data['tickets'] = array();

            $tickets = $this->timber->ticket_model->getTicketsBy(array(
                'depth' => '1',
                'pr_id' => $project_id
            ));

            $i = 1;
            foreach ($tickets as $key => $ticket) {

                $data['tickets'][$i]['ti_id'] = $ticket['ti_id'];
                $data['tickets'][$i]['pr_id'] = $ticket['pr_id'];
                $data['tickets'][$i]['parent_id'] = $ticket['parent_id'];
                $data['tickets'][$i]['reference'] = $ticket['reference'];
                $data['tickets'][$i]['owner_id'] = $ticket['owner_id'];
                $ticket['status'] = $this->timber->helpers->fixTicketStatus($ticket['ti_id'], $ticket['status']);
                $data['tickets'][$i]['status'] = $ticket['status'];
                $data['tickets'][$i]['type'] = $ticket['type'];
                $data['tickets'][$i]['depth'] = $ticket['depth'];
                $data['tickets'][$i]['subject'] = $ticket['subject'];
                $data['tickets'][$i]['content'] = $ticket['content'];
                $data['tickets'][$i]['attach'] = $ticket['attach'];
                $data['tickets'][$i]['created_at'] = $ticket['created_at'];
                $data['tickets'][$i]['updated_at'] = $ticket['updated_at'];

                $data['tickets'][$i]['nice_status'] = str_replace(
                    array('1','2','3','4'),
                    array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('Opened'),  $this->timber->translator->trans('Closed')), $ticket['status']
                );

                $data['tickets'][$i]['nice_type'] = str_replace(
                    array('1','2','3','4','5'),
                    array($this->timber->translator->trans('Inquiry'), $this->timber->translator->trans('Suggestion'),  $this->timber->translator->trans('Normal Bug'), $this->timber->translator->trans('Critical Bug'), $this->timber->translator->trans('Security Bug')), $ticket['type']
                );

                $data['tickets'][$i]['view_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $project_id .'?tab=tickets&sub_tab=view&tick_id=' . $ticket['ti_id'];
                $data['tickets'][$i]['edit_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $project_id .'?tab=tickets&sub_tab=edit&tick_id=' . $ticket['ti_id'];
                $data['tickets'][$i]['trash_link'] =  $this->timber->config('request_url') . '/request/backend/ajax/projects/delete_ticket';

                $data['tickets'][$i]['email'] = "";
                $data['tickets'][$i]['grav_id'] = "";
                $data['tickets'][$i]['name'] = "";

                $ticket_owner = $this->timber->user_model->getUserById( $ticket['owner_id'] );

                if( (false !== $ticket_owner) && (is_object($ticket_owner)) ){
                    $ticket_owner = $ticket_owner->as_array();
                    $data['tickets'][$i]['email'] = $ticket_owner['email'];
                    $data['tickets'][$i]['grav_id'] = $ticket_owner['grav_id'];
                    $data['tickets'][$i]['name'] = trim( $ticket_owner['first_name'] . " " . $ticket_owner['last_name'] );
                }

                $i += 1;
            }

            $data['tickets_count'] = count($data['tickets']);

        }

        return $data;
    }

    /**
     * Get Project Files
     *
     * @since 1.0
     * @access private
     * @return array
     */
    private function projectFilesData($project_id, $data)
    {

        $data['sync_files_action'] = $this->timber->config('request_url') . '/request/backend/ajax/projects/sync';

        $data['project_attachments'] = array();
        $data['project_attachments_ids'] = array();
        $data['project_attachments_count'] = 0;

        $attachments_ids = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $project_id,
            'me_key' => 'project_attachments_data'
        ));


        if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){

            $attachments_ids = $attachments_ids->as_array();
            $data['project_attachments_ids'] = unserialize($attachments_ids['me_value']);

            $i = 0;
            foreach ($data['project_attachments_ids'] as $key => $value) {

                $file = $this->timber->file_model->getFileById($value);
                $data['project_attachments'][$i] = $file->as_array();

                $data['project_attachments'][$i]['owner_full_name'] = "";
                $data['project_attachments'][$i]['owner_email'] = "";
                $data['project_attachments'][$i]['owner_grav_id'] = "";

                $owner_data = $this->timber->user_model->getUserById( $data['project_attachments'][$i]['owner_id'] );

                if( (false !== $owner_data) && (is_object($owner_data)) ){
                    $owner_data = $owner_data->as_array();

                    $data['project_attachments'][$i]['owner_full_name'] = trim( $owner_data['first_name'] . " " . $owner_data['last_name'] );
                    $data['project_attachments'][$i]['owner_email'] = $owner_data['email'];
                    $data['project_attachments'][$i]['owner_grav_id'] = $owner_data['grav_id'];
                }

                $i += 1;
            }

            $data['project_attachments_count'] = count($data['project_attachments']);

        }

        $data['project_attachments_ids'] = implode(',', $data['project_attachments_ids']);

        return $data;
    }

    /**
     * Get project tasks statistics
     *
     * @since 1.0
     * @access private
     * @param integer $project_id
     * @return array
     */
    private function getProjectTasksStats($project_id)
    {

        $tasks = $this->timber->task_model->getTasksBy(array(
            'pr_id' => $project_id
        ));

        // status => (1-Pending) (2-In Progress) (3-Overdue) (4-Done)
        // priority => (1-Low) (2-Middle) (3-High) (4-Critical)

        $data = array(
            'all_count' => 0,
            'all_items' => array(),

            'random' => array_rand(array(1, 2, 3, 4 )) + 1,
            'status_pending_count' => 0,
            'status_pending_items' => array(),
            'status_in_progress_count' => 0,
            'status_in_progress_items' => array(),
            'status_overdue_count' => 0,
            'status_overdue_items' => array(),
            'status_done_count' => 0,
            'status_done_items' => array(),

            'priority_low_count' => 0,
            'priority_low_items' => array(),
            'priority_middle_count' => 0,
            'priority_middle_items' => array(),
            'priority_high_count' => 0,
            'priority_high_items' => array(),
            'priority_critical_count' => 0,
            'priority_critical_items' => array(),
        );

        foreach ($tasks as $task ) {

            $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);
            $task['progress'] = $this->timber->helpers->measureProgressByDates($task['start_at'], $task['end_at']);

            $task['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $task['status']
            );
            $task['nice_priority'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $task['priority']
            );

            $data['all_count'] += 1;
            $data['all_items'][] = $task;

            if( 1 == $task['status'] ){
                $data['status_pending_count'] += 1;
                $data['status_pending_items'][] = $task;
            }
            if( 2 == $task['status'] ){
                $data['status_in_progress_count'] += 1;
                $data['status_in_progress_items'][] = $task;
            }
            if( 3 == $task['status'] ){
                $data['status_overdue_count'] += 1;
                $data['status_overdue_items'][] = $task;
            }
            if( 4 == $task['status'] ){
                $data['status_done_count'] += 1;
                $data['status_done_items'][] = $task;
            }

            if( 1 == $task['priority'] ){
                $data['priority_low_count'] += 1;
                $data['priority_low_items'][] = $task;
            }
            if( 2 == $task['priority'] ){
                $data['priority_middle_count'] += 1;
                $data['priority_middle_items'][] = $task;
            }
            if( 3 == $task['priority'] ){
                $data['priority_high_count'] += 1;
                $data['priority_high_items'][] = $task;
            }
            if( 4 == $task['priority'] ){
                $data['priority_critical_count'] += 1;
                $data['priority_critical_items'][] = $task;
            }
        }

        return $data;
    }

    /**
     * Get project files statistics
     *
     * @since 1.0
     * @access private
     * @param integer $project_id
     * @return array
     */
    private function getProjectFilesStats($project_id)
    {

        $attachments_ids = $this->timber->project_meta_model->getMetaByMultiple(array(
            'pr_id' => $project_id,
            'me_key' => 'project_attachments_data'
        ));

        $data = array();
        $data['files_count'] = 0;

        if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){

            $attachments_ids = $attachments_ids->as_array();
            $project_attachments_ids = unserialize($attachments_ids['me_value']);
            $data['files_count'] = count($project_attachments_ids);
        }


        return $data;
    }

    /**
     * Get project tickets statistics
     *
     * @since 1.0
     * @access private
     * @param integer $project_id
     * @return array
     */
    private function getProjectTicketsStats($project_id)
    {
        $tickets = $this->timber->ticket_model->getTicketsBy(array(
            'depth' => '1',
            'pr_id' => $project_id
        ));

        // status => (1-Pending) (2-Opened) (3-Closed)
        // type => (1-Inquiry) (2-Suggestion) (3-Normal Bug) (4-Critical Bug) (5-Security Bug)

        $data = array(
            'all_count' => 0,
            'all_items' => array(),

            'random' => array_rand(array(1, 2, 3 )) + 1,
            'status_pending_count' => 0,
            'status_pending_items' => array(),
            'status_opened_count' => 0,
            'status_opened_items' => array(),
            'status_closed_count' => 0,
            'status_closed_items' => array(),

            'priority_inquiry_count' => 0,
            'priority_inquiry_items' => array(),
            'priority_suggestion_count' => 0,
            'priority_suggestion_items' => array(),
            'priority_normal_bug_count' => 0,
            'priority_normal_bug_items' => array(),
            'priority_critical_bug_count' => 0,
            'priority_critical_bug_items' => array(),
            'priority_security_bug_count' => 0,
            'priority_security_bug_items' => array(),
        );

        foreach ($tickets as $ticket ) {

            $ticket['status'] = $this->timber->helpers->fixTicketStatus($ticket['ti_id'], $ticket['status']);
            $ticket['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('Opened'),  $this->timber->translator->trans('Closed')), $ticket['status']
            );
            $ticket['nice_type'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Inquiry'), $this->timber->translator->trans('Suggestion'),  $this->timber->translator->trans('Normal Bug'), $this->timber->translator->trans('Critical Bug'), $this->timber->translator->trans('Security Bug')), $ticket['type']
            );
            $ticket['view_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $project_id .'?tab=tickets&sub_tab=view&tick_id=' . $ticket['ti_id'];

            $data['all_count'] += 1;
            $data['all_items'][] = $ticket;

            if( 1 == $ticket['status'] ){
                $data['status_pending_count'] += 1;
                $data['status_pending_items'][] = $ticket;
            }
            if( 2 == $ticket['status'] ){
                $data['status_opened_count'] += 1;
                $data['status_opened_items'][] = $ticket;
            }
            if( 3 == $ticket['status'] ){
                $data['status_closed_count'] += 1;
                $data['status_closed_items'][] = $ticket;
            }

            if( 1 == $ticket['type'] ){
                $data['priority_inquiry_count'] += 1;
                $data['priority_inquiry_items'][] = $ticket;
            }
            if( 2 == $ticket['type'] ){
                $data['priority_suggestion_count'] += 1;
                $data['priority_suggestion_items'][] = $ticket;
            }
            if( 3 == $ticket['type'] ){
                $data['priority_normal_bug_count'] += 1;
                $data['priority_normal_bug_items'][] = $ticket;
            }
            if( 4 == $ticket['type'] ){
                $data['priority_critical_bug_count'] += 1;
                $data['priority_critical_bug_items'][] = $ticket;
            }
            if( 5 == $ticket['type'] ){
                $data['priority_security_bug_count'] += 1;
                $data['priority_security_bug_items'][] = $ticket;
            }
        }

        return $data;
    }

    /**
     * Get project milestones statistics
     *
     * @since 1.0
     * @access private
     * @param integer $project_id
     * @return array
     */
    private function getProjectMilestonesStats($project_id)
    {
        $milestones = $this->timber->milestone_model->getMilestonesBy(array(
            'pr_id' => $project_id
        ));

        // status => (1-Pending) (2-In Progress) (3-Overdue) (4-Done)
        // priority => (1-Low) (2-Middle) (3-High) (4-Critical)

        $data = array(
            'all_count' => 0,
            'all_items' => array(),

            'random' => array_rand(array(1, 2, 3, 4 )) + 1,
            'status_pending_count' => 0,
            'status_pending_items' => array(),
            'status_in_progress_count' => 0,
            'status_in_progress_items' => array(),
            'status_overdue_count' => 0,
            'status_overdue_items' => array(),
            'status_done_count' => 0,
            'status_done_items' => array(),

            'priority_low_count' => 0,
            'priority_low_items' => array(),
            'priority_middle_count' => 0,
            'priority_middle_items' => array(),
            'priority_high_count' => 0,
            'priority_high_items' => array(),
            'priority_critical_count' => 0,
            'priority_critical_items' => array(),
        );

        foreach ($milestones as $milestone ) {

            $milestone['status'] = $this->timber->helpers->fixMilestoneStatus($project_id, $milestone['mi_id'], $milestone['status']);
            $milestone['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $milestone['status']
            );
            $milestone['nice_priority'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $milestone['priority']
            );

            $data['all_count'] += 1;
            $data['all_items'][] = $milestone;

            if( 1 == $milestone['status'] ){
                $data['status_pending_count'] += 1;
                $data['status_pending_items'][] = $milestone;
            }
            if( 2 == $milestone['status'] ){
                $data['status_in_progress_count'] += 1;
                $data['status_in_progress_items'][] = $milestone;
            }
            if( 3 == $milestone['status'] ){
                $data['status_overdue_count'] += 1;
                $data['status_overdue_items'][] = $milestone;
            }
            if( 4 == $milestone['status'] ){
                $data['status_done_count'] += 1;
                $data['status_done_items'][] = $milestone;
            }

            if( 1 == $milestone['priority'] ){
                $data['priority_low_count'] += 1;
                $data['priority_low_items'][] = $milestone;
            }
            if( 2 == $milestone['priority'] ){
                $data['priority_middle_count'] += 1;
                $data['priority_middle_items'][] = $milestone;
            }
            if( 3 == $milestone['priority'] ){
                $data['priority_high_count'] += 1;
                $data['priority_high_items'][] = $milestone;
            }
            if( 4 == $milestone['priority'] ){
                $data['priority_critical_count'] += 1;
                $data['priority_critical_items'][] = $milestone;
            }
        }

        return $data;
    }

    /**
     * Get Uploader Info
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function uploaderInfo()
    {
        $data = array();

        $allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
        $allowed_extensions = array_map('strtolower', $allowed_extensions);
        $allowed_extensions = implode(', ', $allowed_extensions);
        # $allowed_extensions = ".png, .jpg, .gif";
        $max_file_size = $this->timber->config('_max_upload_size');
        $data['page_uploaders'][] = array(
            'id' => 'uploadModal',
            'title' => $this->timber->translator->trans('Media Uploader'),
            'description' => sprintf( $this->timber->translator->trans('Allowed extensions (%1$s) and Max file size (%2$s MB).'), $allowed_extensions , $max_file_size ),
            'uploader_id' => 'dropzone_uploader',
            'uploader_class' => 'dropzone',
            'field_id' => 'uploader_files',
            'field_name' => 'uploaded_files',
        );

        return $data;
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
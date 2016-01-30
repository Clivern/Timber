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

namespace Timber\Services;

/**
 * Dashboard Data Services
 *
 * @since 1.0
 */
class DashboardData extends \Timber\Services\Base {

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
     * Get Overall Statistics
     *
     * @since 1.0
     * @access public
     * @return array
     */
    public function getOverallStatistics()
    {

    	$data = array();

        $data['site_currency'] = $this->timber->config('_site_currency_symbol');

        $method = str_replace(array(
            'admin',
            'staff',
            'client'
        ), array(
            'getAdminStatistics',
            'getStaffStatistics',
            'getClientStatistics'
        ), $this->timber->access->getRule());

        $data = $this->$method($data);

    	return $data;
    }

    /**
     * Get admin statistics
     *
     * Number Boxes
     * - Members Count
     * - In Progress Projects
     * - Unpaid Invoices
     * - Total Subscriptions
     *
     * List Boxes
     * - In Progress Projects Mini List
     * - Unpaid Invoices Mini List
     * - Total Subscriptions Mini List
     * - Total Estimates Mini List
     *
     * @since 1.0
     * @access public
     * @param array $data
     * @return array
     */
    public function getAdminStatistics($data)
    {

        $data['total_members_count'] = $this->timber->user_model->countUsers();
        $data['in_progress_projects_count'] = $this->timber->project_model->countProjectsBy(array(
            'status' => 2,
        ));
        $data['unpaid_invoices_count'] = $this->timber->invoice_model->countInvoicesBy(array(
            'type' => 1,
            'status' => 3,
        ));
        $data['subscriptions_count'] = $this->timber->subscription_model->countSubscriptions();


        $data['in_progress_projects_mini_list'] = $this->timber->project_model->getProjectsBy(array(
            'status' => 2,
        ), 0, 5);
        $data['unpaid_invoices_mini_list'] = $this->timber->invoice_model->getInvoicesBy(array(
            'type' => 1,
            'status' => 3,
        ), 0, 5);
        $data['subscriptions_mini_list'] = $this->timber->subscription_model->getSubscriptions(0, 5);
        $data['estimates_mini_list'] = $this->timber->invoice_model->getInvoicesBy(array(
            'type' => 2,
        ), 0, 5);

    	return $this->fixAdminStatisticsData($data);
    }

    /**
     * Get staff statistics
     *
     * Number Boxes
     * - In Progress Projects Count
     * - Pending Tickets Count
     * - In Progress Tasks Count
     * - Overdue Tasks Count
     *
     * List Boxes
     * - In Progress Projects Mini List
     * - Pending Tickets Mini List
     * - In Progress Tasks Mini List
     * - Overdue Tasks Mini List
     *
     * @since 1.0
     * @access public
     * @param array $data
     * @return array
     */
    public function getStaffStatistics($data)
    {

        $data['in_progress_projects_count'] = $this->timber->custom_model->countProjectsByStatusAndUser(2, $this->timber->security->getId());
        $data['pending_tickets_count'] = $this->timber->custom_model->countTicketsByStatusAndUser(1, $this->timber->security->getId());
        $data['in_progress_tasks_count'] = $this->timber->task_model->countTasksBy(array(
            'assign_to' => $this->timber->security->getId(),
            'status' => 2,
        ));
        $data['overdue_tasks_count'] = $this->timber->task_model->countTasksBy(array(
            'assign_to' => $this->timber->security->getId(),
            'status' => 3,
        ));


        $data['in_progress_projects_mini_list'] = $this->timber->custom_model->getProjectsByStatusAndUser(2, $this->timber->security->getId(), 0, 5);
        $data['pending_tickets_mini_list'] = $this->timber->custom_model->getTicketsByStatusAndUser(1, $this->timber->security->getId(), 0, 5);
        $data['in_progress_tasks_mini_list'] = $this->timber->task_model->getTasksBy(array(
            'assign_to' => $this->timber->security->getId(),
            'status' => 2,
        ), 0, 5);
        $data['overdue_tasks_mini_list'] = $this->timber->task_model->getTasksBy(array(
            'assign_to' => $this->timber->security->getId(),
            'status' => 3,
        ), 0, 5);

    	return $this->fixStaffStatisticsData($data);
    }

    /**
     * Get client statistics
     *
     * Number Boxes
     * - In Progress Projects Count
     * - Unpaid Invoice Count
     * - Total Subscriptions Count
     * - Total Estimates Count
     *
     * List Boxes
     * - In Progress Projects Mini List
     * - Unpaid Invoice Mini List
     * - Total Subscriptions Mini List
     * - Total Estimates Mini List
     *
     * @since 1.0
     * @access public
     * @param array $data
     * @return array
     */
    public function getClientStatistics($data)
    {

        $data['in_progress_projects_count'] = $this->timber->custom_model->countProjectsByStatusAndUser(2, $this->timber->security->getId());
        $data['unpaid_invoices_count'] = $this->timber->invoice_model->countInvoicesBy(array(
            'client_id' => $this->timber->security->getId(),
            'type' => 1,
            'status' => 3,
        ));
        $data['subscriptions_count'] = $this->timber->subscription_model->countSubscriptionsBy(array(
            'client_id' => $this->timber->security->getId(),
        ));
        $data['total_estimates_count'] = $this->timber->invoice_model->countInvoicesBy(array(
            'client_id' => $this->timber->security->getId(),
            'type' => 2,
        ));

        $data['in_progress_projects_mini_list'] = $this->timber->custom_model->getProjectsByStatusAndUser(2, $this->timber->security->getId(), 0, 5);
        $data['unpaid_invoices_mini_list'] = $this->timber->invoice_model->getInvoicesBy(array(
            'client_id' => $this->timber->security->getId(),
            'type' => 1,
            'status' => 3,
        ), 0, 5);
        $data['subscriptions_mini_list'] = $this->timber->subscription_model->getSubscriptionsBy(array(
            'client_id' => $this->timber->security->getId(),
        ),0, 5);
        $data['estimates_mini_list'] = $this->timber->invoice_model->getInvoicesBy(array(
            'client_id' => $this->timber->security->getId(),
            'type' => 2,
        ), 0, 5);

    	return $this->fixClientStatisticsData($data);
    }

    /**
     * Fix Admin Statistics Data
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return array
     */
    private function fixAdminStatisticsData($data)
    {

        $i = 0;
        foreach ($data['in_progress_projects_mini_list'] as $project) {

            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);

            $data['in_progress_projects_mini_list'][$i] = array();
            $data['in_progress_projects_mini_list'][$i]['pr_id'] = $project['pr_id'];
            $data['in_progress_projects_mini_list'][$i]['title'] = $project['title'];
            $data['in_progress_projects_mini_list'][$i]['reference'] = $project['reference'];
            $data['in_progress_projects_mini_list'][$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
            $data['in_progress_projects_mini_list'][$i]['description'] = $project['description'];
            $data['in_progress_projects_mini_list'][$i]['version'] = $project['version'];
            $data['in_progress_projects_mini_list'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
            $data['in_progress_projects_mini_list'][$i]['budget'] = $project['budget'];
            $data['in_progress_projects_mini_list'][$i]['status'] = $project['status'];
            $data['in_progress_projects_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
            );
            $data['in_progress_projects_mini_list'][$i]['owner_id'] = $project['owner_id'];
            $data['in_progress_projects_mini_list'][$i]['tax'] = $project['tax'];
            $tax = explode('-', $project['tax']);
            $data['in_progress_projects_mini_list'][$i]['tax_value'] = $tax[0];
            $data['in_progress_projects_mini_list'][$i]['tax_type'] = $tax[1];
            $data['in_progress_projects_mini_list'][$i]['discount'] = $project['discount'];
            $discount = explode('-', $project['discount']);
            $data['in_progress_projects_mini_list'][$i]['discount_value'] = $discount[0];
            $data['in_progress_projects_mini_list'][$i]['discount_type'] = $discount[1];
            $data['in_progress_projects_mini_list'][$i]['attach'] = $project['attach'];
            $data['in_progress_projects_mini_list'][$i]['created_at'] = $project['created_at'];
            $data['in_progress_projects_mini_list'][$i]['updated_at'] = $project['updated_at'];
            $data['in_progress_projects_mini_list'][$i]['start_at'] = $project['start_at'];
            $data['in_progress_projects_mini_list'][$i]['end_at'] = $project['end_at'];
            $data['in_progress_projects_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['unpaid_invoices_mini_list'] as $invoice) {

            $data['unpaid_invoices_mini_list'][$i] = array();
            $data['unpaid_invoices_mini_list'][$i]['in_id'] = $invoice['in_id'];
            $data['unpaid_invoices_mini_list'][$i]['reference'] = $invoice['reference'];
            $data['unpaid_invoices_mini_list'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
            $data['unpaid_invoices_mini_list'][$i]['owner_id'] = $invoice['owner_id'];
            $data['unpaid_invoices_mini_list'][$i]['client_id'] = $invoice['client_id'];
            $data['unpaid_invoices_mini_list'][$i]['status'] = $invoice['status'];
            $data['unpaid_invoices_mini_list'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
            $data['unpaid_invoices_mini_list'][$i]['type'] = $invoice['type'];
            $data['unpaid_invoices_mini_list'][$i]['tax'] = $invoice['tax'];
            $data['unpaid_invoices_mini_list'][$i]['discount'] = $invoice['discount'];
            $data['unpaid_invoices_mini_list'][$i]['total'] = $invoice['total'];
            $data['unpaid_invoices_mini_list'][$i]['attach'] = $invoice['attach'];
            $data['unpaid_invoices_mini_list'][$i]['rec_type'] = $invoice['rec_type'];
            $data['unpaid_invoices_mini_list'][$i]['rec_id'] = $invoice['rec_id'];
            $data['unpaid_invoices_mini_list'][$i]['due_date'] = $invoice['due_date'];
            $data['unpaid_invoices_mini_list'][$i]['issue_date'] = $invoice['issue_date'];
            $data['unpaid_invoices_mini_list'][$i]['created_at'] = $invoice['created_at'];
            $data['unpaid_invoices_mini_list'][$i]['updated_at'] = $invoice['updated_at'];
            $data['unpaid_invoices_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['subscriptions_mini_list'] as $subscription) {

            $data['subscriptions_mini_list'][$i] = array();
            $data['subscriptions_mini_list'][$i]['su_id'] = $subscription['su_id'];
            $data['subscriptions_mini_list'][$i]['reference'] = $subscription['reference'];
            $data['subscriptions_mini_list'][$i]['ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);
            $data['subscriptions_mini_list'][$i]['owner_id'] = $subscription['owner_id'];
            $data['subscriptions_mini_list'][$i]['client_id'] = $subscription['client_id'];
            $data['subscriptions_mini_list'][$i]['status'] = $subscription['status'];
            $data['subscriptions_mini_list'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);
            $data['subscriptions_mini_list'][$i]['frequency'] = $subscription['frequency'];
            $data['subscriptions_mini_list'][$i]['tax'] = $subscription['tax'];
            $data['subscriptions_mini_list'][$i]['discount'] = $subscription['discount'];
            $data['subscriptions_mini_list'][$i]['total'] = $subscription['total'];
            $data['subscriptions_mini_list'][$i]['attach'] = $subscription['attach'];
            $data['subscriptions_mini_list'][$i]['begin_at'] = $subscription['begin_at'];
            $data['subscriptions_mini_list'][$i]['end_at'] = $subscription['end_at'];
            $data['subscriptions_mini_list'][$i]['created_at'] = $subscription['created_at'];
            $data['subscriptions_mini_list'][$i]['updated_at'] = $subscription['updated_at'];
            $data['subscriptions_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription['su_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['estimates_mini_list'] as $estimate) {

            $data['estimates_mini_list'][$i] = array();
            $data['estimates_mini_list'][$i]['in_id'] = $estimate['in_id'];
            $data['estimates_mini_list'][$i]['reference'] = $estimate['reference'];
            $data['estimates_mini_list'][$i]['ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);
            $data['estimates_mini_list'][$i]['owner_id'] = $estimate['owner_id'];
            $data['estimates_mini_list'][$i]['client_id'] = $estimate['client_id'];
            $data['estimates_mini_list'][$i]['status'] = $estimate['status'];
            $data['estimates_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5','6'),
                array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
            );
            $data['estimates_mini_list'][$i]['type'] = $estimate['type'];
            $data['estimates_mini_list'][$i]['tax'] = $estimate['tax'];
            $data['estimates_mini_list'][$i]['discount'] = $estimate['discount'];
            $data['estimates_mini_list'][$i]['total'] = $estimate['total'];
            $data['estimates_mini_list'][$i]['attach'] = $estimate['attach'];
            $data['estimates_mini_list'][$i]['rec_type'] = $estimate['rec_type'];
            $data['estimates_mini_list'][$i]['rec_id'] = $estimate['rec_id'];
            $data['estimates_mini_list'][$i]['due_date'] = $estimate['due_date'];
            $data['estimates_mini_list'][$i]['issue_date'] = $estimate['issue_date'];
            $data['estimates_mini_list'][$i]['created_at'] = $estimate['created_at'];
            $data['estimates_mini_list'][$i]['updated_at'] = $estimate['updated_at'];
            $data['estimates_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate['in_id'];


            $i += 1;
        }

        return $data;
    }

    /**
     * Fix Staff Statistics Data
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return array
     */
    private function fixStaffStatisticsData($data)
    {

        $i = 0;
        foreach ($data['in_progress_projects_mini_list'] as $project) {

            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);

            $data['in_progress_projects_mini_list'][$i] = array();
            $data['in_progress_projects_mini_list'][$i]['pr_id'] = $project['pr_id'];
            $data['in_progress_projects_mini_list'][$i]['title'] = $project['title'];
            $data['in_progress_projects_mini_list'][$i]['reference'] = $project['reference'];
            $data['in_progress_projects_mini_list'][$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
            $data['in_progress_projects_mini_list'][$i]['description'] = $project['description'];
            $data['in_progress_projects_mini_list'][$i]['version'] = $project['version'];
            $data['in_progress_projects_mini_list'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
            $data['in_progress_projects_mini_list'][$i]['budget'] = $project['budget'];
            $data['in_progress_projects_mini_list'][$i]['status'] = $project['status'];
            $data['in_progress_projects_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
            );
            $data['in_progress_projects_mini_list'][$i]['owner_id'] = $project['owner_id'];
            $data['in_progress_projects_mini_list'][$i]['tax'] = $project['tax'];
            $tax = explode('-', $project['tax']);
            $data['in_progress_projects_mini_list'][$i]['tax_value'] = $tax[0];
            $data['in_progress_projects_mini_list'][$i]['tax_type'] = $tax[1];
            $data['in_progress_projects_mini_list'][$i]['discount'] = $project['discount'];
            $discount = explode('-', $project['discount']);
            $data['in_progress_projects_mini_list'][$i]['discount_value'] = $discount[0];
            $data['in_progress_projects_mini_list'][$i]['discount_type'] = $discount[1];
            $data['in_progress_projects_mini_list'][$i]['attach'] = $project['attach'];
            $data['in_progress_projects_mini_list'][$i]['created_at'] = $project['created_at'];
            $data['in_progress_projects_mini_list'][$i]['updated_at'] = $project['updated_at'];
            $data['in_progress_projects_mini_list'][$i]['start_at'] = $project['start_at'];
            $data['in_progress_projects_mini_list'][$i]['end_at'] = $project['end_at'];
            $data['in_progress_projects_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['pending_tickets_mini_list'] as $ticket) {

            $ticket['status'] = $this->timber->helpers->fixTicketStatus($ticket['ti_id'], $ticket['status']);

            $data['pending_tickets_mini_list'][$i] = array();
            $data['pending_tickets_mini_list'][$i]['ti_id'] = $ticket['ti_id'];
            $data['pending_tickets_mini_list'][$i]['pr_id'] = $ticket['pr_id'];

            # Get Project Name
            $data['pending_tickets_mini_list'][$i]['pr_title'] = $this->timber->translator->trans("Not Exist");
            $project = $this->timber->project_model->getProjectById($ticket['pr_id']);

            if( (false !== $project) && (is_object($project)) ){
                $project = $project->as_array();
                $data['pending_tickets_mini_list'][$i]['pr_title'] = $project['title'];
            }
            $data['pending_tickets_mini_list'][$i]['parent_id'] = $ticket['parent_id'];
            $data['pending_tickets_mini_list'][$i]['reference'] = $ticket['reference'];
            $data['pending_tickets_mini_list'][$i]['owner_id'] = $ticket['owner_id'];
            $data['pending_tickets_mini_list'][$i]['status'] = $ticket['status'];
            $data['pending_tickets_mini_list'][$i]['type'] = $ticket['type'];
            $data['pending_tickets_mini_list'][$i]['depth'] = $ticket['depth'];
            $data['pending_tickets_mini_list'][$i]['subject'] = $ticket['subject'];
            $data['pending_tickets_mini_list'][$i]['content'] = $ticket['content'];
            $data['pending_tickets_mini_list'][$i]['attach'] = $ticket['attach'];
            $data['pending_tickets_mini_list'][$i]['created_at'] = $ticket['created_at'];
            $data['pending_tickets_mini_list'][$i]['updated_at'] = $ticket['updated_at'];
            $data['pending_tickets_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('Opened'),  $this->timber->translator->trans('Closed')), $ticket['status']
            );
            $data['pending_tickets_mini_list'][$i]['nice_type'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Inquiry'), $this->timber->translator->trans('Suggestion'),  $this->timber->translator->trans('Normal Bug'), $this->timber->translator->trans('Critical Bug'), $this->timber->translator->trans('Security Bug')), $ticket['type']
            );
            $data['pending_tickets_mini_list'][$i]['view_link'] =  $this->timber->config('request_url') . '/admin/projects/view/' . $ticket['pr_id'] .'?tab=tickets&sub_tab=view&tick_id=' . $ticket['ti_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['in_progress_tasks_mini_list'] as $task) {

            $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);

            $data['in_progress_tasks_mini_list'][$i] = array();
            $data['in_progress_tasks_mini_list'][$i]['ta_id'] = $task['ta_id'];

            # Get Project Name
            $data['in_progress_tasks_mini_list'][$i]['pr_title'] = $this->timber->translator->trans("Not Exist");
            $project = $this->timber->project_model->getProjectById($task['pr_id']);
            if( (false !== $project) && (is_object($project)) ){
                $project = $project->as_array();
                $data['in_progress_tasks_mini_list'][$i]['pr_title'] = $project['title'];
            }
            $data['in_progress_tasks_mini_list'][$i]['title'] = $task['title'];
            $data['in_progress_tasks_mini_list'][$i]['description'] = $task['description'];
            $data['in_progress_tasks_mini_list'][$i]['status'] = $task['status'];
            $data['in_progress_tasks_mini_list'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($task['start_at'], $task['end_at']);
            $data['in_progress_tasks_mini_list'][$i]['priority'] = $task['priority'];
            $data['in_progress_tasks_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $task['status']
            );
            $data['in_progress_tasks_mini_list'][$i]['nice_priority'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $task['priority']
            );
            $data['in_progress_tasks_mini_list'][$i]['start_at'] = $task['start_at'];
            $data['in_progress_tasks_mini_list'][$i]['end_at'] = $task['end_at'];
            $data['in_progress_tasks_mini_list'][$i]['created_at'] = $task['created_at'];
            $data['in_progress_tasks_mini_list'][$i]['updated_at'] = $task['updated_at'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['overdue_tasks_mini_list'] as $task) {

            $task['status'] = $this->timber->helpers->fixTaskStatus($task['ta_id'], $task['status'], $task['start_at'], $task['end_at']);

            $data['overdue_tasks_mini_list'][$i] = array();
            $data['overdue_tasks_mini_list'][$i]['ta_id'] = $task['ta_id'];

            # Get Project Name
            $data['overdue_tasks_mini_list'][$i]['pr_title'] = $this->timber->translator->trans("Not Exist");
            $project = $this->timber->project_model->getProjectById($task['pr_id']);
            if( (false !== $project) && (is_object($project)) ){
                $project = $project->as_array();
                $data['overdue_tasks_mini_list'][$i]['pr_title'] = $project['title'];
            }
            $data['overdue_tasks_mini_list'][$i]['title'] = $task['title'];
            $data['overdue_tasks_mini_list'][$i]['description'] = $task['description'];
            $data['overdue_tasks_mini_list'][$i]['status'] = $task['status'];
            $data['overdue_tasks_mini_list'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($task['start_at'], $task['end_at']);
            $data['overdue_tasks_mini_list'][$i]['priority'] = $task['priority'];
            $data['overdue_tasks_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done')), $task['status']
            );
            $data['overdue_tasks_mini_list'][$i]['nice_priority'] = str_replace(
                array('1','2','3','4'),
                array($this->timber->translator->trans('Low'), $this->timber->translator->trans('Middle'),  $this->timber->translator->trans('High'), $this->timber->translator->trans('Critical')), $task['priority']
            );
            $data['overdue_tasks_mini_list'][$i]['start_at'] = $task['start_at'];
            $data['overdue_tasks_mini_list'][$i]['end_at'] = $task['end_at'];
            $data['overdue_tasks_mini_list'][$i]['created_at'] = $task['created_at'];
            $data['overdue_tasks_mini_list'][$i]['updated_at'] = $task['updated_at'];

            $i += 1;
        }

        return $data;
    }

    /**
     * Fix Client Statistics Data
     *
     * @since 1.0
     * @access private
     * @param array $data
     * @return array
     */
    private function fixClientStatisticsData($data)
    {

        $i = 0;
        foreach ($data['in_progress_projects_mini_list'] as $project) {

            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);

            $data['in_progress_projects_mini_list'][$i] = array();
            $data['in_progress_projects_mini_list'][$i]['pr_id'] = $project['pr_id'];
            $data['in_progress_projects_mini_list'][$i]['title'] = $project['title'];
            $data['in_progress_projects_mini_list'][$i]['reference'] = $project['reference'];
            $data['in_progress_projects_mini_list'][$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
            $data['in_progress_projects_mini_list'][$i]['description'] = $project['description'];
            $data['in_progress_projects_mini_list'][$i]['version'] = $project['version'];
            $data['in_progress_projects_mini_list'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
            $data['in_progress_projects_mini_list'][$i]['budget'] = $project['budget'];
            $data['in_progress_projects_mini_list'][$i]['status'] = $project['status'];
            $data['in_progress_projects_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5'),
                array($this->timber->translator->trans('Pending'), $this->timber->translator->trans('In Progress'),  $this->timber->translator->trans('Overdue'), $this->timber->translator->trans('Done'), $this->timber->translator->trans('Archived')), $project['status']
            );
            $data['in_progress_projects_mini_list'][$i]['owner_id'] = $project['owner_id'];
            $data['in_progress_projects_mini_list'][$i]['tax'] = $project['tax'];
            $tax = explode('-', $project['tax']);
            $data['in_progress_projects_mini_list'][$i]['tax_value'] = $tax[0];
            $data['in_progress_projects_mini_list'][$i]['tax_type'] = $tax[1];
            $data['in_progress_projects_mini_list'][$i]['discount'] = $project['discount'];
            $discount = explode('-', $project['discount']);
            $data['in_progress_projects_mini_list'][$i]['discount_value'] = $discount[0];
            $data['in_progress_projects_mini_list'][$i]['discount_type'] = $discount[1];
            $data['in_progress_projects_mini_list'][$i]['attach'] = $project['attach'];
            $data['in_progress_projects_mini_list'][$i]['created_at'] = $project['created_at'];
            $data['in_progress_projects_mini_list'][$i]['updated_at'] = $project['updated_at'];
            $data['in_progress_projects_mini_list'][$i]['start_at'] = $project['start_at'];
            $data['in_progress_projects_mini_list'][$i]['end_at'] = $project['end_at'];
            $data['in_progress_projects_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $project['pr_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['unpaid_invoices_mini_list'] as $invoice) {

            $data['unpaid_invoices_mini_list'][$i] = array();
            $data['unpaid_invoices_mini_list'][$i]['in_id'] = $invoice['in_id'];
            $data['unpaid_invoices_mini_list'][$i]['reference'] = $invoice['reference'];
            $data['unpaid_invoices_mini_list'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
            $data['unpaid_invoices_mini_list'][$i]['owner_id'] = $invoice['owner_id'];
            $data['unpaid_invoices_mini_list'][$i]['client_id'] = $invoice['client_id'];
            $data['unpaid_invoices_mini_list'][$i]['status'] = $invoice['status'];
            $data['unpaid_invoices_mini_list'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
            $data['unpaid_invoices_mini_list'][$i]['type'] = $invoice['type'];
            $data['unpaid_invoices_mini_list'][$i]['tax'] = $invoice['tax'];
            $data['unpaid_invoices_mini_list'][$i]['discount'] = $invoice['discount'];
            $data['unpaid_invoices_mini_list'][$i]['total'] = $invoice['total'];
            $data['unpaid_invoices_mini_list'][$i]['attach'] = $invoice['attach'];
            $data['unpaid_invoices_mini_list'][$i]['rec_type'] = $invoice['rec_type'];
            $data['unpaid_invoices_mini_list'][$i]['rec_id'] = $invoice['rec_id'];
            $data['unpaid_invoices_mini_list'][$i]['due_date'] = $invoice['due_date'];
            $data['unpaid_invoices_mini_list'][$i]['issue_date'] = $invoice['issue_date'];
            $data['unpaid_invoices_mini_list'][$i]['created_at'] = $invoice['created_at'];
            $data['unpaid_invoices_mini_list'][$i]['updated_at'] = $invoice['updated_at'];
            $data['unpaid_invoices_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['subscriptions_mini_list'] as $subscription) {

            $data['subscriptions_mini_list'][$i] = array();
            $data['subscriptions_mini_list'][$i]['su_id'] = $subscription['su_id'];
            $data['subscriptions_mini_list'][$i]['reference'] = $subscription['reference'];
            $data['subscriptions_mini_list'][$i]['ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);
            $data['subscriptions_mini_list'][$i]['owner_id'] = $subscription['owner_id'];
            $data['subscriptions_mini_list'][$i]['client_id'] = $subscription['client_id'];
            $data['subscriptions_mini_list'][$i]['status'] = $subscription['status'];
            $data['subscriptions_mini_list'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);
            $data['subscriptions_mini_list'][$i]['frequency'] = $subscription['frequency'];
            $data['subscriptions_mini_list'][$i]['tax'] = $subscription['tax'];
            $data['subscriptions_mini_list'][$i]['discount'] = $subscription['discount'];
            $data['subscriptions_mini_list'][$i]['total'] = $subscription['total'];
            $data['subscriptions_mini_list'][$i]['attach'] = $subscription['attach'];
            $data['subscriptions_mini_list'][$i]['begin_at'] = $subscription['begin_at'];
            $data['subscriptions_mini_list'][$i]['end_at'] = $subscription['end_at'];
            $data['subscriptions_mini_list'][$i]['created_at'] = $subscription['created_at'];
            $data['subscriptions_mini_list'][$i]['updated_at'] = $subscription['updated_at'];
            $data['subscriptions_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription['su_id'];

            $i += 1;
        }

        $i = 0;
        foreach ($data['estimates_mini_list'] as $estimate) {

            $data['estimates_mini_list'][$i] = array();
            $data['estimates_mini_list'][$i]['in_id'] = $estimate['in_id'];
            $data['estimates_mini_list'][$i]['reference'] = $estimate['reference'];
            $data['estimates_mini_list'][$i]['ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);
            $data['estimates_mini_list'][$i]['owner_id'] = $estimate['owner_id'];
            $data['estimates_mini_list'][$i]['client_id'] = $estimate['client_id'];
            $data['estimates_mini_list'][$i]['status'] = $estimate['status'];
            $data['estimates_mini_list'][$i]['nice_status'] = str_replace(
                array('1','2','3','4','5','6'),
                array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
            );
            $data['estimates_mini_list'][$i]['type'] = $estimate['type'];
            $data['estimates_mini_list'][$i]['tax'] = $estimate['tax'];
            $data['estimates_mini_list'][$i]['discount'] = $estimate['discount'];
            $data['estimates_mini_list'][$i]['total'] = $estimate['total'];
            $data['estimates_mini_list'][$i]['attach'] = $estimate['attach'];
            $data['estimates_mini_list'][$i]['rec_type'] = $estimate['rec_type'];
            $data['estimates_mini_list'][$i]['rec_id'] = $estimate['rec_id'];
            $data['estimates_mini_list'][$i]['due_date'] = $estimate['due_date'];
            $data['estimates_mini_list'][$i]['issue_date'] = $estimate['issue_date'];
            $data['estimates_mini_list'][$i]['created_at'] = $estimate['created_at'];
            $data['estimates_mini_list'][$i]['updated_at'] = $estimate['updated_at'];
            $data['estimates_mini_list'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate['in_id'];

            $i += 1;
        }

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
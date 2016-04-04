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
 * Members Data Services
 *
 * @since 1.0
 */
class MembersData extends \Timber\Services\Base {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 * @access public
	 * @param object $timber
	 */
	public function __construct($timber) {
		parent::__construct($timber);
	}

	/**
	 * Get a list of members
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function listData()
	{
		# Get & Bind Members
		$data = array();

		$records_start = ( (isset($_POST['records_start'])) && (filter_var($_POST['records_start'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['records_start'], FILTER_SANITIZE_NUMBER_INT) : 0;
		$records_offset = 20;

		$members = $this->timber->user_model->getUsers( $records_start, $records_offset, 'desc', 'created_at' );

		$i = 1;
		$data['members'] = array();
		foreach ($members as $key => $member) {
			$data['members'][$i]['status'] = $member['status'];
			$data['members'][$i]['us_id'] = $member['us_id'];
			$data['members'][$i]['user_name'] = $member['user_name'];
			$data['members'][$i]['first_name'] = $member['first_name'];
			$data['members'][$i]['last_name'] = $member['last_name'];
			$data['members'][$i]['full_name'] = trim($member['first_name'] . ' ' . $member['last_name']);
			$data['members'][$i]['nice_name'] = ( empty($data['members'][$i]['full_name']) ) ? $data['members'][$i]['user_name'] : $data['members'][$i]['full_name'];
			$data['members'][$i]['company'] = $member['company'];
			$data['members'][$i]['phone_num'] = $member['phone_num'];
			$data['members'][$i]['zip_code'] = $member['zip_code'];
			$data['members'][$i]['vat_nubmer'] = $member['vat_nubmer'];
			$data['members'][$i]['language'] = $member['language'];
			$data['members'][$i]['country'] = $member['country'];
			$data['members'][$i]['city'] = $member['city'];
			$data['members'][$i]['address1'] = $member['address1'];
			$data['members'][$i]['address2'] = $member['address2'];
			$data['members'][$i]['email'] = $member['email'];
			$data['members'][$i]['website'] = $member['website'];
			$data['members'][$i]['job'] = $member['job'];
			$data['members'][$i]['grav_id'] = $member['grav_id'];
			$data['members'][$i]['auth_by'] = $member['auth_by'];
			$data['members'][$i]['access_rule'] = $member['access_rule'];
			$data['members'][$i]['identifier'] = $member['identifier'];
			$data['members'][$i]['created_at'] = $member['created_at'];
			$data['members'][$i]['updated_at'] = $member['updated_at'];
			$data['members'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/members/view/' . $member['us_id'];
			$data['members'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/members/edit/' . $member['us_id'];
			$data['members'][$i]['delete_link'] = $this->timber->config('request_url') . '/request/backend/ajax/members/delete_member';

			# $this->timber->user_meta_model->getMetasByUserId($member['us_id']);

			$i += 1;
		}

		return $data;
	}

	/**
	 * Get add data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function addData()
	{
		# Bind Actions

		# Get & Bind Members
		$data = array();

		$data['languages_list'] = $this->timber->translator->getLocales();
		$data['countries_list'] = $this->timber->helpers->getCountries();
		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/members/add_member';

		return $data;
	}

	/**
	 * Get edit Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function editData($member_id)
	{
		# Bind Actions

		# Get & Bind Members
		$data = array();

		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/members/update_member_profile';

		$member_id = ((boolean) filter_var($member_id, FILTER_VALIDATE_INT)) ? filter_var($member_id, FILTER_SANITIZE_NUMBER_INT) : false;

		if($member_id === false){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$member = $this->timber->user_model->getUserById($member_id);

		if( (false === $member) || !(is_object($member)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$member = $member->as_array();

		if( ($this->timber->access->getRule() == 'staff') && ($member['access_rule'] != '3') ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		if( ($this->timber->access->getRule() == 'staff') &&  !($this->timber->access->checkPermission('edit.clients')) && ($member['access_rule'] == '3') ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$data['us_id'] = $member['us_id'];
		$data['user_name'] = $member['user_name'];
		$data['first_name'] = $member['first_name'];
		$data['last_name'] = $member['last_name'];
		$data['full_name'] = trim($member['first_name'] . ' ' . $member['last_name']);
		$data['nice_name'] = ( empty($data['full_name']) ) ? $member['user_name'] : $data['full_name'];
		$data['company'] = $member['company'];
		$data['phone_num'] = $member['phone_num'];
		$data['email'] = $member['email'];
		$data['website'] = $member['website'];
		$data['zip_code'] = $member['zip_code'];
		$data['vat_nubmer'] = $member['vat_nubmer'];
		$data['language'] = $member['language'];
		$data['job'] = $member['job'];
		$data['grav_id'] = $member['grav_id'];
		$data['country'] = $member['country'];
		$data['city'] = $member['city'];
		$data['address1'] = $member['address1'];
		$data['address2'] = $member['address2'];
		$data['auth_by'] = $member['auth_by'];
		$data['access_rule'] = $member['access_rule'];
		$data['status'] = $member['status'];
		$data['created_at'] = $member['created_at'];
		$data['updated_at'] = $member['updated_at'];

		$data['social_login'] = ($member['auth_by'] == '1') ? 'off' : 'on';

		$data['languages_list'] = $this->timber->translator->getLocales();
		$data['countries_list'] = $this->timber->helpers->getCountries();

        if( !empty($data['nice_name']) ){
            $data['site_sub_page'] = $data['nice_name']  . " | ";
        }

		return $data;
	}

	/**
	 * Get view Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function viewData($member_id)
	{
		$data = array();

		$user_id = $this->timber->security->getId();

		$member_id = ((boolean) filter_var($member_id, FILTER_VALIDATE_INT)) ? filter_var($member_id, FILTER_SANITIZE_NUMBER_INT) : false;

		if($member_id === false){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$member = $this->timber->user_model->getUserById($member_id);

		if( (false === $member) || !(is_object($member)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$member = $member->as_array();

		if( ($this->timber->access->getRule() == 'staff') && ($member['access_rule'] != '3') ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		if( ($this->timber->access->getRule() == 'staff') &&  !($this->timber->access->checkPermission('view.clients')) && ($member['access_rule'] == '3') ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

        $data['site_currency'] = $this->timber->config('_site_currency_symbol');
		$data['us_id'] = $member['us_id'];
		$data['user_name'] = $member['user_name'];
		$data['first_name'] = $member['first_name'];
		$data['last_name'] = $member['last_name'];
		$data['full_name'] = trim($member['first_name'] . ' ' . $member['last_name']);
		$data['nice_name'] = ( empty($data['full_name']) ) ? $member['user_name'] : $data['full_name'];
        $data['email'] = $member['email'];
		$data['website'] = $member['website'];
		$data['zip_code'] = $member['zip_code'];
		$data['vat_nubmer'] = $member['vat_nubmer'];
		$data['language'] = $member['language'];
		$data['job'] = $member['job'];
		$data['grav_id'] = $member['grav_id'];
		$data['country'] = $this->timber->helpers->getCountryFromID($member['country']);
		$data['city'] = $member['city'];
		$data['address1'] = $member['address1'];
		$data['address2'] = $member['address2'];
		$data['auth_by'] = $member['auth_by'];
		$data['access_rule'] = $member['access_rule'];
		$data['status'] = $member['status'];
		$data['created_at'] = $member['created_at'];
		$data['updated_at'] = $member['updated_at'];
		$data['social_login'] = ($member['auth_by'] == '1') ? 'off' : 'on';

        if( !empty($data['nice_name']) ){
            $data['site_sub_page'] = $data['nice_name']  . " | ";
        }

        # Get Projects (Staff & Admins & Clients)
        if( in_array($member['access_rule'], array(1, 2, 3)) ){

        	if( in_array($member['access_rule'], array(2, 3)) ){
				$projects = $this->timber->custom_model->getProjectsByUser($data['us_id']);
			}elseif( in_array($member['access_rule'], array(1)) ){
				$projects = $this->timber->project_model->getProjectsBy(array(
					'owner_id' => $data['us_id'],
				), false, false, 'desc', 'created_at' );
			}

        	$data['projects'] = array();
        	$i = 0;
        	foreach ($projects as $project) {

	            $project['status'] = $this->timber->helpers->fixProjectStatus($project['pr_id'], $project['status'], $project['start_at'], $project['end_at']);
	            $data['projects'][$i] = array();
	            $data['projects'][$i]['pr_id'] = $project['pr_id'];
	            $data['projects'][$i]['title'] = $project['title'];
	            $data['projects'][$i]['reference'] = $project['reference'];
	            $data['projects'][$i]['ref_id'] = "PRO-" . str_pad($project['pr_id'], 8, '0', STR_PAD_LEFT);
	            $data['projects'][$i]['description'] = $project['description'];
	            $data['projects'][$i]['version'] = $project['version'];
	            $data['projects'][$i]['progress'] = $this->timber->helpers->measureProgressByDates($project['start_at'], $project['end_at']);
	            $data['projects'][$i]['budget'] = $project['budget'];
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

            	$i += 1;
        	}
        }

        # Get Tasks (Staff & Admins)
        if( in_array($member['access_rule'], array(1,2)) ){

            $tasks = $this->timber->task_model->getTasksBy(array(
                'assign_to' => $data['us_id'],
            ));

            $data['tasks'] = array();
            $i = 0;
            foreach ($tasks as $task) {
                $data['tasks'][$i]['ta_id'] = $task['ta_id'];
                $data['tasks'][$i]['mi_id'] = $task['mi_id'];
                $data['tasks'][$i]['pr_id'] = $task['pr_id'];

                # Get Project Name
                $data['tasks'][$i]['pr_title'] = $this->timber->translator->trans("Not Exist");
                $project = $this->timber->project_model->getProjectById($task['pr_id']);
                if( (false !== $project) && (is_object($project)) ){
                    $project = $project->as_array();
                    $data['tasks'][$i]['pr_title'] = $project['title'];
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

                $i += 1;
            }
        }

        # Get Subscriptions (Clients)
        if( in_array($member['access_rule'], array(3)) ){

            $subscriptions = $this->timber->subscription_model->getSubscriptionsBy(array(
                'client_id' => $data['us_id'],
            ));

            $data['subscriptions'] = array();
            $i = 0;
            foreach ($subscriptions as $subscription) {
                $data['subscriptions'][$i]['su_id'] = $subscription['su_id'];
                $data['subscriptions'][$i]['reference'] = $subscription['reference'];
                $data['subscriptions'][$i]['ref_id'] = "SUB-" . str_pad($subscription['su_id'], 8, '0', STR_PAD_LEFT);
                $data['subscriptions'][$i]['owner_id'] = $subscription['owner_id'];
                $data['subscriptions'][$i]['client_id'] = $subscription['client_id'];
                $data['subscriptions'][$i]['status'] = $subscription['status'];
                $data['subscriptions'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $subscription['status']);
                $data['subscriptions'][$i]['frequency'] = $subscription['frequency'];
                $data['subscriptions'][$i]['tax'] = $subscription['tax'];
                $data['subscriptions'][$i]['discount'] = $subscription['discount'];
                $data['subscriptions'][$i]['total'] = $subscription['total'];
                $data['subscriptions'][$i]['attach'] = $subscription['attach'];
                $data['subscriptions'][$i]['begin_at'] = $subscription['begin_at'];
                $data['subscriptions'][$i]['end_at'] = $subscription['end_at'];
                $data['subscriptions'][$i]['created_at'] = $subscription['created_at'];
                $data['subscriptions'][$i]['updated_at'] = $subscription['updated_at'];
                $data['subscriptions'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/subscriptions/view/' . $subscription['su_id'];

                $i += 1;
            }
        }

        # Get Invoices (Clients)
        if( in_array($member['access_rule'], array(3)) ){

            $invoices = $this->timber->invoice_model->getInvoicesBy(array(
                'client_id' => $data['us_id'],
                'type' => 1
            ));

            $data['invoices'] = array();
            $i = 0;
            foreach ($invoices as $invoice) {
                $data['invoices'][$i]['in_id'] = $invoice['in_id'];
                $data['invoices'][$i]['reference'] = $invoice['reference'];
                $data['invoices'][$i]['ref_id'] = "INV-" . str_pad($invoice['in_id'], 8, '0', STR_PAD_LEFT);
                $data['invoices'][$i]['owner_id'] = $invoice['owner_id'];
                $data['invoices'][$i]['client_id'] = $invoice['client_id'];
                $data['invoices'][$i]['status'] = $invoice['status'];
                $data['invoices'][$i]['nice_status'] = str_replace(array('1','2','3'), array($this->timber->translator->trans('Paid'), $this->timber->translator->trans('Partially Paid'), $this->timber->translator->trans('Unpaid')), $invoice['status']);
                $data['invoices'][$i]['type'] = $invoice['type'];
                $data['invoices'][$i]['tax'] = $invoice['tax'];
                $data['invoices'][$i]['discount'] = $invoice['discount'];
                $data['invoices'][$i]['total'] = $invoice['total'];
                $data['invoices'][$i]['attach'] = $invoice['attach'];
                $data['invoices'][$i]['rec_type'] = $invoice['rec_type'];
                $data['invoices'][$i]['rec_id'] = $invoice['rec_id'];
                $data['invoices'][$i]['due_date'] = $invoice['due_date'];
                $data['invoices'][$i]['issue_date'] = $invoice['issue_date'];
                $data['invoices'][$i]['created_at'] = $invoice['created_at'];
                $data['invoices'][$i]['updated_at'] = $invoice['updated_at'];
                $data['invoices'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/invoices/view/' . $invoice['in_id'];

                $i += 1;
            }
        }

        # Get Tickets (Clients)
        if( in_array($member['access_rule'], array(3)) ){

            $tickets = $this->timber->ticket_model->getTicketsBy(array(
                'owner_id' => $data['us_id'],
            ));

            $data['tickets'] = array();
            $i = 0;
            foreach ($tickets as $ticket) {
                $data['tickets'][$i]['ti_id'] = $ticket['ti_id'];
                $data['tickets'][$i]['pr_id'] = $ticket['pr_id'];

                # Get Project Name
                $data['tickets'][$i]['pr_title'] = $this->timber->translator->trans("Not Exist");
                $project = $this->timber->project_model->getProjectById($task['pr_id']);
                if( (false !== $project) && (is_object($project)) ){
                    $project = $project->as_array();
                    $data['tickets'][$i]['pr_title'] = $project['title'];
                }

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

                $i += 1;
            }
        }

        # Get Estimates (Clients)
        if( in_array($member['access_rule'], array(3)) ){

            $estimates = $this->timber->invoice_model->getInvoicesBy(array(
                'client_id' => $data['us_id'],
                'type' => 2,
            ));

            $data['estimates'] = array();
            $i = 0;
            foreach ($estimates as $estimate) {
                $data['estimates'][$i]['in_id'] = $estimate['in_id'];
                $data['estimates'][$i]['reference'] = $estimate['reference'];
                $data['estimates'][$i]['ref_id'] = "EST-" . str_pad($estimate['in_id'], 8, '0', STR_PAD_LEFT);
                $data['estimates'][$i]['owner_id'] = $estimate['owner_id'];
                $data['estimates'][$i]['client_id'] = $estimate['client_id'];
                $data['estimates'][$i]['status'] = $estimate['status'];
                $data['estimates'][$i]['nice_status'] = str_replace(
                    array('1','2','3','4','5','6'),
                    array($this->timber->translator->trans('Opened'), $this->timber->translator->trans('Sent'), $this->timber->translator->trans('Accepted'), $this->timber->translator->trans('Rejected'), $this->timber->translator->trans('Invoiced'), $this->timber->translator->trans('Closed')), $estimate['status']
                );
                $data['estimates'][$i]['type'] = $estimate['type'];
                $data['estimates'][$i]['tax'] = $estimate['tax'];
                $data['estimates'][$i]['discount'] = $estimate['discount'];
                $data['estimates'][$i]['total'] = $estimate['total'];
                $data['estimates'][$i]['attach'] = $estimate['attach'];
                $data['estimates'][$i]['rec_type'] = $estimate['rec_type'];
                $data['estimates'][$i]['rec_id'] = $estimate['rec_id'];
                $data['estimates'][$i]['due_date'] = $estimate['due_date'];
                $data['estimates'][$i]['issue_date'] = $estimate['issue_date'];
                $data['estimates'][$i]['created_at'] = $estimate['created_at'];
                $data['estimates'][$i]['updated_at'] = $estimate['updated_at'];
                $data['estimates'][$i]['view_link'] = $this->timber->config('request_url') . '/admin/estimates/view/' . $estimate['in_id'];

                $i += 1;
            }
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

		# $allowed_extensions = unserialize($this->timber->config('_allowed_upload_extensions'));
		# $allowed_extensions = implode(', ', $allowed_extensions);
		$allowed_extensions = ".png, .jpg";
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
}
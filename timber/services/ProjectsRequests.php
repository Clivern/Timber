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
 * Projects Requests Services
 *
 * @since 1.0
 */
class ProjectsRequests extends \Timber\Services\Base {

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
	 * Add New Project
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addProject()
	{

		$project_data = $this->timber->validator->clear(array(
			'project_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Project title is invalid.'),
				),
			),
			'project_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,1500',
				'default' => '',
				'errors' => array(),
			),
			'project_version' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:1,18',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project version is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Project version is invalid.'),
				),
			),
			'project_budget' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Project budget is invalid.'),
				),
			),
			'project_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'project_tax_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:off,percent,flat',
				'default' => 'off',
				'errors' => array(),
			),
			'project_tax_value' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '0',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Tax value is invalid.'),
				),
			),
			'project_discount_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:off,percent,flat',
				'default' => 'off',
				'errors' => array(),
			),
			'project_discount_value' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '0',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Discount value is invalid.'),
				),
			),
			'project_start_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Start date is invalid.'),
					'vdate' => $this->timber->translator->trans('Start date is invalid.'),
				),
			),
			'project_end_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
					'vdate' => $this->timber->translator->trans('End date is invalid.'),
				),
			),
			'project_staff' => array(
				'req' => 'post',
				'sanit' => 'susersids',
				'valid' => 'vnotempty&vusersids',
				'default' => array(),
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project staff is invalid.'),
					'vusersids' => $this->timber->translator->trans('Project staff is invalid.'),
				),
			),
			'project_clients' => array(
				'req' => 'post',
				'sanit' => 'susersids',
				'valid' => 'vnotempty&vusersids',
				'default' => array(),
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project clients is invalid.'),
					'vusersids' => $this->timber->translator->trans('Project clients is invalid.'),
				),
			),
		));

		if( true === $project_data['error_status'] ){
			$this->response['data'] = $project_data['error_text'];
			return false;
		}

		if( !($project_data['project_end_at']['value'] >= $project_data['project_start_at']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('End date must not be less than start date.');
			return false;
		}

		$new_project_data = array();

		$new_project_data['title'] = $project_data['project_title']['value'];
		$new_project_data['reference'] = $this->timber->project_model->newReference("PRO");
		$new_project_data['description'] = $project_data['project_description']['value'];
		$new_project_data['version'] = $project_data['project_version']['value'];
		$new_project_data['progress'] = 1;
		$new_project_data['budget'] = $project_data['project_budget']['value'];
		$new_project_data['status'] = $project_data['project_status']['value'];
		$new_project_data['owner_id'] = $this->timber->security->getId();
		$new_project_data['tax'] = $project_data['project_tax_value']['value'] . '-' . $project_data['project_tax_type']['value'];
		$new_project_data['discount'] = $project_data['project_discount_value']['value'] . '-' . $project_data['project_discount_type']['value'];
		$new_project_data['attach'] = 'on';
		$new_project_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_project_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_project_data['start_at'] = $project_data['project_start_at']['value'];
		$new_project_data['end_at'] = $project_data['project_end_at']['value'];

		$project_id = $this->timber->project_model->addProject($new_project_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->project_meta_model->addMeta(array(
			'pr_id' => $project_id,
			'me_key' => 'project_staff_members',
			'me_value' => serialize($project_data['project_staff']['value']),
		));

		$meta_status &= (boolean) $this->timber->project_meta_model->addMeta(array(
			'pr_id' => $project_id,
			'me_key' => 'project_clients_members',
			'me_value' => serialize($project_data['project_clients']['value']),
		));

		$meta_status &= (boolean) $this->timber->project_meta_model->addMeta(array(
			'pr_id' => $project_id,
			'me_key' => 'project_attachments_data',
			'me_value' => serialize(array()),
		));

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $project_id,
			'rec_type' => 11,
			'me_key' => 'project_members_list',
			'me_value' => '|' . implode('|', array_merge($project_data['project_staff']['value'], $project_data['project_clients']['value'])) . '|',
		));

		# New Project Notification
		$this->timber->notify->increment('projects_notif', $new_project_data['owner_id']);
		foreach ($project_data['project_staff']['value'] as $user_id ) {
			$this->timber->notify->increment('projects_notif', $user_id);
		}

		foreach ($project_data['project_clients']['value'] as $user_id ) {
			$this->timber->notify->increment('projects_notif', $user_id);
		}

 		$this->timber->notify->setMailerCron(array(
 			'method_name' => 'newProjectEmailNotifier',
 			'project_id' => $project_id,
 		));


		if( $project_id && $meta_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Project created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/projects/edit/' . $project_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Project
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editProject()
	{
		$project_data = $this->timber->validator->clear(array(
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'project_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Project title is invalid.'),
				),
			),
			'project_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,1500',
				'default' => '',
				'errors' => array(),
			),
			'project_version' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:1,18',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project version is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Project version is invalid.'),
				),
			),
			'project_budget' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Project budget is invalid.'),
				),
			),
			'project_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'project_tax_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:off,percent,flat',
				'default' => 'off',
				'errors' => array(),
			),
			'project_tax_value' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '0',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Tax value is invalid.'),
				),
			),
			'project_discount_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:off,percent,flat',
				'default' => 'off',
				'errors' => array(),
			),
			'project_discount_value' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:,,,0',
				'default' => '0',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Discount value is invalid.'),
				),
			),
			'project_start_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Start date is invalid.'),
					'vdate' => $this->timber->translator->trans('Start date is invalid.'),
				),
			),
			'project_end_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
					'vdate' => $this->timber->translator->trans('End date is invalid.'),
				),
			),
			'project_staff' => array(
				'req' => 'post',
				'sanit' => 'susersids',
				'valid' => 'vnotempty&vusersids',
				'default' => array(),
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project staff is invalid.'),
					'vusersids' => $this->timber->translator->trans('Project staff is invalid.'),
				),
			),
			'project_clients' => array(
				'req' => 'post',
				'sanit' => 'susersids',
				'valid' => 'vnotempty&vusersids',
				'default' => array(),
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Project clients is invalid.'),
					'vusersids' => $this->timber->translator->trans('Project clients is invalid.'),
				),
			),
		));

		if( true === $project_data['error_status'] ){
			$this->response['data'] = $project_data['error_text'];
			return false;
		}

		$project = $this->timber->project_model->getProjectById($project_data['pro_id']['value']);

		if( (false === $project) || !(is_object($project)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($project_data['project_end_at']['value'] >= $project_data['project_start_at']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('End date must not be less than start date.');
			return false;
		}

		$project = $project->as_array();

		$new_project_data = array();
		$new_project_data['pr_id'] = $project_data['pro_id']['value'];
		$new_project_data['title'] = $project_data['project_title']['value'];
		$new_project_data['description'] = $project_data['project_description']['value'];
		$new_project_data['version'] = $project_data['project_version']['value'];
		$new_project_data['progress'] = 1;
		$new_project_data['budget'] = $project_data['project_budget']['value'];
		$new_project_data['status'] = $project_data['project_status']['value'];
		$new_project_data['tax'] = $project_data['project_tax_value']['value'] . '-' . $project_data['project_tax_type']['value'];
		$new_project_data['discount'] = $project_data['project_discount_value']['value'] . '-' . $project_data['project_discount_type']['value'];
		$new_project_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_project_data['start_at'] = $project_data['project_start_at']['value'];
		$new_project_data['end_at'] = $project_data['project_end_at']['value'];

		$action_status  = (boolean) $this->timber->project_model->updateProjectById($new_project_data);

		$action_status &= (boolean) $this->timber->project_meta_model->updateMetaByMultiple(array(
			'pr_id' => $project_data['pro_id']['value'],
			'me_key' => 'project_staff_members',
			'me_value' => serialize($project_data['project_staff']['value']),
		));

		$action_status &= (boolean) $this->timber->project_meta_model->updateMetaByMultiple(array(
			'pr_id' => $project_data['pro_id']['value'],
			'me_key' => 'project_clients_members',
			'me_value' => serialize($project_data['project_clients']['value']),
		));

		$action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
			'rec_id' => $project_data['pro_id']['value'],
			'rec_type' => 11,
			'me_key' => 'project_members_list',
			'me_value' => '|' . implode('|', array_merge($project_data['project_staff']['value'], $project_data['project_clients']['value'])) . '|',
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Project updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Project
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteProject()
	{
		$project_id = ( (isset($_POST['project_id'])) && ((boolean) filter_var($_POST['project_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['project_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $project_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->project_model->deleteProjectById($project_id);
		$action_status &= (boolean) $this->timber->project_meta_model->dumpProjectMetas($project_id);
		$action_status &= (boolean) $this->timber->milestone_model->dumpMilestone(false, $project_id);
		$action_status &= (boolean) $this->timber->task_model->dumpTask(false, false, $project_id);
		$action_status &= (boolean) $this->timber->ticket_model->dumpTicket(false, $project_id);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Project deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Add & Edit Project Files
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function syncFiles()
	{
		$project_data = $this->timber->validator->clear(array(
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'pro_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
			'pro_old_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfilesids',
				'valid' => 'vfilesids',
				'default' => array(),
				'errors' => array(),
			),
		));

		if( true === $project_data['error_status'] ){
			$this->response['data'] = $project_data['error_text'];
			return false;
		}

		# Add Attachments
		$pro_attachments = $project_data['pro_attachments']['value'];

		$files_ids = array();

		$attachments_ids = $this->timber->project_meta_model->getMetaByMultiple(array(
			'pr_id' => $project_data['pro_id']['value'],
			'me_key' => 'project_attachments_data'
		));

		if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
			$attachments_ids = $attachments_ids->as_array();
			$files_ids = unserialize($attachments_ids['me_value']);

			foreach ($files_ids as $key => $value) {
				if( !in_array( $value, $project_data['pro_old_attachments']['value'] ) ){
					unset($files_ids[$key]);
				}
			}
		}


		if( (is_array($pro_attachments)) && (count($pro_attachments) > 0) ){
			foreach( $pro_attachments as $pro_attachment ) {
				$pro_attachment = explode('--||--', $pro_attachment);
				$file_id = $this->timber->file_model->addFile(array(
					'title' => $pro_attachment[1],
					'hash' => $pro_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Project Attachments",
					'storage' => 2,
					'type' => pathinfo($pro_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));

				$files_ids[] = $file_id;

 				$this->timber->notify->setMailerCron(array(
 					'method_name' => 'projectFileEmailNotifier',
 					'project_id' => $project_data['pro_id']['value'],
 					'file_id' => $file_id,
 				));
			}
		}


		$action_status = (boolean) $this->timber->project_meta_model->updateMetaByMultiple(array(
			'pr_id' => $project_data['pro_id']['value'],
			'me_key' => 'project_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Project files updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Add Milestone
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addMilestone()
	{
		$milestone_data = $this->timber->validator->clear(array(
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'milestone_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Milestone title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Milestone title is invalid.'),
				),
			),
			'milestone_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'milestone_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'milestone_priority' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
		));

		if( true === $milestone_data['error_status'] ){
			$this->response['data'] = $milestone_data['error_text'];
			return false;
		}

		$new_milestone_data = array();

		$new_milestone_data['title'] = $milestone_data['milestone_title']['value'];
		$new_milestone_data['pr_id'] = $milestone_data['pro_id']['value'];
		$new_milestone_data['description'] = $milestone_data['milestone_description']['value'];
		$new_milestone_data['priority'] = $milestone_data['milestone_priority']['value'];
		$new_milestone_data['status'] = $milestone_data['milestone_status']['value'];
		$new_milestone_data['owner_id'] = $this->timber->security->getId();
		$new_milestone_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_milestone_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		$milestone_id = $this->timber->milestone_model->addMilestone($new_milestone_data);

 		$this->timber->notify->setMailerCron(array(
 			'method_name' => 'projectMilestoneEmailNotifier',
 			'project_id' => $new_milestone_data['pr_id'],
 			'miles_id' => $milestone_id,
 		));

		if( $milestone_id ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Milestone created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $new_milestone_data['pr_id'].'?tab=milestones&sub_tab=edit&miles_id=' . $milestone_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Milestone
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editMilestone()
	{
		$milestone_data = $this->timber->validator->clear(array(
			'mi_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'milestone_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Milestone title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Milestone title is invalid.'),
				),
			),
			'milestone_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'milestone_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'milestone_priority' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
		));

		if( true === $milestone_data['error_status'] ){
			$this->response['data'] = $milestone_data['error_text'];
			return false;
		}

		$new_milestone_data = array();
		$new_milestone_data['mi_id'] = $milestone_data['mi_id']['value'];
		$new_milestone_data['title'] = $milestone_data['milestone_title']['value'];
		$new_milestone_data['description'] = $milestone_data['milestone_description']['value'];
		$new_milestone_data['priority'] = $milestone_data['milestone_priority']['value'];
		$new_milestone_data['status'] = $milestone_data['milestone_status']['value'];
		$new_milestone_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		$action_status  = (boolean) $this->timber->milestone_model->updateMilestoneById($new_milestone_data);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Milestone updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Milestone
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteMilestone()
	{
		$milestone_id = ( (isset($_POST['milestone_id'])) && ((boolean) filter_var($_POST['milestone_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['milestone_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $milestone_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->milestone_model->deleteMilestoneById($milestone_id);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Milestone deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Add Task
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addTask()
	{
		$task_data = $this->timber->validator->clear(array(
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'mi_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select milestone.'),
					'vint' => $this->timber->translator->trans('Please select milestone.')
				),
			),
			'assign_to' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please assign task to someone.'),
					'vint' => $this->timber->translator->trans('Please assign task to someone.')
				),
			),
			'task_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Task title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Task title is invalid.'),
				),
			),
			'task_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'task_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'task_priority' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'task_start_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Start date is invalid.'),
					'vdate' => $this->timber->translator->trans('Start date is invalid.'),
				),
			),
			'task_end_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
					'vdate' => $this->timber->translator->trans('End date is invalid.'),
				),
			),
		));

		if( true === $task_data['error_status'] ){
			$this->response['data'] = $task_data['error_text'];
			return false;
		}

		if( !($task_data['task_end_at']['value'] >= $task_data['task_start_at']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('End date must not be less than start date.');
			return false;
		}

		$new_task_data = array();

		$new_task_data['title'] = $task_data['task_title']['value'];
		$new_task_data['pr_id'] = $task_data['pro_id']['value'];
		$new_task_data['mi_id'] = $task_data['mi_id']['value'];
		$new_task_data['assign_to'] = $task_data['assign_to']['value'];
		$new_task_data['description'] = $task_data['task_description']['value'];
		$new_task_data['priority'] = $task_data['task_priority']['value'];
		$new_task_data['status'] = $task_data['task_status']['value'];
		$new_task_data['owner_id'] = $this->timber->security->getId();
		$new_task_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_task_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_task_data['start_at'] = $task_data['task_start_at']['value'];
		$new_task_data['end_at'] = $task_data['task_end_at']['value'];

		$task_id = $this->timber->task_model->addTask($new_task_data);

 		$this->timber->notify->setMailerCron(array(
 			'method_name' => 'projectTaskEmailNotifier',
 			'project_id' => $new_task_data['pr_id'],
 			'task_id' => $task_id,
 		));

		if( $task_id ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Task created successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $new_task_data['pr_id'] . '?tab=tasks&sub_tab=edit&task_id=' . $task_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Task
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editTask()
	{
		$task_data = $this->timber->validator->clear(array(
			'ta_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'mi_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please select milestone.'),
					'vint' => $this->timber->translator->trans('Please select milestone.')
				),
			),
			'assign_to' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Please assign task to someone.'),
					'vint' => $this->timber->translator->trans('Please assign task to someone.')
				),
			),
			'task_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Task title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Task title is invalid.'),
				),
			),
			'task_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'task_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'task_priority' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'task_start_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Start date is invalid.'),
					'vdate' => $this->timber->translator->trans('Start date is invalid.'),
				),
			),
			'task_end_at' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vdate',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('End date is invalid.'),
					'vdate' => $this->timber->translator->trans('End date is invalid.'),
				),
			),
		));

		if( true === $task_data['error_status'] ){
			$this->response['data'] = $task_data['error_text'];
			return false;
		}

		if( !($task_data['task_end_at']['value'] >= $task_data['task_start_at']['value']) ){
			$this->response['data'] = $this->timber->translator->trans('End date must not be less than start date.');
			return false;
		}

		$new_task_data = array();

		$new_task_data['title'] = $task_data['task_title']['value'];
		$new_task_data['ta_id'] = $task_data['ta_id']['value'];
		$new_task_data['mi_id'] = $task_data['mi_id']['value'];
		$new_task_data['assign_to'] = $task_data['assign_to']['value'];
		$new_task_data['description'] = $task_data['task_description']['value'];
		$new_task_data['priority'] = $task_data['task_priority']['value'];
		$new_task_data['status'] = $task_data['task_status']['value'];
		$new_task_data['updated_at'] = $this->timber->time->getCurrentDate(true);
		$new_task_data['start_at'] = $task_data['task_start_at']['value'];
		$new_task_data['end_at'] = $task_data['task_end_at']['value'];

		$action_status  = (boolean) $this->timber->task_model->updateTaskById($new_task_data);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Task updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Task
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteTask()
	{
		$task_id = ( (isset($_POST['task_id'])) && ((boolean) filter_var($_POST['task_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['task_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $task_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->task_model->deleteTaskById($task_id);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Task deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
	/**
	 * Mark Task
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markTask()
	{
		$task_id = ( (isset($_POST['task_id'])) && ((boolean) filter_var($_POST['task_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['task_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$task_status = ( (isset($_POST['task_status'])) && (in_array($_POST['task_status'], array(1,2,3,4))) ) ? $_POST['task_status'] : 4;

		if( $task_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status = false;
		if( $this->timber->access->getRule() == 'staff' ){
			$action_status  = (boolean) $this->timber->task_model->updateTaskByMultiple(array(
					'ta_id' => $task_id,
					'status' => $task_status,
					'assign_to' => $this->timber->security->getId(),
			));
		}

		if( $this->timber->access->getRule() == 'admin' ){
			$action_status  = (boolean) $this->timber->task_model->updateTaskById(array(
					'ta_id' => $task_id,
					'status' => $task_status,
			));
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Task updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.' . $h);
			return false;
		}
	}

	/**
	 * Add Ticket
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addTicket()
	{

		$ticket_data = $this->timber->validator->clear(array(
			'pro_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'parent_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '0',
				'errors' => array(),
			),
			'ticket_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'ticket_depth' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2',
				'default' => '1',
				'errors' => array(),
			),
			'ticket_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'ticket_subject' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '__REP__',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Ticket subject is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Ticket subject is invalid.'),
				),
			),
			'ticket_content' => array(
				'req' => 'post',
				'sanit' => '',
				'valid' => 'vnotempty&vstrlenbetween:3,3000',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Ticket content is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Ticket content is invalid.'),
				),
			),
			'tic_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
		));

		if( true === $ticket_data['error_status'] ){
			$this->response['data'] = $ticket_data['error_text'];
			return false;
		}

		$new_ticket_data = array();
		$new_ticket_data['pr_id'] = $ticket_data['pro_id']['value'];
		$new_ticket_data['parent_id'] = $ticket_data['parent_id']['value'];
		$new_ticket_data['reference'] = $this->timber->ticket_model->newReference("TIC");
		$new_ticket_data['owner_id'] = $this->timber->security->getId();
		$new_ticket_data['status'] = $ticket_data['ticket_status']['value'];
		$new_ticket_data['type'] = $ticket_data['ticket_type']['value'];
		$new_ticket_data['depth'] = $ticket_data['ticket_depth']['value'];
		$new_ticket_data['subject'] = ($ticket_data['ticket_subject']['value'] == '__REP__') ? '' : $ticket_data['ticket_subject']['value'];
		$new_ticket_data['content'] = $ticket_data['ticket_content']['value'];
		$new_ticket_data['attach'] = 'off';
		$new_ticket_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_ticket_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$tic_attachments = $ticket_data['tic_attachments']['value'];

		$files_ids = array();
		if( (is_array($tic_attachments)) && (count($tic_attachments) > 0) ){
			foreach( $tic_attachments as $tic_attachment ) {
				$tic_attachment = explode('--||--', $tic_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $tic_attachment[1],
					'hash' => $tic_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Ticket Attachments",
					'storage' => 2,
					'type' => pathinfo($tic_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
			$new_ticket_data['attach'] = 'on';
		}

		$ticket_id = $this->timber->ticket_model->addTicket($new_ticket_data);

		# Add Metas
		$meta_status = true;

		$meta_status &= (boolean) $this->timber->meta_model->addMeta(array(
			'rec_id' => $ticket_id,
			'rec_type' => 10,
			'me_key' => 'ticket_attachments_data',
			'me_value' => serialize($files_ids),
		));

 		$this->timber->notify->setMailerCron(array(
 			'method_name' => 'projectTicketEmailNotifier',
 			'project_id' => $new_ticket_data['pr_id'],
 			'ticket_id' => $ticket_id,
 		));


		if( $ticket_id && $meta_status ){
			$this->response['status'] = 'success';
			if( $new_ticket_data['parent_id'] == '0' ){
				$this->response['data'] = $this->timber->translator->trans('Ticket created successfully.');
				$this->response['next_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $new_ticket_data['pr_id'] . '?tab=tickets&sub_tab=edit&tick_id=' . $ticket_id;
			}else{
				$this->response['data'] = $this->timber->translator->trans('Reply created successfully.');
				$this->response['next_link'] = $this->timber->config('request_url') . '/admin/projects/view/' . $new_ticket_data['pr_id'] . '?tab=tickets&sub_tab=view&tick_id=' . $new_ticket_data['parent_id'];
			}

			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Ticket
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editTicket()
	{
		$ticket_data = $this->timber->validator->clear(array(
			'ti_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Invalid Request.'),
					'vint' => $this->timber->translator->trans('Invalid Request.')
				),
			),
			'ticket_status' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3',
				'default' => '1',
				'errors' => array(),
			),
			'ticket_type' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vinarray:1,2,3,4,5',
				'default' => '1',
				'errors' => array(),
			),
			'ticket_subject' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '__REP__',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Ticket subject is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Ticket subject is invalid.'),
				),
			),
			'ticket_content' => array(
				'req' => 'post',
				'sanit' => '',
				'valid' => 'vnotempty&vstrlenbetween:3,3000',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Ticket content is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Ticket content is invalid.'),
				),
			),
			'tic_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfiles',
				'valid' => 'vnotempty&vfiles:' . implode(',' , unserialize($this->timber->config('_allowed_upload_extensions'))),
				'default' => '',
				'errors' => array(),
			),
			'tic_old_attachments' => array(
				'req' => 'post',
				'sanit' => 'sfilesids',
				'valid' => 'vfilesids',
				'default' => array(),
				'errors' => array(),
			),
		));

		if( true === $ticket_data['error_status'] ){
			$this->response['data'] = $ticket_data['error_text'];
			return false;
		}

		$ticket = $this->timber->ticket_model->getTicketById($ticket_data['ti_id']['value']);

		if( (false === $ticket) || !(is_object($ticket)) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$ticket = $ticket->as_array();

		if( !($this->timber->access->getRule() == 'admin') && !( ($this->timber->access->getRule() == 'staff') && ($this->timber->security->getId() == $ticket['owner_id']) ) && !( ($this->timber->access->getRule() == 'client') && ($this->timber->security->getId() == $ticket['owner_id']) ) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$new_ticket_data = array();
		$new_ticket_data['ti_id'] = $ticket_data['ti_id']['value'];
		$new_ticket_data['status'] = $ticket_data['ticket_status']['value'];
		$new_ticket_data['type'] = $ticket_data['ticket_type']['value'];
		$new_ticket_data['subject'] = ($ticket_data['ticket_subject']['value'] == '__REP__') ? '' : $ticket_data['ticket_subject']['value'];
		$new_ticket_data['content'] = $ticket_data['ticket_content']['value'];
		$new_ticket_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		# Add Attachments
		$tic_attachments = $ticket_data['tic_attachments']['value'];

		$files_ids = array();
		if( $ticket['attach'] == 'on' ){
			$attachments_ids = $this->timber->meta_model->getMetaByMultiple(array(
				'rec_id' => $ticket_data['ti_id']['value'],
				'rec_type' => 10,
				'me_key' => 'ticket_attachments_data'
			));
			if( (false !== $attachments_ids) && (is_object($attachments_ids)) ){
				$attachments_ids = $attachments_ids->as_array();
				$files_ids = unserialize($attachments_ids['me_value']);

				foreach ($files_ids as $key => $value) {
					if( !in_array( $value, $ticket_data['tic_old_attachments']['value'] ) ){
						unset($files_ids[$key]);
					}
				}
			}
		}

		if( (is_array($tic_attachments)) && (count($tic_attachments) > 0) ){
			foreach( $tic_attachments as $tic_attachment ) {
				$tic_attachment = explode('--||--', $tic_attachment);
				$files_ids[] = $this->timber->file_model->addFile(array(
					'title' => $tic_attachment[1],
					'hash' => $tic_attachment[0],
					'owner_id' => $this->timber->security->getId(),
					'description' => "Ticket Attachments",
					'storage' => 2,
					'type' => pathinfo($tic_attachment[1], PATHINFO_EXTENSION),
					'uploaded_at' => $this->timber->time->getCurrentDate(true),
				));
			}
		}

		if( count($files_ids) > 0 ){
			$new_ticket_data['attach'] = 'on';
		}

		$action_status = (boolean) $this->timber->ticket_model->updateTicketById($new_ticket_data);


		$action_status &= (boolean) $this->timber->meta_model->updateMetaByMultiple(array(
			'rec_id' => $ticket_data['ti_id']['value'],
			'rec_type' => 10,
			'me_key' => 'ticket_attachments_data',
			'me_value' => serialize($files_ids),
		));

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Ticket updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Ticket
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteTicket()
	{
		$ticket_id = ( (isset($_POST['ticket_id'])) && ((boolean) filter_var($_POST['ticket_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $ticket_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status  = (boolean) $this->timber->ticket_model->deleteTicketById($ticket_id);
		$action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $ticket_id, 10);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Ticket deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Mark Ticket
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function markTicket()
	{
		$ticket_id = ( (isset($_POST['ticket_id'])) && ((boolean) filter_var($_POST['ticket_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['ticket_id'], FILTER_SANITIZE_NUMBER_INT) : false;
		$ticket_status = ( (isset($_POST['ticket_status'])) && (in_array($_POST['ticket_status'], array(1,2,3))) ) ? $_POST['ticket_status'] : 3;

		if( $ticket_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status = false;

		if( ($this->timber->access->getRule() == 'admin') || ($this->timber->access->getRule() == 'staff') ){
			$action_status  = (boolean) $this->timber->ticket_model->updateTicketById(array(
				'ti_id' => $ticket_id,
				'status' => $ticket_status,
			));
		}

		if( $this->timber->access->getRule() == 'client' ){
			$action_status  = (boolean) $this->timber->ticket_model->updateTicketByMultiple(array(
				'ti_id' => $ticket_id,
				'status' => $ticket_status,
				'owner_id' => $this->timber->security->getId(),
			));
		}

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Ticket updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}
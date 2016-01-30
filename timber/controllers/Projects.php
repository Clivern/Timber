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
 * Projects Controller
 *
 * @since 1.0
 */
class Projects {

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
	 * @parm string $page
	 * @return boolean
	 */
	public function renderFilters($page = 'list')
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/projects');

		if( !($this->timber->access->checkPermission($page . '.projects')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Render Projects Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $project_id
	 */
	public function render( $page = 'list', $project_id = '' )
	{
		if( !in_array($page, array('list', 'add', 'edit', 'view')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'projects-' . $page, $this->getData($page, $project_id) );
	}

	/**
	 *  Run common tasks before requests
	 *
	 * @since 1.0
	 * @access public
	 */
	public function requestFilters()
	{
		$this->services->Common->ajaxCheck();
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
	}

	/**
	 * Process requests and respond
	 *
	 * @since 1.0
	 * @access public
	 * @param string $form
	 * @return string
	 */
	public function requests($form = '')
	{
		# $this->timber->bench->start();

		$this->services->ProjectsRequests->setRequest($form);
		$this->services->ProjectsRequests->processRequest(
			array(
				'add' => array('admin'),
				'edit' => array('admin'),
				'delete' => array('admin'),
				'mark' => array('admin'),

				'sync' => array('client', 'staff', 'admin'),

				'edit_milestone' => array('admin'),
				'add_milestone' => array('admin'),
				'delete_milestone' => array('admin'),

				'edit_task' => array('admin'),
				'add_task' => array('admin'),
				'delete_task' => array('admin'),
				'mark_task' => array('staff', 'admin'),

				'add_ticket' => array('client', 'staff', 'admin'),
				'edit_ticket' => array('client', 'staff', 'admin'),
				'delete_ticket' => array('admin'),
				'mark_ticket' => array('client', 'staff', 'admin'),
			),
			array(
				'add' => array('real_admin'),
				'edit' => array('real_admin'),
				'delete' => array('real_admin'),
				'mark' => array('real_admin'),

				'sync' => array('real_client', 'real_staff', 'real_admin'),

				'edit_milestone' => array('real_admin'),
				'add_milestone' => array('real_admin'),
				'delete_milestone' => array('real_admin'),

				'edit_task' => array('real_admin'),
				'add_task' => array('real_admin'),
				'delete_task' => array('real_admin'),
				'mark_task' => array('real_staff', 'real_admin'),

				'add_ticket' => array('real_client', 'real_staff', 'real_admin'),
				'edit_ticket' => array('real_client', 'real_staff', 'real_admin'),
				'delete_ticket' => array('real_admin'),
				'mark_ticket' => array('real_client', 'real_staff', 'real_admin'),
			),
			$form,
			$this->services->ProjectsRequests,
			array(
				'add' => 'addProject',
				'edit' => 'editProject',
				'delete' => 'deleteProject',
				'mark' => 'markProject',

				'sync' => 'syncFiles',

				'edit_milestone' => 'editMilestone',
				'add_milestone' => 'addMilestone',
				'delete_milestone' => 'deleteMilestone',

				'edit_task' => 'editTask',
				'add_task' => 'addTask',
				'delete_task' => 'deleteTask',
				'mark_task' => 'markTask',

				'add_ticket' => 'addTicket',
				'edit_ticket' => 'editTicket',
				'delete_ticket' => 'deleteTicket',
				'mark_ticket' => 'markTicket',
			)
		);
		$this->services->ProjectsRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $project_id
	 * @return array
	 */
	private function getData($page = 'list', $project_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			if( $this->timber->access->getRule() == 'admin' ){

				$data = array_merge($data,
					$this->services->Common->subPageName( $this->timber->translator->trans('Projects') . " | " ),
                    $this->services->ProjectsData->currentUserData(),
                    $this->services->ProjectsData->listData(),
					$this->services->Common->runtimeScripts( 'projectsList' ),
					$this->services->Common->injectScripts(array(
						'projects_records_start' => 6,
						'projects_total_records' => $this->timber->project_model->countProjects(),
						'projects_render_socket' => $this->timber->config('request_url') . '/request/backend/ajax/realtime/projects_list'
					))
				);

			}else{

                $data = array_merge($data,
					$this->services->Common->subPageName( $this->timber->translator->trans('Projects') . " | " ),
                    $this->services->ProjectsData->currentUserData(),
                    $this->services->ProjectsData->listData(),
					$this->services->Common->runtimeScripts( 'projectsStrictList' )
				);

			}

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Add New Project') . " | " ),
                $this->services->ProjectsData->currentUserData(),
                $this->services->ProjectsData->addData(),
                $this->services->ProjectsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'projectsAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Edit Project') . " | " ),
                $this->services->ProjectsData->currentUserData(),
                $this->services->ProjectsData->editData($project_id),
                $this->services->ProjectsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'projectsEdit' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Project') . " | " ),
                $this->services->ProjectsData->currentUserData(),
                $this->services->ProjectsData->viewData($project_id),
                $this->services->ProjectsData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'projectsView' ),
				$this->services->Common->injectScripts(array(
					'project_sub_tab' => ( (isset($_GET['tab'])) && (in_array($_GET['tab'], array('stats', 'files', 'tasks', 'milestones', 'tickets'))) ) ? $_GET['tab'] : 'stats',
				))
			);

		}

		return $data;
	}
}
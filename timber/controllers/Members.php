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

namespace Timber\Controllers;

/**
 * Members Controller
 *
 * @since 1.0
 */
class Members {

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
	 * @param string $page
	 * @return boolean
	 */
	public function renderFilters($page = 'list')
	{
		$this->timber->filter->issueDetect();
		$this->timber->filter->configLibs();
		$this->timber->security->cookieCheck();
		$this->services->Common->renderFilter(array('staff', 'admin'), '/admin/members');

		if( ('add' == $page) && !($this->timber->access->checkPermission('add.clients')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}
	}

	/**
	 * Render Members Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param string $member_id
	 */
	public function render($page = 'list', $member_id = '')
	{
		if( !in_array($page, array('list', 'add', 'edit', 'view')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'members-' . $page, $this->getData($page, $member_id) );
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

		$this->services->MembersRequests->setRequest($form);
		$this->services->MembersRequests->processRequest(
			array(
				'add_member' => array('staff', 'admin'),
				'update_member_profile' => array('staff', 'admin'),
				'delete_member' => array('staff', 'admin'),
			),
			array(
				'add_member' => array('real_staff', 'real_admin'),
				'update_member_profile' => array('real_staff', 'real_admin'),
				'delete_member' => array('real_staff', 'real_admin'),
			),
			$form,
			$this->services->MembersRequests,
			array(
				'add_member' => 'addMember',
				'update_member_profile' => 'updateMemberProfile',
				'delete_member' => 'deleteMember',
			)
		);
		$this->services->MembersRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param string $member_id
	 * @return array
	 */
	private function getData($page = 'list', $member_id = '')
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
                $this->services->Common->subPageName( $this->timber->translator->trans('Members') . " | " ),
				$this->services->MembersData->currentUserData(),
				$this->services->MembersData->listData(),
				$this->services->Common->runtimeScripts( 'membersList' ),
				$this->services->Common->injectScripts(array(
					'members_records_start' => 20,
					'members_total_records' => $this->timber->user_model->countUsers(),
					'members_render_socket' => $this->timber->config('request_url') . '/request/backend/ajax/realtime/members_list'
				))
			);


		}elseif( 'add' == $page ){

			$data = array_merge($data,
                $this->services->Common->subPageName( $this->timber->translator->trans('Add Member') . " | " ),
				$this->services->MembersData->currentUserData(),
				$this->services->MembersData->addData(),
				$this->services->MembersData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'membersAdd' )
			);

		}elseif( 'edit' == $page ){

			$data = array_merge($data,
                $this->services->Common->subPageName( $this->timber->translator->trans('Edit Member') . " | " ),
				$this->services->MembersData->currentUserData(),
				$this->services->MembersData->editData($member_id),
				$this->services->MembersData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'membersEdit' )
			);

		}elseif( 'view' == $page ){


			$data = array_merge($data,
                $this->services->Common->subPageName( $this->timber->translator->trans('View Member') . " | " ),
				$this->services->MembersData->currentUserData(),
				$this->services->MembersData->viewData($member_id),
				$this->services->Common->runtimeScripts( 'membersView' )
			);

		}

		return $data;
	}
}
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
 * Messages Controller
 *
 * @since 1.0
 */
class Messages {

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
		$this->services->Common->renderFilter(array('client', 'staff', 'admin'), '/admin/messages');
	}

	/**
	 * Render Messages Page
	 *
	 * @since 1.0
	 * @access public
	 * @param string $page
	 * @param integer $message_id
	 */
	public function render($page = 'list', $message_id = '')
	{
		if( !in_array($page, array('list', 'add', 'view')) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		return $this->timber->render( 'messages-' . $page, $this->getData($page, $message_id) );
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

		$this->services->MessagesRequests->setRequest($form);
		$this->services->MessagesRequests->processRequest(
			array(
				'add_message' => array('client', 'staff', 'admin'),
				'reply_to_message' => array('client', 'staff', 'admin'),
				'mark_message' => array('client', 'staff', 'admin'),
			),
			array(
				'add_message' => array('real_client', 'real_staff', 'real_admin'),
				'reply_to_message' => array('real_client', 'real_staff', 'real_admin'),
				'mark_message' => array('real_client', 'real_staff', 'real_admin'),
			),
			$form,
			$this->services->MessagesRequests,
			array(
				'add_message' => 'addMessage',
				'reply_to_message' => 'addReply',
				'mark_message' => 'markMessage',
			)
		);
		$this->services->MessagesRequests->getResponse();

		# $this->timber->bench->end();
		# $this->timber->bench->log();
	}

	/**
	 * Bind and get data
	 *
	 * @since 1.0
	 * @access private
	 * @param string $page
	 * @param integer $message_id
	 * @return array
	 */
	private function getData($page, $message_id)
	{
		$data = array();

		if( 'list' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Messages') . " | " ),
                $this->services->MessagesData->currentUserData(),
                $this->services->MessagesData->messagesMeta(),
                $this->services->MessagesData->listData(),
				$this->services->Common->runtimeScripts( 'messagesList' )
			);

		}elseif( 'add' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('Compose Message') . " | " ),
                $this->services->MessagesData->currentUserData(),
                $this->services->MessagesData->messagesMeta(),
                $this->services->MessagesData->addData(),
                $this->services->MessagesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'messagesAdd' )
			);

		}elseif( 'view' == $page ){

			$data = array_merge($data,
				$this->services->Common->subPageName( $this->timber->translator->trans('View Message') . " | " ),
                $this->services->MessagesData->currentUserData(),
                $this->services->MessagesData->messagesMeta(),
                $this->services->MessagesData->viewData($message_id),
                $this->services->MessagesData->uploaderInfo(),
				$this->services->Common->runtimeScripts( 'messagesView' )
			);

		}

		return $data;
	}
}
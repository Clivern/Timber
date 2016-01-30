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
 * Items Data Services
 *
 * @since 1.0
 */
class ItemsData extends \Timber\Services\Base {

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
	 * Get Items Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function listData()
	{
		$data = array();

		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/items/list';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$items = $this->timber->item_model->getItems( false, false, 'desc', 'created_at' );

		$i = 1;
		$data['items'] = array();
		foreach ($items as $key => $item) {

   			$data['items'][$i]['it_id'] = $item['it_id'];
   			$data['items'][$i]['title'] = $item['title'];
   			$data['items'][$i]['owner_id'] = $item['owner_id'];
   			$data['items'][$i]['description'] = $item['description'];
   			$data['items'][$i]['cost'] = $item['cost'];
   			$data['items'][$i]['created_at'] = $item['created_at'];
   			$data['items'][$i]['updated_at'] = $item['updated_at'];
			$data['items'][$i]['edit_link'] = $this->timber->config('request_url') . '/admin/items/edit/' . $item['it_id'];
			$data['items'][$i]['trash_link'] = $this->timber->config('request_url') . '/request/backend/ajax/items/delete';

			$i += 1;
		}

		return $data;
	}

	/**
	 * Get Item Data
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function addData()
	{
		$data = array();

		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/items/add';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		return $data;
	}

	/**
	 * Get Item Data
	 *
	 * @since 1.0
	 * @access public
	 * @param integer $item_id
	 * @return array
	 */
	public function editData($item_id)
	{
		$data = array();

		$data['form_action'] = $this->timber->config('request_url') . '/request/backend/ajax/items/edit';
		$data['site_currency'] = $this->timber->config('_site_currency_symbol');

		$item_id = ((boolean) filter_var($item_id, FILTER_VALIDATE_INT)) ? filter_var($item_id, FILTER_SANITIZE_NUMBER_INT) : false;

		if( $item_id === false ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$item = $this->timber->item_model->getItemById($item_id);
		if( (false === $item) || !(is_object($item)) ){
			$this->timber->redirect( $this->timber->config('request_url') . '/404' );
		}

		$item = $item->as_array();
   		$data['it_id'] = $item['it_id'];
   		$data['title'] = $item['title'];

        if( !empty($data['title']) ){
            $data['site_sub_page'] = $data['title']  . " | ";
        }

   		$data['owner_id'] = $item['owner_id'];
   		$data['description'] = $item['description'];
   		$data['cost'] = $item['cost'];
   		$data['created_at'] = $item['created_at'];
   		$data['updated_at'] = $item['updated_at'];

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
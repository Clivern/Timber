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
 * Items Requests Services
 *
 * @since 1.0
 */
class ItemsRequests extends \Timber\Services\Base {

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
	 * Add Item Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function addItem()
	{

		$item_data = $this->timber->validator->clear(array(
			'item_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Item title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Item title is invalid.'),
				),
			),
			'item_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'item_cost' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:0',
				'default' => '',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Item cost is invalid.'),
				),
			)
		));

		if( true === $item_data['error_status'] ){
			$this->response['data'] = $item_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('add.items')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$new_item_data = array();
		$new_item_data['title'] = $item_data['item_title']['value'];
		$new_item_data['description'] = $item_data['item_description']['value'];
		$new_item_data['cost'] = $item_data['item_cost']['value'];
		$new_item_data['owner_id'] = $this->timber->security->getId();
		$new_item_data['created_at'] = $this->timber->time->getCurrentDate(true);
		$new_item_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		$item_id = $this->timber->item_model->addItem($new_item_data);

		if( $item_id ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Item added successfully.');
			$this->response['next_link'] = $this->timber->config('request_url') . '/admin/items/edit/' . $item_id;
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Edit Item Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function editItem()
	{
		$item_data = $this->timber->validator->clear(array(
			'item_id' => array(
				'req' => 'post',
				'sanit' => 'snumberint',
				'valid' => 'vnotempty&vint',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Something goes wrong! Refresh page and try again.'),
					'vint' => $this->timber->translator->trans('Something goes wrong! Refresh page and try again.'),
				),
			),
			'item_title' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:2,100',
				'default' => '',
				'errors' => array(
					'vnotempty' => $this->timber->translator->trans('Item title is invalid.'),
					'vstrlenbetween' => $this->timber->translator->trans('Item title is invalid.'),
				),
			),
			'item_description' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vnotempty&vstrlenbetween:0,200',
				'default' => '',
				'errors' => array(),
			),
			'item_cost' => array(
				'req' => 'post',
				'sanit' => 'sstring',
				'valid' => 'vfloat:0',
				'default' => '',
				'errors' => array(
					'vfloat' => $this->timber->translator->trans('Item cost is invalid.'),
				),
			)
		));

		if( true === $item_data['error_status'] ){
			$this->response['data'] = $item_data['error_text'];
			return false;
		}

		if( !($this->timber->access->checkPermission('edit.items')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$new_item_data = array();
		$new_item_data['it_id'] = $item_data['item_id']['value'];
		$new_item_data['title'] = $item_data['item_title']['value'];
		$new_item_data['description'] = $item_data['item_description']['value'];
		$new_item_data['cost'] = $item_data['item_cost']['value'];
		$new_item_data['updated_at'] = $this->timber->time->getCurrentDate(true);

		$status = (boolean) $this->timber->item_model->updateItemById($new_item_data);

		if( $status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Item updated successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}

	/**
	 * Delete Item Request
	 *
	 * @since 1.0
	 * @access public
	 * @return boolean
	 */
	public function deleteItem()
	{
		$item_id = ( (isset($_POST['item_id'])) && ((boolean) filter_var($_POST['item_id'], FILTER_VALIDATE_INT)) ) ? filter_var($_POST['item_id'], FILTER_SANITIZE_NUMBER_INT) : false;

		if( $item_id === false ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		if( !($this->timber->access->checkPermission('delete.items')) ){
			$this->response['data'] = $this->timber->translator->trans('Invalid Request.');
			return false;
		}

		$action_status = (boolean) $this->timber->item_model->deleteItemById($item_id);
		$action_status &= (boolean) $this->timber->meta_model->dumpMetas(false, $item_id, 1);

		if( $action_status ){
			$this->response['status'] = 'success';
			$this->response['data'] = $this->timber->translator->trans('Item deleted successfully.');
			return true;
		}else{
			$this->response['data'] = $this->timber->translator->trans('Something goes wrong! Try again later.');
			return false;
		}
	}
}
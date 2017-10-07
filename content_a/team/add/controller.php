<?php
namespace content_a\team\add;

class controller extends \content_a\main\controller
{
	/**
	 * route
	 */
	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		// add team
		$this->get(false, 'add')->ALL();
		$this->post('add')->ALL();

		unset($_SESSION['first_go_to_setup']);
	}
}
?>
<?php
namespace content_enter\username;


class controller extends \content_enter\main\controller
{
	public function _route()
	{
		// if the user is login redirect to base
		parent::if_login_not_route();
		$this->post('username')->ALL('username');
	}
}
?>
<?php
namespace content_enter\verify;


class controller extends \content_enter\main\controller
{
	public function _route()
	{
		// if the user is login redirect to base
		parent::if_login_not_route();

		// check remeber me is set
		// if remeber me is set: login!
		parent::check_remeber_me();

		$url = \lib\router::get_url();

		if($url === 'verify')
		{
			\lib\error::page();
		}
	}
}
?>
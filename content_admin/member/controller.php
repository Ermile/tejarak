<?php
namespace content_admin\member;

class controller extends \content_admin\main\controller
{
	/**
	 * rout
	 */
	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		$this->get(false, 'add')->ALL("/^team\/([a-zA-Z0-9]+)\/member$/");
		$this->post('add')->ALL("/^team\/([a-zA-Z0-9]+)\/member$/");

		$this->display_name = 'content_admin\member\add.html';


		// $this->route();
		$this->get(false, 'list')->ALL("/^team\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)$/");
		if(preg_match("/^team\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)$/", $url))
		{
			$this->display_name = 'content_admin\member\dashboard.html';
		}

	}
}
?>
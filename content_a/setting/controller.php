<?php
namespace content_a\setting;

class controller extends \content_a\main\controller
{
	/**
	 * rout
	 */
	function _route()
	{

		parent::_route();

		$new_url = $this->url('baseFull'). '/'. \lib\router::get_url(0). '/setting/general';

		$this->redirector($new_url)->redirect();

	}
}
?>
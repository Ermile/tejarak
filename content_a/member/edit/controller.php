<?php
namespace content_a\member\edit;

class controller extends \content_a\main\controller
{
	/**
	 * rout
	 */
	public function _route()
	{
		parent::_route();


		$new_url = $this->url('baseFull'). '/'. \lib\router::get_url(0). '/member/general/'. \lib\router::get_url(3);

		$this->redirector($new_url)->redirect();


		$this->get(false, 'edit')->ALL("/.*/");
		$this->post('edit')->ALL("/.*/");
	}
}
?>
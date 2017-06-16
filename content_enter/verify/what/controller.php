<?php
namespace content_enter\verify\what;


class controller extends \content_enter\main\controller
{
	public function _route()
	{

		// if this step is locked go to error page and return
		if(self::lock('verify/what'))
		{
			self::error_page('verify/what');
			return;
		}

		$this->get()->ALL('verify/what');
	}
}
?>
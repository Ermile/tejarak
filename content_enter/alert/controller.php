<?php
namespace content_enter\alert;


class controller extends \content_enter\main\controller
{
	public function _route()
	{
		// if this step is locked go to error page and return
		if(self::lock('alert'))
		{
			self::error_page('alert');
			return;
		}
	}
}
?>
<?php
namespace content_a\billing\detail;

class view extends \content_a\main\view
{
	public function config()
	{
		parent::config();
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_detail($_args)
	{
		if(!$this->login())
		{
			return;
		}
		// var_dump($_args);exit();
		$detail = $_args->api_callback;
		$this->data->detail = $detail;

	}

}
?>
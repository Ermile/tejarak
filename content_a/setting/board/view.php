<?php
namespace content_a\setting\board;

class view extends \content_a\main\view
{

	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_board($_args)
	{
		$request_data = $this->model()->load_last_request();
		$this->data->sended_data = $request_data;
	}
}
?>
<?php
namespace content_enter\sessions;

class view extends \content_enter\main\view
{

	/**
	 * view enter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_sessions($_args)
	{
		$this->data->sessions_list = $this->model()->sessions_list();
	}
}
?>
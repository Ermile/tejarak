<?php
namespace content\hours;

class view extends \mvc\view
{
	function config()
	{

	}


	/**
	 * show hous page of team
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_show($_args)
	{
		// check read url
		if(
			isset($_args->match->url[0]) &&
			is_string($_args->match->url[0]) &&
			preg_match("/^([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)$/", $_args->match->url[0], $split)
		  )
		{
			$url = $_args->match->url[0];
		}
		else
		{
			\lib\error::bad();
			return;
		}
		if(isset($split[1]) && isset($split[2]))
		{
			$request            = [];
			$this->data->team   = $request['team']         = $split[1];
			$this->data->branch = $request['branch']       = $split[2];
			$this->data->list_member = $this->model()->list_member($request);
		}
	}
}
?>
<?php
namespace content_a\houredit;
use \lib\debug;
use \lib\utility;

class model extends \content_a\main\model
{

	/**
	 * delete request
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function delete_request($_args)
	{
		$team_id        = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$hourrequest_id = isset($_args->match->url[0][2]) ? $_args->match->url[0][2] : null;
		if(!$team_id || !$hourrequest_id)
		{
			return false;
		}

		$this->user_id = $this->login('id');
		utility::set_request_array(['id' => $hourrequest_id]);
		$this->hourrequest_delete(['method' => 'delete']);
		$this->redirector($this->url('baseFull'). "/$team_id/houredit/list")->redirect();
	}


	/**
	 * show one hourrequest detail
	 *
	 * @param      <type>  $_request  The request
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function request_detail($_request)
	{
		$this->user_id = $this->login('id');
		utility::set_request_array($_request);
		return $this->get_houredit_detail();
	}


	/**
	 * show request list
	 *
	 * @param      <type>  $_request  The request
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function request_list($_request)
	{
		$this->user_id = $this->login('id');
		utility::set_request_array($_request);
		return $this->get_houredit_list();
	}


	/**
	 * Gets my time.
	 * get the time record
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  My time.
	 */
	public function getMyTime($_args)
	{
		utility::set_request_array($_args);
		$this->user_id = $this->login('id');
		$result = $this->get_houredit();

		if(!$result)
		{
			debug::$status = 1;
			$result = $this->get_hours();
		}
		return $result;
	}


	/**
	 * save a request for edit time
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_save($_args)
	{
		$request               = [];

		if(isset($_args->match->url[0][3]) && $_args->match->url[0][3])
		{
			$request['id'] = $_args->match->url[0][3];
		}

		$request['start_date'] = utility::post('start_date');
		$request['start_time'] = utility::post('start_time');
		$request['end_date']   = utility::post('end_date');
		$request['end_time']   = utility::post('end_time');
		$request['desc']       = utility::post('desc');
		$request['team']       = \lib\router::get_url(0);

		utility::set_request_array($request);
		$this->user_id = $this->login('id');
		$this->add_houredit();
	}
}
?>
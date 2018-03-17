<?php
namespace content_a\request\hour;


class model extends \content_a\main\model
{



	/**
	 * Gets the post.
	 *
	 * @return     array  The post.
	 */
	public function getPost()
	{
		$args               = [];
		$args['start_date'] = \lib\utility\human::number(\lib\request::post('start_date'), 'en');
		$args['start_time'] = \lib\utility\human::number(\lib\request::post('start_time'), 'en');
		$args['end_date']   = \lib\utility\human::number(\lib\request::post('end_date'), 'en');
		$args['end_time']   = \lib\utility\human::number(\lib\request::post('end_time'), 'en');
		$args['desc']       = \lib\request::post('desc');

		return $args;
	}


	/**
	 * save a request for edit time
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_hour($_args)
	{
		$request            = $this->getPost();
		$request['team']    = \lib\url::dir(0);
		$request['hour_id'] = \lib\url::dir(3);

		\lib\utility::set_request_array($request);
		$this->user_id = $this->login('id');
		$this->add_houredit();
		if(\lib\notif::$status)
		{
			\lib\redirect::to(\lib\url::here(). '/'. $request['team']. '/request');
		}
	}
}
?>
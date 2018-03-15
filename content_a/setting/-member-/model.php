<?php
namespace content_a\setting\member;


class model extends \content_a\main\model
{

	/**
	 * Gets the post.
	 *
	 * @return     array  The post.
	 */
	public function getPost()
	{
		$args =
		[

			'show_avatar'       => \lib\request::post('showAvatar'),
			'quick_traffic'     => \lib\request::post('quickTraffic'),
			'allow_plus'        => \lib\request::post('allowPlus'),
			'allow_minus'       => \lib\request::post('allowMinus'),
			'remote_user'       => \lib\request::post('remoteUser'),
			'24h'               => \lib\request::post('24h'),


		];

		return $args;
	}


	/**
	 * Posts an add.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_member($_args)
	{
		$code = \lib\url::dir(0);

		$request       = $this->getPost();
		$this->user_id = $this->login('id');
		$request['id'] = $code;

		\lib\utility::set_request_array($request);

		// THE API ADD TEAM FUNCTION BY METHOD PATHC
		$this->add_team(['method' => 'patch']);
	}
}
?>
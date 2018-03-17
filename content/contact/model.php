<?php
namespace content\contact;


class model extends \mvc\model
{

	// log callers
	// user:send:contact
	// user:send:contact:fail
	// user:send:contact:empty:message
	// user:send:contact:empty:mobile
	// user:send:contact:wrong:captha
	// user:send:contact:register:by:mobile

	/**
	 * save contact form
	 */
	public function post_contact()
	{
		// check login
		if($this->login())
		{
			$user_id = $this->login("id");

			// get mobile from user login session
			$mobile = $this->login('mobile');

			if(!$mobile)
			{
				$mobile = \lib\request::post('mobile');
			}

			// get display name from user login session
			$displayname = $this->login("displayname");
			// user not set users display name, we get display name from contact form
			if(!$displayname)
			{
				$displayname = \lib\request::post("name");
			}
			// get email from user login session
			$email = \lib\db\users::get_email($user_id);
			// user not set users email, we get email from contact form
			if(!$email)
			{
				$email = \lib\request::post("email");
			}
		}
		else
		{
			// users not registered
			$user_id     = null;
			$displayname = \lib\request::post("name");
			$email       = \lib\request::post("email");
			$mobile      = \lib\request::post("mobile");
		}
		// get the content
		$content = \lib\request::post("content");

		// save log meta
		$log_meta =
		[
			'meta' =>
			[
				'login'    => $this->login('all'),
				'language' => \lib\language::get_language(),
				'post'     => \lib\request::post(),
			]
		];

		// check content
		if($content == '')
		{
			\lib\db\logs::set('user:send:contact:empty:message', $user_id, $log_meta);
			\lib\notif::error(T_("Please try type something!"), "content");
			return false;
		}
		// ready to insert comments
		$args =
		[
			'author'  => $displayname,
			'email'   => $email,
			'type'    => 'comment',
			'content' => $content,
			'user_id'         => $user_id
		];
		// insert comments
		$result = \lib\db\comments::insert($args);
		if($result)
		{
			// $mail =
			// [
			// 	'from'    => 'info@tejarak.com',
			// 	'to'      => 'info@tejarak.com',
			// 	'subject' => 'contact',
			// 	'body'    => $content,
			// 	'debug'   => false,
			// ];
			// \lib\utility\mail::send($mail);

			\lib\db\logs::set('user:send:contact', $user_id, $log_meta);
			\lib\notif::true(T_("Thank You For contacting us"));
		}
		else
		{
			\lib\db\logs::set('user:send:contact:fail', $user_id, $log_meta);
			\lib\notif::error(T_("We could'nt save the contact"));
		}
	}
}
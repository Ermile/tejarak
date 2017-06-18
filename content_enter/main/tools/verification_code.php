<?php
namespace content_enter\main\tools;
use \lib\utility;
use \lib\debug;

trait verification_code
{

	/**
	 * generate verification code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function create_new_code($_way = null)
	{
		$code =  rand(10000,99999);
		if(self::$dev_mode)
		{
			$code = 11111;
		}
		// set verification code in session
		self::set_enter_session('verification_code', $code);
		$time = date("Y-m-d H:i:s");

		$log_meta =
		[
			'data'     => $code,
			'log_desc' => $_way,
			'time'     => $time,
			'meta'     =>
			[
				'session' => $_SESSION,
			],
		];


		// save this code in logs table and session
		$log_id = \lib\db\logs::set('user:verification:code', self::user_data('id'), $log_meta);

		self::set_enter_session('verification_code', $code);
		self::set_enter_session('verification_code_time', $time);
		self::set_enter_session('verification_code_way', $_way);
		self::set_enter_session('verification_code_id', $log_id);

		return $code;
	}


	/**
	 * check code exist and live
	 */
	public static function generate_verification_code()
	{
		// check last code time and if is not okay make new code
		$last_code_ok = false;
		// get saved session last verification code

		if
		(
			self::get_enter_session('verification_code') &&
			self::get_enter_session('verification_code_id') &&
			self::get_enter_session('verification_code_time')
		)
		{
			if(time() - strtotime(self::get_enter_session('verification_code_time')) < self::$life_time_code)
			{
				// last code is true
				// need less to create new code
				$last_code_ok = true;
			}
		}


		// user code not found
		if(!$last_code_ok)
		{
			if(self::user_data('id'))
			{
				$where =
				[
					'caller'     => 'user:verification:code',
					'user_id'    => self::user_data('id'),
					'log_status' => 'enable',
					'limit'      => 1,
				];
				$log_code = \lib\db\logs::get($where);

				if($log_code)
				{
					if(isset($log_code['log_createdate']) && time() - strtotime($log_code['log_createdate']) < self::$life_time_code)
					{
						// the last code is okay
						// need less to create new code
						$last_code_ok = true;
						// save data in session
						if(isset($log_code['log_data']))
						{
							self::set_enter_session('verification_code', $log_code['log_data']);
						}
						// save log time
						if(isset($log_code['log_createdate']))
						{
							self::set_enter_session('verification_code_time', $log_code['log_createdate']);
						}
						// save log way
						if(isset($log_code['log_desc']))
						{
							self::set_enter_session('verification_code_way', $log_code['log_desc']);
						}
						// save log id
						if(isset($log_code['id']))
						{
							self::set_enter_session('verification_code_id', $log_code['id']);
						}

					}
					else
					{
						// the log is exist and the time of log is die
						// we expire the log
						if(isset($log_code['id']))
						{
							\lib\db\logs::update(['log_status' => 'expire'], $log_code['id']);
						}
					}
				}
			}
		}
		// if last code is not okay
		// make new code
		if(!$last_code_ok)
		{
			self::create_new_code();
		}
	}



	public static function check_code($_module)
	{
		if(!self::check_input_current_mobile())
		{
			debug::error(T_("Dont!"));
			return false;
		}

		if(!utility::post('code'))
		{
			debug::error(T_("Please fill the verification code"), 'code');
			return false;
		}

		if(!is_numeric(utility::post('code')))
		{
			debug::error(T_("What happend? the code is number. you try to send string!?"), 'code');
			return false;
		}

		if(intval(utility::post('code')) === intval(self::get_enter_session('verification_code')))
		{
			/**
			 ***********************************************************
			 * VERIFY FROM
			 * PASS/SIGNUP
			 * PASS/SET
			 * PASS/RECOVERY
			 ***********************************************************
			 */
			if(
				(
					self::get_enter_session('verify_from') === 'signup' ||
					self::get_enter_session('verify_from') === 'set' ||
					self::get_enter_session('verify_from') === 'recovery'
				) &&
				self::get_enter_session('temp_ramz_hash') &&
				is_numeric(self::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\lib\db\users::update(['user_pass' => self::get_enter_session('temp_ramz_hash')], self::user_data('id'));
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * USERNAME
			 * TRY TO REMOVE USER NAME
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'username_remove' && is_numeric(self::user_data('id')))
			{
				// set temp ramz in use pass
				\lib\db\users::update(['user_username' => null], self::user_data('id'));
				// remove usename from sessions
				unset($_SESSION['user']['username']);
				// set the alert message
				self::set_alert(T_("Your username was removed"));
				// open lock of alert page
				self::next_step('alert');
				// go to alert page
				self::go_to('alert');
				return;
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * ENTER/DELETE
			 * DELETE ACCOUNT
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'delete')
			{
				if(self::get_enter_session('why'))
				{
					$update_meta  = [];

					$user_meta = self::user_data('user_meta');
					if(!$user_meta)
					{
						$update_meta['why'] = self::get_enter_session('why');
					}
					elseif(is_string($user_meta) && substr($user_meta, 0, 1) !== '{')
					{
						$update_meta['other'] = $user_meta;
						$update_meta['why'] = self::get_enter_session('why');
					}
					elseif(is_string($user_meta) && substr($user_meta, 0, 1) === '{')
					{
						$json = json_decode($user_meta, true);
						$update_meta = array_merge($json, ['why' => self::get_enter_session('why')]);
					}

				}

				$update_user = [];
				if(!empty($update_meta))
				{
					$update_user['user_meta'] = json_encode($update_meta, JSON_UNESCAPED_UNICODE);
				}
				$update_user['user_status'] = 'removed';

				\lib\db\users::update($update_user, self::user_data('id'));

				\lib\db\sessions::delete_account(self::user_data('id'));

				//put logout
				self::set_logout(self::user_data('id'));

				self::go_to('byebye');
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * USERNAME/SET
			 * USERNAME/CHANGE
			 ***********************************************************
			 */
			if(
				(
					self::get_enter_session('verify_from') === 'username_set' ||
					self::get_enter_session('verify_from') === 'username_change'
				) &&
				self::get_enter_session('temp_username') &&
				is_numeric(self::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\lib\db\users::update(['user_username' => self::get_enter_session('temp_username')], self::user_data('id'));
				// set the alert message
				if(self::get_enter_session('verify_from') === 'username_set')
				{
					self::set_alert(T_("Your username was set"));
				}
				else
				{
					self::set_alert(T_("Your username was change"));
				}

				if(isset($_SESSION['user']) && is_array($_SESSION['user']))
				{
					$_SESSION['user']['username'] = self::get_enter_session('temp_username');
				}

				// open lock of alert page
				self::next_step('alert');
				// go to alert page
				self::go_to('alert');
				return;
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * MOBILI /REQUEST
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'mobile_request' && is_numeric(self::user_data('id')))
			{
				if(!self::mobile_request_next_step())
				{
					return false;
				}

			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * EMAIL/SET
			 * EMAIL/CHANGE
			 ***********************************************************
			 */
			if(
				(
					self::get_enter_session('verify_from') === 'email_set' ||
					self::get_enter_session('verify_from') === 'email_change'
				) &&
				self::get_enter_session('temp_email') &&
				is_numeric(self::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\lib\db\users::update(['user_email' => self::get_enter_session('temp_email')], self::user_data('id'));
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'two_step' &&	is_numeric(self::user_data('id')))
			{
				// no thing yet
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION SET
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'two_step_set' &&	is_numeric(self::user_data('id')))
			{
				// set on two_step of this user
				\lib\db\users::update(['user_two_step' => 1], self::user_data('id'));
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION SET
			 ***********************************************************
			 */
			if(self::get_enter_session('verify_from') === 'two_step_unset' &&	is_numeric(self::user_data('id')))
			{
				// set off two_step of this user
				\lib\db\users::update(['user_two_step' => 0], self::user_data('id'));
			}

			// set login session
			$redirect_url = self::enter_set_login();

			// save redirect url in session to get from okay page
			self::set_enter_session('redirect_url', $redirect_url);
			// set okay as next step
			self::next_step('okay');
			// go to okay page
			self::go_to('okay');

		}
		else
		{
			// wrong code sleep code
			self::sleep_code();

			// plus count invalid code
			self::plus_try_session('invalid_code_call');

			debug::error(T_("Invalid code, try again"), 'code');
			return false;
		}
	}


	/**
	 * Sends a code email.
	 * send verification code whit email address
	 */
	public static function send_code_email()
	{
		$email = self::get_enter_session('temp_email');
		$code  = self::generate_verification_code();
		$mail =
		[
			'from'    => 'info@sarshomar.com',
			'to'      => $email,
			'subject' => 'contact',
			'body'    => "salam". $code,
			'debug'   => true,
		];
		$mail = \lib\utility\mail::send($mail);
		return $mail;
	}



	/**
	 * user fill the mobile/request
	 * this function find next step
	 * signup user
	 * or login only
	 */
	public static function mobile_request_next_step()
	{
		// set temp ramz in use pass
		switch (self::get_enter_session('mobile_request_from'))
		{
			case 'google_email_not_exist':
				if(self::get_enter_session('must_signup'))
				{
					// sign up user
					$user_id = self::signup_email(self::get_enter_session('must_signup'));

					if($user_id)
					{
						self::enter_set_login(self::user_data('id'));

						\lib\db\users::update(['user_mobile' => self::get_enter_session('temp_mobile')], $user_id);
						// the user click on dont will mobile
						// we save this time to user_dont_will_set_mobile to never show this message again
						if(self::get_enter_session('dont_will_set_mobile'))
						{
							\lib\db\users::update(['user_dont_will_set_mobile' => date("Y-m-d H:i:s")], $user_id);
						}
					}
				}
				break;

			case 'google_email_exist':
				if(is_numeric(self::user_data('id')))
				{
					// the user click on dont will mobile
					// we save this time to user_dont_will_set_mobile to never show this message again
					$update_user_google = [];

					if(self::get_enter_session('dont_will_set_mobile'))
					{
						$update_user_google['user_dont_will_set_mobile'] = date("Y-m-d H:i:s");
					}
					if(!empty($update_user_google))
					{
						\lib\db\users::update($update_user_google, self::user_data('id'));
					}

					self::enter_set_login(self::user_data('id'));
				}

				return false;
				break;

			default:
				# code...
				break;
		}
		return true;
	}
}
?>
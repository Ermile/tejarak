<?php
namespace content_enter\verify\call;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\call\tg as bot;


class model extends \content_enter\main\model
{

	/**
	 * send verification by call
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function send_call_code()
	{
		$code = self::get_enter_session('verification_code');
		if(!self::get_enter_session('mobile'))
		{
			return false;
		}

		if(!\lib\option::enter('call'))
		{
			return false;
		}

		$language     = \lib\define::get_language();
		// find template to call by it
		if(\lib\option::enter('call_template', $language))
		{
			$template   = \lib\option::enter('call_template', $language);
		}
		else
		{
			return false;
		}

		$request =
		[
			'mobile'   => self::get_enter_session('mobile'),
			'template' => $template,
			'token'    => $code,
		 	'type'     => 'call',
		];

		// ready to save log
		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'session' => $_SESSION,
			]
		];


		if(self::$dev_mode)
		{
			$kavenegar_send_result = true;
		}
		else
		{
			$kavenegar_send_result = \lib\utility\sms::send($request, 'verify');
		}

		if($kavenegar_send_result === 411 && substr(self::get_enter_session('mobile'), 0, 2) === '98')
		{
			// invalid user mobil
			db\logs::set('kavenegar:service:411:call', self::user_data('id'), $log_meta);
			return false;
		}
		elseif($kavenegar_send_result === 22)
		{
			db\logs::set('kavenegar:service:done:call', self::user_data('id'), $log_meta);
			// the kavenegar service is down!!!
		}
		elseif($kavenegar_send_result)
		{

			$log_meta['meta']['response'] = [];

			if(is_string($kavenegar_send_result))
			{
				$log_meta['meta']['response'] = $kavenegar_send_result;
			}
			elseif(is_array($kavenegar_send_result))
			{
				foreach ($kavenegar_send_result as $key => $value)
				{
					$log_meta['meta']['response'][$key] = str_replace("\n", ' ', $value);
				}
			}

			db\logs::set('enter:send:call:result', self::user_data('id'), $log_meta);

			return true;
		}
		else
		{
			db\logs::set('enter:send:cannot:send:call', self::user_data('id'), $log_meta);
		}

		// why?!
		return false;
	}


	/**
	* cehck sended code
	*
	*/
	public function post_verify()
	{
		self::check_code('call');
	}

}
?>

<?php
namespace content_enter\verify\sms;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\sms\tg as bot;


class model extends \content_enter\main\model
{

	/**
	 * send verification code to the user sms
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function send_sms_code()
	{
		if(!self::get_enter_session('mobile'))
		{
			return false;
		}

		$code = self::get_enter_session('verification_code');

		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'mobile'  => self::get_enter_session('mobile'),
				'code'    => $code,
				'session' => $_SESSION,
			]
		];

		$request           = [];
		$request['mobile'] = self::get_enter_session('mobile');
		$request['msg']    = 'signup';
		$request['args']   = $code;

		if(self::$dev_mode)
		{
			$kavenegar_send_result = true;
		}
		else
		{
			$kavenegar_send_result = \lib\utility\sms::send($request);
		}

		if($kavenegar_send_result === 411 && substr(self::get_enter_session('mobile'), 0, 2) === '98')
		{
			// invalid user mobil
			db\logs::set('kavenegar:service:411:sms', self::user_data('id'), $log_meta);
			return false;
		}
		elseif($kavenegar_send_result === 22)
		{
			db\logs::set('kavenegar:service:done:sms', self::user_data('id'), $log_meta);
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

			db\logs::set('enter:send:sems:result', self::user_data('id'), $log_meta);

			return true;
		}
		else
		{
			db\logs::set('enter:send:cannot:send:sms', self::user_data('id'), $log_meta);
		}

		return false;
	}
}
?>

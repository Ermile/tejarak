<?php
namespace lib\utility\plan;
use \lib\utility;
use \lib\utility\human;
use \lib\debug;
use \lib\db;
use \lib\utility\telegram;
use \lib\utility\plan;

trait feature
{
	public static $my_name               = null;
	public static $my_start_time         = null;
	public static $my_group_id           = null;
	public static $my_plus               = 0;
	public static $my_minus              = 0;
	public static $my_team_id            = null;
	public static $my_admins             = null;
	public static $my_admins_id          = null;
	public static $my_admins_telegram_id = null;
	public static $_args                 = [];
	public static $my_team_detail        = [];
	public static $my_team_name          = null;
	public static $my_team_name_hashtag  = null;
	public static $admins_access_detail  = [];
	public static $my_team_report_header = null;
	public static $my_team_report_footer = null;
	public static $my_report_settings    = [];
	public static $yourself_chat_id      = null;


	// 'telegram_group'    => string 'on' (length=2)
	// 'start_time_first'  => string 'on' (length=2)
	// 'first_member_name' => string 'on' (length=2)
	// 'report_daily'      => string 'on' (length=2)
	// 'report_daily_time' => string 'on' (length=2)
	// 'report_daily_gold' => string 'on' (length=2)
	// 'report_count'      => string '-1' (length=2)


	/**
	 * check some date
	 * when this var is false
	 * means we can not run the feature
	 * return false
	 *
	 * @var        boolean
	 */
	public static $check_is_true = true;


	/**
	 * Sets the yourself message.
	 *
	 * @param      <type>  $_admins   The admins
	 * @param      <type>  $_message  The message
	 * @param      <type>  $_sort     The sort
	 */
	public static function set_yourself_message($_admins, $_message, $_sort)
	{
		if(!self::$yourself_chat_id)
		{
			return;
		}

		$admin_chat_id = array_column($_admins, 'chat_id');
		if(in_array(self::$yourself_chat_id, $admin_chat_id))
		{
			return;
		}

		telegram::sendMessage(self::$yourself_chat_id, self::generate_telegram_message($_message), $_sort);

	}

	/**
	 * check feacher
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check_feature($_args)
	{
		self::$_args = $_args;
		if(!isset($_args['args']['userteam_details']['team_id'])) return false;

		self::$my_team_id = $_args['args']['userteam_details']['team_id'];

		switch ($_args['type'])
		{
			case 'enter':
				$first_msg = false;

				$config_is_run = false;
				if(plan::access('telegram:enter:msg', self::$my_team_id))
				{
					self::config();

					$config_is_run = true;

					if(!self::$check_is_true)
					{
						return false;
					}

					self::set_yourself_message(self::$admins_access_detail, 'enter', ['sort' => 3]);

					foreach (self::$admins_access_detail as $key => $value)
					{
						if(isset($value['chat_id']) && isset($value['reportenterexit']) && $value['reportenterexit'])
						{
							telegram::sendMessage($value['chat_id'], self::generate_telegram_message('enter'), ['sort' => 3]);
							$first_msg = true;
						}
					}
				}

				// first msg in day
				if(plan::access('telegram:first:of:day:msg', self::$my_team_id))
				{
					if(!$config_is_run)
					{
						self::config();
					}
					// check if this user is first login user
					if(\lib\db\hours::enter(self::$my_team_id) <=1)
					{
						if(isset(self::$my_report_settings['telegram_group']) && self::$my_report_settings['telegram_group'])
						{
							if(plan::access('telegram:first:of:day:msg:group', self::$my_team_id))
							{
								telegram::sendMessageGroup(self::$my_group_id, self::generate_telegram_message('first_enter'), ['sort' => 4]);
							}
						}

						self::set_yourself_message(self::$admins_access_detail, 'date_now', ['sort' => 3]);

						foreach (self::$admins_access_detail as $key => $value)
						{
							if(isset($value['chat_id']) && isset($value['reportenterexit']) && $value['reportenterexit'])
							{
								if(!$first_msg)
								{
									telegram::sendMessage($value['chat_id'], self::generate_telegram_message('first_enter'), ['sort' => 3]);
								}

								telegram::sendMessage($value['chat_id'], self::generate_telegram_message('date_now'), ['sort' => 1]);
							}
						}
					}
				}
				// send message by sorting
				telegram::sort_send();
				telegram::clean_cash();

				break;

			case 'exit':

				$config_is_run = false;
				if(plan::access('telegram:exit:msg', self::$my_team_id))
				{
					self::config();

					$config_is_run = true;

					if(!self::$check_is_true)
					{
						return false;
					}

					// check if this user is first login user
					$is_first_transaction = \lib\db\hours::enter(self::$my_team_id);
					$is_first_transaction = ($is_first_transaction <= 1) ? true : false;

					if($is_first_transaction)
					{
						if(isset(self::$my_report_settings['telegram_group']) && self::$my_report_settings['telegram_group'])
						{
							if(plan::access('telegram:first:of:day:msg:group', self::$my_team_id))
							{
								telegram::sendMessageGroup(self::$my_group_id, self::generate_telegram_message('date_now'), ['sort' => 1]);
								// send the message to her youser id
								if(self::$yourself_chat_id)
								{
									telegram::sendMessage(self::$yourself_chat_id, self::generate_telegram_message('date_now'), ['sort' => 3]);
								}
							}
						}
					}

					self::set_yourself_message(self::$admins_access_detail, 'exit', ['sort' => 3]);

					foreach (self::$admins_access_detail as $key => $value)
					{
						if(isset($value['chat_id']) && isset($value['reportenterexit']) && $value['reportenterexit'])
						{
							if($is_first_transaction)
							{
								telegram::sendMessage($value['chat_id'], self::generate_telegram_message('date_now'), ['sort' => 1]);
							}
							telegram::sendMessage($value['chat_id'], self::generate_telegram_message('exit'), ['sort' => 2]);
						}
					}
				}

				if(plan::access('telegram:end:day:report', self::$my_team_id))
				{
					if(!$config_is_run)
					{
						self::config();
					}

					if(\lib\db\hours::live(self::$my_team_id) <= 0 )
					{
						if(isset(self::$my_report_settings['telegram_group']) && self::$my_report_settings['telegram_group'])
						{
							if(isset(self::$my_report_settings['report_daily']) && self::$my_report_settings['report_daily'])
							{
								if(plan::access('telegram:end:day:report:group', self::$my_team_id))
								{
									telegram::sendMessageGroup(self::$my_group_id, self::generate_telegram_message('report_end_day'), ['sort' => 4]);
								}
							}
						}

						foreach (self::$admins_access_detail as $key => $value)
						{
							if(isset($value['chat_id']) && isset($value['reportdaily']) && $value['reportdaily'])
							{
								telegram::sendMessage($value['chat_id'], self::generate_telegram_message('report_end_day_admin'), ['sort' => 3]);
							}
						}
					}
				}
				// send message by sorting
				telegram::sort_send();
				telegram::clean_cash();

				break;
			default:
				# code...
				break;
		}
	}

}
?>
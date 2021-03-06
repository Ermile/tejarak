<?php
namespace lib\app\hours;


trait add
{

	/**
	 * Adds hours.
	 * add member time
	 * start or end of time save on this function and
	 * minus and plus time
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function add_hours($_args = [])
	{
		$default_args =
		[
			'method' => 'post'
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		// // \dash\notif::title(T_("Operation Faild"));

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\app::request(),
			]
		];

		if(!\dash\user::id())
		{
			\dash\db\logs::set('api:hours:user_id:notfound', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$user = \dash\app::request('user');
		$user = \dash\coding::decode($user);

		if(!$user)
		{
			\dash\db\logs::set('api:hours:user:notfound', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Member not found"), 'user', 'arguments');
			return false;
		}


		// get team and check it
		$team = \dash\app::request('team');
		if(!$team)
		{
			\dash\db\logs::set('api:hours:team:notfound', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Team not found"), 'team', 'permission');
			return false;
		}
		// load team data
		$team_detail = \lib\db\teams::access_team($team, \dash\user::id(), ['action' => 'save_hours', 'change_hour_user' => $user]);

		// check the team exist
		if(isset($team_detail['id']))
		{
			$team_id = $team_detail['id'];
		}
		else
		{
			\dash\db\logs::set('api:hours:team:notfound:invalid', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Can not access to set time of this team"), 'team', 'permission');
			return false;
		}

		// CHECK PERMISSION TO ADD TIME
		// if(\lib\db\teams::access_in_team_id($team_id, \dash\user::id()))

		$minus = \dash\app::request('minus');
		if($minus && !is_numeric($minus))
		{
			\dash\db\logs::set('api:hours:minus:notfound', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Member not found"), 'minus', 'arguments');
			return false;
		}

		$plus = \dash\app::request('plus');
		if($plus && !is_numeric($plus))
		{
			\dash\db\logs::set('api:hours:plus:notfound', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Member not found"), 'plus', 'arguments');
			return false;
		}

		$type = \dash\app::request('type');
		if(!$type)
		{
			\dash\db\logs::set('api:hours:type:notset', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Type not set"), 'type', 'arguments');
			return false;
		}

		if(!in_array($type, ['enter', 'exit']))
		{
			\dash\db\logs::set('api:hours:type:invalid', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Invalid arguments type"), 'type', 'arguments');
			return false;
		}

		$desc = \dash\app::request('desc');
		if($desc && mb_strlen($desc) > 500)
		{
			\dash\db\logs::set('api:hours:desc:max:limit', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Your text is too large"), 'text_enter', 'arguments');
			return false;
		}

		$args            = [];
		$args['user_id'] = $user;
		$args['team_id'] = $team_id;
		$args['minus']   = $minus;
		$args['plus']    = $plus;
		$args['type']    = $type;
		$args['gateway'] = \dash\user::id();
		$args['desc']    = $desc;

		if($_args['method'] === 'post')
		{
			if($type === 'enter')
			{
				// save hours
				$hours_id = \lib\db\hours::save_enter($args);
			}
			else
			{
				$hours_id = \lib\db\hours::save_exit($args);
			}
		}
		else
		{
			\dash\db\logs::set('api:hours:method:invalid', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Invalid method of api"), 'method', 'permission');
			return false;
		}

		if(\dash\engine\process::status())
		{
			// \dash\notif::title(null);
			$name = null;

			if(\dash\temp::get('enter_exit_name'))
			{
				$name = \dash\temp::get('enter_exit_name');
			}
			if($type === 'enter')
			{
				$msg_notify = T_("Dear :name;", ['name'=> $name])."<br />". T_('Your enter was registered.').' '. T_("Have a good time.");
				\dash\notif::ok($msg_notify);
			}
			else
			{
				$msg_notify = T_("Bye Bye :name ;)", ['name'=> $name]);
				\dash\notif::ok($msg_notify);
			}
		}
	}
}
?>
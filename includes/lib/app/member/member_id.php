<?php
namespace lib\app\member;


trait member_id
{
	/**
	 * find user id
	 *
	 * @param      <type>   $_args      The arguments
	 * @param      <type>   $_log_meta  The log meta
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function find_member_id($_args, $_log_meta, $_team_id)
	{
		if($_args['method'] === 'patch' && !\dash\app::request('mobile'))
		{
			return true;
		}

		$mobile           = null;
		$mobile_syntax    = null;
		$check_user_exist = null;

		$log_meta = $_log_meta;

		// get mobile of user
		$mobile           = \dash\app::request("mobile");
		$mobile_syntax    = \dash\utility\filter::mobile($mobile);
		$check_user_exist = null;
		if($mobile && !$mobile_syntax)
		{
			if($_args['save_log']) \dash\db\logs::set('api:member:mobile:not:set', \dash\user::id(), $log_meta);
			if($_args['debug']) \dash\notif::error(T_("Invalid mobile number"), 'mobile', 'arguments');
			return false;
		}
		elseif($mobile && $mobile_syntax && ctype_digit($mobile))
		{
			$mobile = $mobile_syntax;
		}
		else
		{
			$mobile_syntax = $mobile = null;
		}

		self::$master_user_id = null;
		// check userid exist in userteam by this team
		$check_not_duplicate_userteam = false;
		/**
		 ****************************************************************************
		 * find user id
		 *
		 * @var        <type>
		 */
		// post to add new member
		if($_args['method'] === 'post')
		{
			// mobile is set
			if($mobile)
			{
				$check_user_exist = \dash\db\users::get_by_mobile($mobile);
				// the mobile was exist
				if(isset($check_user_exist['id']))
				{
					self::$master_user_id = $check_user_exist['id'];
					$check_not_duplicate_userteam = true;
				}
				else
				{
					// we need to get user id to insert new record of userteams
					// signup empty to get user id
					$signup =
					[
						'mobile'      => $mobile,
						'password'    => null,
						'displayname' => null,
						'datecreated' => date("Y-m-d H:i:s"),
					];

					self::$master_user_id = \dash\db\users::signup($signup);
				}
			}
			else
			{
				// we need to get user id to insert new record of userteams
				// signup empty to get user id
				$signup =
				[
					'mobile'      => null,
					'password'    => null,
					'displayname' => null,
					'datecreated' => date("Y-m-d H:i:s"),
				];

				self::$master_user_id = \dash\db\users::signup($signup);
			}
		}
		elseif($_args['method'] === 'patch')
		{

			$request_id = \dash\app::request('id');
			$request_id = \dash\coding::decode($request_id);

			if($request_id)
			{
				$request_user_id = self::$userteam_record_detail;
			}

			if(isset($request_user_id['user_id']))
			{
				$request_user_id = $request_user_id['user_id'];
			}
			else
			{
				if($_args['save_log']) \dash\db\logs::set('api:member:user_id:not:invalid:patch:not:found', \dash\user::id(), $log_meta);
				if($_args['debug']) \dash\notif::error(T_("Invalid user id"), 'user', 'system');
				return false;
			}

			if($request_user_id)
			{
				// save old user id to check if change
				// change all the this user id
				// in all team
				self::$OLD_USER_ID = $request_user_id;

				$old_user_id = \lib\db\userteams::get_list(['user_id' => $request_user_id,'team_id' => $_team_id, 'limit' => 1]);
				self::$old_user_id = $old_user_id;

				if(!isset($old_user_id['user_id']) || !array_key_exists('mobile', $old_user_id))
				{
					if($_args['save_log']) \dash\db\logs::set('api:member:user_id:not:invalid:patch', \dash\user::id(), $log_meta);
					if($_args['debug']) \dash\notif::error(T_("Invalid user id"), 'user', 'system');
					return false;
				}
			}
			else
			{
				if($_args['save_log']) \dash\db\logs::set('api:member:user_id:not:set:patch', \dash\user::id(), $log_meta);
				if($_args['debug']) \dash\notif::error(T_("User id not set"), 'user', 'system');
				return false;
			}

			// if(isset($old_user_id['rule']) && $old_user_id['rule'] === 'admin')
			// {
			// 	self::$master_user_id = $old_user_id['user_id'];
			// }
			// else
			if($old_user_id['mobile'])
			{
				if($mobile)
				{
					if($mobile == $old_user_id['mobile'])
					{
						self::$master_user_id = $old_user_id['user_id'];
					}
					else
					{
						$check_user_exist = \dash\db\users::get_by_mobile($mobile);
						// the mobile was exist
						if(isset($check_user_exist['id']))
						{
							self::$master_user_id = $check_user_exist['id'];
							$check_not_duplicate_userteam = true;
						}
						else
						{
							// we need to get user id to insert new record of userteams
							// signup empty to get user id
							$signup =
							[
								'mobile'      => $mobile,
								'password'    => null,
								'displayname' => null,
								'datecreated' => date("Y-m-d H:i:s"),
							];

							self::$master_user_id = \dash\db\users::signup($signup);
						}
					}
				}
				else
				{
					// the user remove mobile
					// signup empty to get user id
					$signup =
					[
						'mobile'      => null,
						'password'    => null,
						'displayname' => null,
						'datecreated' => date("Y-m-d H:i:s"),
					];

					self::$master_user_id = \dash\db\users::signup($signup);
				}
			}
			else
			{
				if($mobile)
				{
					// unreachable old user id
					\dash\db\users::update(['status' => 'unreachable'], $old_user_id['user_id']);

					$check_user_exist = \dash\db\users::get_by_mobile($mobile);
					// the mobile was exist
					if(isset($check_user_exist['id']))
					{
						self::$master_user_id = $check_user_exist['id'];
						$check_not_duplicate_userteam = true;
					}
					else
					{
						// we need to get user id to insert new record of userteams
						// signup empty to get user id
						$signup =
						[
							'mobile'      => $mobile,
							'password'    => null,
							'displayname' => null,
							'datecreated' => date("Y-m-d H:i:s"),
						];

						self::$master_user_id = \dash\db\users::signup($signup);
					}
				}
				else
				{
					self::$master_user_id = $old_user_id['user_id'];
				}
			}
		}

		/**
		 * end find userid
		 ****************************************************************************
		 */


		if(!self::$master_user_id)
		{
			if($_args['save_log']) \dash\db\logs::set('api:member:user_id:not:found:and:cannot:signup', \dash\user::id(), $log_meta);
			if($_args['debug']) \dash\notif::error(T_("User id not found"), 'user', 'system');
			return false;
		}

		self::$NEW_USER_ID = self::$master_user_id;


		// to redirect site in new url
		\dash\temp::set('new_shcode', \dash\coding::encode(self::$master_user_id));

		if($check_not_duplicate_userteam)
		{
			$userteam_record = \lib\db\userteams::get(['user_id' => self::$master_user_id, 'team_id' => $_team_id, 'limit' => 1]);
			if($userteam_record)
			{
				if($_args['save_log']) \dash\db\logs::set('api:member:duplicate:user:team', \dash\user::id(), $log_meta);
				if($_args['debug']) \dash\notif::error(T_("This user was already added to this team"), 'mobile', 'arguments');
				return false;
			}
		}
	}
}
?>
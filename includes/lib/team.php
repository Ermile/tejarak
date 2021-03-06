<?php
namespace lib;

/**
 * Class for team.
 */
class team
{

	private static $team         = [];
	private static $team_admins  = [];
	private static $team_members = [];


	/**
	 * clean chach to load detail again
	 * user in edit team
	 */
	public static function clean()
	{
		\dash\session::set('lib_team_detail', null);
		\dash\session::set('lib_team_admin', null);
		\dash\session::set('lib_team_member', null);
		self::$team = [];
	}


	/**
	 * initial team detail
	 */
	public static function init()
	{
		if(!empty(self::$team))
		{
			return;
		}


		if
		(
			\dash\session::get('lib_team_admin') &&
			\dash\session::get('lib_team_member') &&
			\dash\session::get('lib_team_detail')
		)
		{
			self::$team_admins  = \dash\session::get('lib_team_admin');
			self::$team_members = \dash\session::get('lib_team_member');
			self::$team         = \dash\session::get('lib_team_detail');

			return;
		}

		$team_code = \dash\request::get('id');
		$team_id   = \dash\coding::decode($team_code);

		if($team_id)
		{
			$lib_team_detail = \lib\db\teams::get(['id' => $team_id, 'limit' => 1]);

			if(is_array($lib_team_detail))
			{
				self::$team = $lib_team_detail;
				\dash\session::set('lib_team_detail', $lib_team_detail);
			}

			self::$team_members = \lib\db\userteams::get(['team_id' => $team_id, 'status' => ["IN", "('active','deactive')"], 'rule' => ["<>", "'gateway'"]]);

			if(is_array(self::$team_members))
			{
				foreach (self::$team_members as $key => $value)
				{
					if(isset($value['rule']) && $value['rule'] === 'admin')
					{
						array_push(self::$team_admins, $value);
					}
				}
			}

			\dash\session::set('lib_team_admin', self::$team_admins);
			\dash\session::set('lib_team_member', self::$team_members);
		}
	}


	/**
	 * get id of team
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function id()
	{
		self::init();

		if(isset(self::$team['id']))
		{
			return intval(self::$team['id']);
		}
		return null;
	}


	/**
	 * return the creator of team
	 */
	public static function creator()
	{
		self::init();
		if(isset(self::$team['creator']))
		{
			return intval(self::$team['creator']);
		}
		return null;
	}


	/**
	 * Determines if creator.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  True if creator, False otherwise.
	 */
	public static function is_creator($_user_id)
	{
		return intval($_user_id) === intval(self::creator());
	}


	/**
	 * get admins of the team
	 */
	public static function admins()
	{
		self::init();

		if(self::id())
		{
			return array_column(self::$team_admins, 'user_id');
		}

		return [];
	}


	/**
	 * check user id is admin or no
	 */
	public static function is_admin($_user_id)
	{
		return in_array($_user_id, self::admins());
	}


	/**
	 * get members of the team
	 */
	public static function members()
	{
		self::init();

		if(self::id())
		{
			return array_column(self::$team_members, 'user_id');
		}

		return [];
	}


	/**
	 * check user id is member or no
	 */
	public static function is_member($_user_id)
	{
		return in_array($_user_id, self::members());
	}


	/**
	 * alise of is_member function
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function in_team()
	{
		return self::is_member(...func_get_args());
	}


	/**
	 * get team detail
	 */
	public static function detail($_name = null)
	{
		self::init();

		if($_name)
		{
			if(array_key_exists($_name, self::$team))
			{
				return self::$team[$_name];
			}
			return null;
		}
		else
		{
			return self::$team;
		}
	}
}
?>
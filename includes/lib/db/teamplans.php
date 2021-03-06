<?php
namespace lib\db;


class teamplans
{
	/**
	 * the plan change nae whit number of curren permission group
	 *
	 * @var        array
	 */
	public static $PLANS = [];


	/**
	 * get plan list
	 */
	public static function config()
	{
		if(empty(self::$PLANS))
		{
			self::$PLANS = \lib\utility\plan::list(true, true);
		}
	}


	/**
	 * return the plan code
	 *
	 * @param      <type>  $_name  The name
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function plan_code($_name)
	{
		self::config();

		if(isset(self::$PLANS[$_name]))
		{
			return self::$PLANS[$_name];
		}
	}

	/**
	 * get the plan name
	 *
	 * @param      <type>  $_code  The code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function plan_name($_code)
	{
		self::config();

		$temp = array_flip(self::$PLANS);

		if(isset($temp[$_code]))
		{
			return $temp[$_code];
		}
	}

	/**
	 * add new teamplans
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		return \dash\db\config::public_insert('teamplans', ...func_get_args());
	}


	/**
	 * update teamplans record
	 */
	public static function update()
	{
		return \dash\db\config::public_update('teamplans', ...func_get_args());
	}


	/**
	 * get current team plan
	 *
	 * @param      <type>   $_team_id  The team identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function current($_team_id)
	{
		if(!$_team_id || !is_numeric($_team_id))
		{
			return false;
		}
		$query =
		"
			SELECT
				*
			FROM
				teamplans
			WHERE
				teamplans.team_id = $_team_id AND
				teamplans.status = 'enable'
			ORDER BY
				teamplans.id DESC
			LIMIT 1
		";
		$result = \dash\db::get($query, null, true);
		if(isset($result['plan']))
		{
			$result['plan_name'] = self::plan_name($result['plan']);
		}
		return $result;
	}


	/**
	 * set plan of team
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set($_args)
	{
		$default_args =
		[
			'team_id'       => null,
			'plan'          => null,
			'start'         => date("Y-m-d H:i:s"),
			'lastcalcdate'  => date("Y-m-d H:i:s"),
			'end'           => null,
			'creator'       => null,
			'desc'          => null,
			'maked_invoice' => true,
		];

		if(is_array($_args))
		{
			$_args = array_merge($default_args, $_args);
		}

		$args_make_invoice = $_args['maked_invoice'];
		unset($_args['maked_invoice']);

		$log_meta =
		[
			'meta' =>
			[
				'input'   => $_args,
			],
		];

		if(!$_args['team_id'] || !$_args['plan'] || !$_args['start'])
		{
			return false;
		}

		$team_details = \lib\db\teams::get_by_id($_args['team_id']);

		$log_meta['meta']['team_details'] = $team_details;

		if(isset($team_details['creator']))
		{
			if(intval($team_details['creator']) === intval($_args['creator']))
			{
				// just the creator of team can change
			}
			else
			{
				\dash\db\logs::set('plan:change:not:creator', $_args['creator'], $log_meta);
				\dash\notif::error(T_("Just creator of team can change the plan"));
				return false;
			}
		}
		else
		{
			\dash\db\logs::set('plan:change:team:details:not:found', $_args['creator'], $log_meta);
			return false;
		}

		$_args['status'] = 'enable';

		$_args['plan'] = self::plan_code($_args['plan']);
		if(!$_args['plan'])
		{
			\dash\db\logs::set('plan:cannot:support', $_args['creator'], $log_meta);
			return false;
		}

		$current = self::current($_args['team_id']);

		if(isset($current['plan']) && intval($current['plan']) === intval($_args['plan']))
		{
			\dash\notif::error(T_("This plan is already active for you"));
			return false;
		}

		$update_teamplans        = [];
		$update_teamplans['end'] = date("Y-m-d H:i:s");

		$need_maked_invoice = true;

		if(isset($current['start']) && (time() - strtotime($current['start']) < (60 * 60)))
		{
			$need_maked_invoice = false;
			$update_teamplans['status'] = 'skipped';
		}
		else
		{
			$update_teamplans['status'] = 'disable';
		}

		$prepayment_new  = \lib\utility\plan::get_detail($_args['plan']);

		if(is_array($prepayment_new ) && array_key_exists('prepayment', $prepayment_new ) && $prepayment_new ['prepayment'] === true)
		{
			$prepayment_new  = true;
		}
		else
		{
			$prepayment_new  = false;
		}

		$prepayment_old = false;
		if(isset($current['plan']))
		{
			$prepayment_old  = \lib\utility\plan::get_detail($current['plan']);

			if(is_array($prepayment_old ) && array_key_exists('prepayment', $prepayment_old ) && $prepayment_old['prepayment'] === true)
			{
				$prepayment_old  = true;
			}
			else
			{
				$prepayment_old  = false;
			}

		}

		if($args_make_invoice)
		{
			if($need_maked_invoice || $prepayment_new || $prepayment_old)
			{
				$old_plan_id = isset($current['plan']) ? $current['plan'] : null;
				$teamplan_id = isset($current['id']) ? $current['id'] : null;

				$maked_invoice = new \lib\utility\calc($_args['team_id']);
				$maked_invoice->old_plan(self::plan_name($old_plan_id));
				$maked_invoice->new_plan(self::plan_name($_args['plan']));
				$maked_invoice->type('change_plan');
				$maked_invoice->save(true);
				$maked_invoice->notify(true);
				$maked_invoice->old_teamplan_id($teamplan_id);
				$maked_invoice = $maked_invoice->calc();

				// $maked_invoice = \lib\utility\invoices::team_plan($_args['team_id'], ['current' => $current, 'new' => $_args]);
				// in plan full or other plan
				// if system can not creat invoice
				// we have an error
				// never shoud change team plan
				// for example the full plan need to pay the money before change it!
				if(!$maked_invoice)
				{
					return false;
				}
			}
		}

		if(isset($current['id']))
		{
			self::update($update_teamplans, $current['id']);
		}

		$log_meta['meta']['current'] = $current;

		\dash\db\logs::set('plan:changed', $_args['creator'], $log_meta);
		// insert new teamplans
		self::insert($_args);

		$update_team =
		[
			'plan'         => self::plan_name($_args['plan']),
			'startplan'    => date("Y-m-d H:i:s"),
			'startplanday' => date("d"),
		];

		\lib\db\teams::update($update_team, $_args['team_id']);

		return true;
	}


	public static function search($_string = null, $_options = [])
	{
		if(!is_array($_options))
		{
			$_options = [];
		}

		$default_option =
		[
			'search_field' =>
			"
				(
					teams.name LIKE '%__string__%'
				)

			",
			'public_show_field' =>
				"
					teamplans.*,
					teams.name, teams.shortname
				",
			'master_join'         => " INNER JOIN teams ON teams.id = teamplans.team_id ",
		];

		$_options = array_merge($default_option, $_options);

		return \dash\db\config::public_search('teamplans', $_string, $_options);

	}
}
?>
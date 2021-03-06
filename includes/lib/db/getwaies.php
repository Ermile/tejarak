<?php
namespace lib\db;
use \lib\db;

class getwaies
{

	/**
	 * add new gateway
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$set = \dash\db\config::make_set($_args);
		if($set)
		{
			\dash\db::query("INSERT INTO getwaies SET $set");
			return \dash\db::insert_id();
		}
	}



	/**
	 * get gateway record
	 *
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		if($_args)
		{
			$only_one_value = false;
			$limit = null;
			if(isset($_args['limit']) && $_args['limit'] === 1)
			{
				$only_one_value = true;
				$limit = " LIMIT 1 ";
				unset($_args['limit']);
			}
			$where = \dash\db\config::make_where($_args, ['table_name' => 'getwaies']);
			$query = " SELECT getwaies.* FROM getwaies WHERE $where $limit ";
			$result = \dash\db::get($query, null, $only_one_value);
			return $result;
		}
		return false;
	}



	/**
	 * get gateway record
	 *
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_by_id($_getwaies_id, $_boss)
	{
		if($_getwaies_id && $_boss)
		{
			$query =
			"
				SELECT
					getwaies.*,
					users.mobile AS `mobile`,
					users.displayname AS `displayname`,
					users.email AS `email`
				FROM
					getwaies
				INNER JOIN users ON users.id = getwaies.user_id
				INNER JOIN teams ON teams.id = getwaies.team_id
				WHERE
					getwaies.id = $_getwaies_id AND teams.boss = $_boss
				LIMIT 1
			";
			$result = \dash\db::get($query, null, true);
			return $result;
		}
		return false;
	}



	/**
	 * remove
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_args)
	{
		if(isset($_args['team_id']) && isset($_args['user_id']) && is_numeric($_args['team_id']) && is_numeric($_args['user_id']))
		{
			$query =
			"
				DELETE FROM getwaies
				WHERE
					getwaies.team_id = $_args[team_id] AND
					getwaies.user_id = $_args[user_id]
				LIMIT 1
			";
			$result = \dash\db::get($query, null, true);
			return $result;

		}
	}


	/**
	 * update gateway
	 *
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $_id    The identifier
	 */
	public static function update($_args, $_id)
	{
		$set = \dash\db\config::make_set($_args);
		if(!$set || !$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "UPDATE getwaies SET $set WHERE id = $_id LIMIT 1";
		return \dash\db::query($query);
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 */
	public static function search($_string = null, $_options = [])
	{
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of gateway
			$_options['get_last'] = true;
		}

		$default_options =
		[
			// just return the count record
			"get_count"      => false,
			// enable|disable paignation,
			"pagenation"     => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit = null and pagenation = false to get all record whitout limit
			"limit"          => 5,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"    => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"      => 10,
			// the the last record inserted to post table
			"get_last"       => false,
			// default order by DESC you can change to DESC
			"order"          => "DESC",
			// custom sort by field
			"sort"           => null,
		];

		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		// ------------------ get count
		$only_one_value = false;
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
			$public_fields  = " COUNT(getwaies.id) AS 'getwaiescount' FROM	getwaies";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = " * FROM getwaies";

			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}


		if($_options['sort'])
		{
			$temp_sort = null;
			switch ($_options['sort'])
			{
				default:
					$temp_sort = $_options['sort'];
					break;
			}
			$_options['sort'] = $temp_sort;
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY getwaies.id DESC ";
			}
		}
		else
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY getwaies.id $_options[order] ";
			}
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		$no_limit = false;
		if($_options['limit'] === false)
		{
			$no_limit = true;
		}

		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "getwaies.`field` LIKE '%valud%'"
					$where[] = " getwaies.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " getwaies.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " getwaies.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " getwaies.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);

			$search = "(getwaies.title  LIKE '%$_string%' )";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($where)
		{
			$where = "WHERE $where";
		}
		elseif($search)
		{
			$where = "WHERE";
		}

		if($pagenation && !$get_count)
		{
			$pagenation_query = "SELECT	COUNT(getwaies.id) AS `count`	FROM getwaies	$where $search ";
			$pagenation_query = \dash\db::get($pagenation_query, 'count', true);

			list($limit_start, $limit) = \dash\db::pagnation((int) $pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$json = json_encode(func_get_args());
		if($no_limit)
		{
			$limit = null;
		}
		$query = " SELECT $public_fields $where $search $order $limit -- getwaies::search() 	-- $json";

		if(!$only_one_value)
		{
			$result = \dash\db::get($query, null, false);
			$result = \dash\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \dash\db::get($query, 'getwaiescount', true);
		}

		return $result;
	}

}
?>
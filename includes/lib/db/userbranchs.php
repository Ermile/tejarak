<?php
namespace lib\db;
use \lib\db;

class userbranchs
{

	/**
	 * add new election
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$set = \lib\db\config::make_set($_args);
		if($set)
		{
			\lib\db::query("INSERT IGNORE INTO userbranchs SET $set");
			return \lib\db::insert_id();
		}
	}



	/**
	 * get election record
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
			$limit          = null;

			if(isset($_args['limit']))
			{
				if($_args['limit'] === 1)
				{
					$only_one_value = true;
				}

				$limit = " LIMIT $_args[limit] ";
			}

			unset($_args['limit']);

			$where = \lib\db\config::make_where($_args);
			$query =
			"
				SELECT
					userbranchs.*,
					users.user_mobile AS `mobile`,
					users.user_displayname AS `displayname`,
					users.user_email AS `email`,
					posts.post_meta AS `file_detail`
 				FROM
					userbranchs
				INNER JOIN users ON users.id = userbranchs.user_id
				LEFT JOIN posts ON posts.id = users.user_file_id
				WHERE
					$where
				$limit
			";
			$result = \lib\db::get($query, null, $only_one_value);
			return $result;
		}
		return false;
	}



	/**
	 * get election record
	 *
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_by_id($_userbranchs_id, $_boss)
	{
		if($_userbranchs_id && $_boss)
		{
			$query =
			"
				SELECT
					userbranchs.*,
					users.user_mobile AS `mobile`,
					users.user_displayname AS `displayname`,
					users.user_email AS `email`
				FROM
					userbranchs
				INNER JOIN users ON users.id = userbranchs.user_id
				INNER JOIN teams ON teams.id = userbranchs.team_id
				WHERE
					userbranchs.id = $_userbranchs_id AND teams.boss = $_boss
				LIMIT 1
			";
			$result = \lib\db::get($query, null, true);
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
				DELETE FROM userbranchs
				WHERE
					userbranchs.team_id = $_args[team_id] AND
					userbranchs.user_id = $_args[user_id]
				LIMIT 1
			";
			$result = \lib\db::get($query, null, true);
			return $result;

		}
	}


	/**
	 * update election
	 *
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $_id    The identifier
	 */
	public static function update($_args, $_id)
	{
		$set = \lib\db\config::make_set($_args);
		if(!$set || !$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "UPDATE userbranchs SET $set WHERE id = $_id LIMIT 1";
		return \lib\db::query($query);
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
			// default return of this function 10 last record of election
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
			"limit"          => 15,
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
			$public_fields  = " COUNT(userbranchs.id) AS 'userbranchscount' FROM	userbranchs";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = " * FROM userbranchs";

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
				$order = " ORDER BY userbranchs.id DESC ";
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
				$order = " ORDER BY userbranchs.id $_options[order] ";
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
					// for similar "userbranchs.`field` LIKE '%valud%'"
					$where[] = " userbranchs.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " userbranchs.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " userbranchs.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " userbranchs.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);

			$search = "(userbranchs.title  LIKE '%$_string%' )";
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
			$pagenation_query = "SELECT	COUNT(userbranchs.id) AS `count`	FROM userbranchs	$where $search ";
			$pagenation_query = \lib\db::get($pagenation_query, 'count', true);

			list($limit_start, $limit) = \lib\db::pagnation((int) $pagenation_query, $limit);
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
		$query = " SELECT $public_fields $where $search $order $limit -- userbranchs::search() 	-- $json";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query, null, false);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'userbranchscount', true);
		}

		return $result;
	}

}
?>
<?php
namespace lib\app\team;


trait get
{
	public static $logo_urls = [];

	/**
	 * ready data of team to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready_team($_data, $_options = [])
	{
		$default_options =
		[
			'check_is_my_team' => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'creator':
				// case 'parent':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				case 'desc2':
					$result['address'] = $value ? (string) $value : null;
					break;

				case 'type':

					if($value === 'classroom')
					{
						if(isset($_data['desc3']))
						{
							$result['multi_classroom'] = $_data['desc3'] ? true : false;
						}
						if(isset($_data['desc4']))
						{
							$result['classroom_size'] = isset($_data['desc4']) ? $_data['desc4'] : null;
						}
					}
					else
					{
						if(isset($_data['desc3']))
						{
							$result['awards'] = isset($_data['desc3']) ? $_data['desc3'] : null;
						}

						if(isset($_data['desc4']))
						{
							$result['about'] = isset($_data['desc4']) ? $_data['desc4'] : null;
						}
					}
					break;

				case 'phone1':
					$result['tel'] = $value ? (string) $value : null;
					break;
				case 'phone2':
					$result['fax'] = $value ? (string) $value : null;
					break;

				// case 'status':
					// only enable team can be show
					// switch ($value)
					// {
					// 	case 'enable':
					// 	case 'close':
					// 		$result[$key] = $value ? (string) $value : null;
					// 		break;
					// 	default:
					// 		return false;
					// 		break;
					// }
					// break;

				case 'country':
				case 'city':
				case 'province':
				case 'zipcode':
				case 'name':
				case 'website':
				case 'desc':
				case 'alias':
				case 'status':
				case 'privacy':
				case 'rule':
				case 'gender':
					$result[$key] = isset($value) ? (string) $value : null;
					break;
				case 'shortname':
					$result['short_name'] = $value ? (string) $value : null;
					$result['url'] = \lib\app\option::host('with_language'). '/'. $value;
					break;

				case 'lang':
					$result['language'] = isset($value) ? (string) $value : null;
					break;

				case 'manualtimeenter':
					$result['manual_time_enter'] = $value ? true : false;
					break;

				case 'manualtimeexit':
					$result['manual_time_exit'] = $value ? true : false;
					break;

				case 'sendphoto':
					$result['send_photo'] = $value ? true : false;
					break;

				case 'eventtitle':
					$result['event_title'] = isset($value) ? $value : null;
					break;

				case 'eventdate':
				case 'eventenddate':
					$myKey = $key === 'eventenddate' ? 'event_date' : 'event_date_start';
					if($value)
					{
						$strtotime            = strtotime($value);
						$result[$myKey] = date("Y-m-d", $strtotime);
						$toGregorian          = \dash\utility\jdate::toGregorian(date("Y", $strtotime), date("m", $strtotime), date("d", $strtotime));
					 	if(is_array($toGregorian))
					 	{
							$result[$myKey. '_gregorian'] = implode('-', $toGregorian);
					 	}
					}
					else
					{
						$result[$myKey] = null;
					}
					break;

				case 'showavatar':
					$result['show_avatar'] = $value ? true : false;
					break;
				case 'allowplus':
					$result['allow_plus'] = $value ? true : false;
					break;

				case 'quick':
					$result['quick_traffic'] = $value ? true : false;
					break;

				case 'allowminus':
					$result['allow_minus'] = $value ? true : false;
					break;
				case 'remote':
					$result['remote_user'] = $value ? true : false;
					break;
				case '24h':
					$result['24h'] = $value ? true : false;
					break;

				case 'logourl':
					if($value)
					{
						$result['logo'] = \lib\app\option::host('file'). '/'. $value;
					}
					else
					{
						$result['logo'] = \lib\app\option::host('siftal_image');
					}

					break;

				case 'logo':
					// continue;
					if(isset(self::$logo_urls[$value]))
					{
						$result['logo'] = \lib\app\option::host('file'). '/'. self::$logo_urls[$value];
					}
					else
					{
						$result['logo'] = null;
					}
					break;
				case 'cardsize':
					$result['cardsize'] = $value ? intval($value) : null;
					break;

				case 'allowdescenter':
					$result['allow_desc_enter'] = $value ? true : false;
					break;

				case 'allowdescexit':
					$result['allow_desc_exit'] = $value ? true : false;
					break;

				case 'createdate':
					$result[$key] = $value;
					$date_now = new \DateTime("now");
					$start    = new \DateTime($value);
					$result['day_use']    = $date_now->diff($start)->days;
					$result['day_use']++;
					break;
				case 'telegram_id':
					$result['telegram'] = $value ? true : false;
					break;

				case 'plan':
					$result[$key] = $value;
					break;

				case 'fileid':
				case 'until':
				case 'date_modified':
				case 'meta':
				case 'value':
				default:

					break;
			}
		}

		if(isset($result['event_date_start_gregorian']) && isset($result['event_date_gregorian']))
		{
			$result['event']         = [];

			$now                     = $result['event']['now'] = date("Y-m-d");
			$start                   = $result['event']['start'] = $result['event_date_start_gregorian'];
			$end                     = $result['event']['end'] = $result['event_date_gregorian'];

			$date_now                = new \DateTime("now");
			$date_start              = new \DateTime($end);
			$date_end                = new \DateTime($start);

			$left                    = $date_end->diff($date_now)->days;
			$remain                  = $date_start->diff($date_now)->days;
			$diff                    = $date_start->diff($date_end)->days;

			$result['event']['days'] = $diff;

			$myDivision = $diff;
			if(intval($diff) <= 0)
			{
				$myDivision = 1;
			}

			$result['event']['left']  = $left;

			if($date_now < $date_start)
			{
				$result['event']['remain'] = $remain;
				$result['event']['remain_percent'] = round((intval($remain) * 100) / $myDivision);
				$result['event']['left_percent']   = round((intval($left) * 100) / $myDivision);
			}
			else
			{
				$result['event']['remain']         = 0;
				$result['event']['remain_percent'] = 0;
				$result['event']['left_percent']   = 100;
			}

		}
		// var_dump($result);exit();
		return $result;
	}


	/**
	 * Loads file records.
	 *
	 * @param      <type>  $_result  The result
	 */
	public static function load_file_records($_result)
	{
		if(is_array($_result))
		{
			$logos = array_column($_result, 'logo');
			$logos = array_filter($logos);
			$logos = array_unique($logos);
			if(!empty($logos))
			{
				$get_post_record = \dash\db\posts::get_some_id($logos);
				if(!empty($get_post_record))
				{
					$id              = array_column($get_post_record, 'id');
					$meta            = array_column($get_post_record, 'meta');
					$url             = array_column($meta, 'url');
					self::$logo_urls = array_combine($id, $url);
				}
			}
		}
	}

	/**
	 * Gets the team.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The team.
	 */
	public static function get_list_team($_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}

		$meta = [];

		$type = \dash\app::request('type');
		if($type && !is_string($type))
		{
			\dash\db\logs::set('api:team:get:list:type:invalid', \dash\user::id());
			\dash\notif::error(T_("Invalid team type"), 'type', 'arguments');
			return false;
		}

		$meta['type'] = $type;

		$result = \lib\db\teams::team_list(\dash\user::id(), $meta);
		$temp = [];
		foreach ($result as $key => $value)
		{
			$check = self::ready_team($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}


	/**
	 * Gets the team.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The team.
	 */
	public static function get_list_team_child($_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}
	}

	/**
	 * Gets the team.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The team.
	 */
	public static function get_team($_options = [])
	{
		$default_options =
		[
			'debug' => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if($_options['debug'])
		{
			// \dash\notif::title(T_("Operation Faild"));
		}

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
			// return false;
		}

		$id = \dash\app::request("id");
		$id = \dash\coding::decode($id);

		$shortname = \dash\app::request('shortname');

		if(!$id && !$shortname)
		{
			if($_options['debug'])
			{
				\dash\db\logs::set('api:team:id:shortname:not:set', \dash\user::id(), $log_meta);
				\dash\notif::error(T_("Team id or shortname not set"), 'id', 'arguments');
			}
			return false;
		}

		if($id && $shortname)
		{
			\dash\db\logs::set('api:team:id:shortname:together:set', \dash\user::id(), $log_meta);
			if($_options['debug'])
			{
				\dash\notif::error(T_("Can not set team id and shortname together"), 'id', 'arguments');
			}
			return false;
		}

		if($id)
		{
			$result = \lib\db\teams::access_team_id($id, \dash\user::id(), ['action' => 'view']);
		}
		else
		{
			$result = \lib\db\teams::access_team($shortname, \dash\user::id(), ['action' => 'view']);
		}

		if(!$result)
		{
			if($id)
			{
				$result = \lib\db\teams::get(['id' => $id, 'limit' => 1]);
			}
			elseif($shortname)
			{
				$result = \lib\db\teams::get(['shortname' => $shortname, 'limit' => 1]);
			}

			if($result)
			{
				if(\dash\permission::check('load:all:team', \dash\user::id()))
				{
					$result = $result;
				}
				else
				{
					\dash\temp::set('team_access_denied', true);
					\dash\temp::set('team_exist', true);
					$result = false;
				}
			}
		}

		if(!$result)
		{
			\dash\db\logs::set('api:team:access:denide', \dash\user::id(), $log_meta);
			if($_options['debug'])
			{
				\dash\notif::error(T_("Can not access to load this team details"), 'team', 'permission');
			}
			return false;
		}

		if($_options['debug'])
		{
			// \dash\notif::title(T_("Operation complete"));
		}

		$result = self::ready_team($result);

		return $result;
	}

}
?>
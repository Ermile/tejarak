<?php
namespace content_cp\teamplans;

class view
{
	public static function config()
	{
		$list = self::teamplans_list();
		\dash\data::teamplansList($list);
	}

	public static function teamplans_list()
	{
		$meta   = [];

		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}


		if(empty($meta))
		{
			$meta['teamplans.status'] = 'enable';
			$meta['sort']             = 'teamplans.lastcalcdate';
			$meta['order']            = 'desc';
		}

		$meta['admin'] = true;


		$result = \lib\db\teamplans::search($search, $meta);
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				if(isset($value['plan']))
				{
					$result[$key]['plan'] = \lib\utility\plan::plan_name($value['plan']);
				}
				if(isset($value['lastcalcdate']))
				{
					$renew_time = strtotime("+30 day") - strtotime($value['lastcalcdate']);
					$renew_time = date("d", $renew_time). ' '. T_("Day"). ' '. T_("And"). ' '. date("H", $renew_time). " ". T_("Hour");
					$result[$key]['renew_time'] = $renew_time;
				}
			}
		}

		return $result;
	}
}
?>
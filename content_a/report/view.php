<?php
namespace content_a\report;


class view
{
	public static function config()
	{
		\dash\data::month(\dash\date::month_precent());
		/**
		* get raw time
		*/
		if(\dash\request::get('time') === 'raw')
		{
			\dash\data::timeRaw(true);
		}
		else
		{
			\dash\data::timeRaw(false);
		}

		if(\dash\request::get())
		{
			if(!\dash\request::get('export'))
			{
				\dash\data::exportUrl(\dash\url::pwd(). '&export=true');
			}
			else
			{
				\dash\data::exportUrl(\dash\url::pwd());
			}
		}
		else
		{
			\dash\data::exportUrl(\dash\url::pwd(). '?export=true');
		}

		if(\dash\request::get('year') && is_numeric(\dash\request::get('year')) && mb_strlen(\dash\request::get('year')) === 4)
		{
			\dash\data::getYear(\dash\request::get('year'));
		}


		\dash\data::page_title(T_('Reports'));
		\dash\data::page_desc(T_('You can see reports and compare data of members and give export from report to use in another programs.'));

		$all_user = [];

		if($teamCode = \dash\temp::get('team_code_url'))
		{
			\dash\data::reportUrl(\dash\url::here(). '/'. \dash\url::directory());
		}

		if(\dash\request::get('user') && isset($all_user[\dash\request::get('user')]))
		{
			\dash\data::reportCurrent_user($all_user[\dash\request::get('user')]);
		}

		$team_id = \dash\coding::decode(\dash\request::get('id'));

		$cache_chart = \dash\session::get('report_last_month_chart_'. $team_id);
		if($cache_chart === null)
		{

			if(\dash\data::isAdmin())
			{
				$user_id = null;
			}
			else
			{
				$user_id = \dash\user::id();
			}

			$chartLastMonth = \lib\db\teams::last_time_chart($team_id, $user_id, 30);
			$cache_chart = [];

			if(is_array($chartLastMonth))
			{
				foreach ($chartLastMonth as $key => $value)
				{
					$format = "Y-m-d D";
					$date = date($format, strtotime($key));
					if(\dash\language::current() === 'fa')
					{
						$date = \dash\utility\jdate::date($format, strtotime($key));
					}

					$cache_chart[] = ['key' => $date, 'value' => floatval($value)];
				}
			}
			$hi_chart = [];
			$hi_chart['categories'] = json_encode(array_column($cache_chart, 'key'), JSON_UNESCAPED_UNICODE);
			$hi_chart['value'] = json_encode(array_column($cache_chart, 'value'), JSON_UNESCAPED_UNICODE);
			\dash\data::hiChart($hi_chart);

			$cache_chart = json_encode($cache_chart, JSON_UNESCAPED_UNICODE);
			\dash\session::set('report_last_month_chart_'. $team_id, $cache_chart, null, 60);
		}
		\dash\data::chartLastMonth($cache_chart);

		self::showAllUser();

	}



	public static function showAllUser()
	{
		$team_id = \dash\coding::decode(\dash\request::get('id'));
		if($team_id)
		{
			// check admin or user of team
			$user_status = \lib\db\userteams::get(
			[
				'user_id' => \dash\user::id(),
				'team_id' => $team_id,
				'limit'   => 1
			]);

			if(isset($user_status['rule']) && $user_status['rule'] === 'admin')
			{
				// this user is admin
				// set true on showAllUser
				\dash\data::showAllUser(true);
				// load all user data to show
				$all_user = \lib\app\team::listMember($team_id);

				$all_user_id = array_column($all_user, 'user_id');
				$all_user    = array_combine($all_user_id, $all_user);
				\dash\data::allUserList($all_user);
			}
			else
			{
				\dash\data::showAllUser(false);
				\dash\data::allUserList([]);
				// this user is user
			}
		}
	}
}
?>
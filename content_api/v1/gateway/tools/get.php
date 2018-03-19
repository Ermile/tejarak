<?php
namespace content_api\v1\gateway\tools;


trait get
{
	public $remote_user         = false;
	public $rule                = null;
	public $show_another_status = false;
	public $team_privacy        = 'private';

	/**
	 * Gets the gateway.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The gateway.
	 */
	public function get_list_gateway($_args = [])
	{
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\utility::request(),
			]
		];
		if(!$this->user_id)
		{
			// return false;
		}

		$id = \lib\utility::request('id');
		$id = \lib\coding::decode($id);

		$shortname =  \lib\utility::request('shortname');

		if(!$id && !$shortname)
		{
			\lib\db\logs::set('api:gateway:team:id:not:set', $this->user_id, $log_meta);
			\lib\notif::error(T_("Id or shortname not set"), 'id', 'arguments');
			return false;
		}
		elseif($id && $shortname)
		{
			\lib\db\logs::set('api:gateway:team:id:and:shortname:together:set', $this->user_id, $log_meta);
			\lib\notif::error(T_("Can not set id and shortname together"), 'id', 'arguments');
			return false;
		}

		if($id)
		{
			$team_detail = \lib\db\teams::access_team_id($id, $this->user_id, ['action' => 'get_gateway']);
		}
		elseif($shortname)
		{
			$team_detail = \lib\db\teams::access_team($shortname, $this->user_id, ['action' => 'get_gateway']);
		}

		if(!$team_detail)
		{
			\lib\db\logs::set('api:gateway:team:id:permission:denide', $this->user_id, $log_meta);
			\lib\notif::error(T_("Can not access to load this team"), 'id', 'permission');
			return false;
		}

		if(isset($team_detail['id']))
		{
			$where            = [];
			$where['team_id'] = $team_detail['id'];
			$where['rule']    = 'gateway';
			$where['status']  = ['IN', "('active', 'deactive')"];
			$result           = \lib\db\userteams::get_list($where);

			$temp             = [];
			if(is_array($result))
			{
				foreach ($result as $key => $value)
				{
					$a = $this->ready_gateway($value, ['condition_checked' => true]);
					if($a)
					{
						$temp[] = $a;
					}
				}
			}
			return $temp;
		}
	}


	/**
	 * Gets the gateway.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The gateway.
	 */
	public function get_gateway($_args = [])
	{
		\lib\notif::title(T_("Operation Faild"));
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\utility::request(),
			]
		];
		if(!$this->user_id)
		{
			\lib\db\logs::set('api:gateway:user_id:notfound', $this->user_id, $log_meta);
			\lib\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$team = \lib\utility::request('team');
		if(!$team)
		{
			\lib\db\logs::set('api:gateway:team:not:set', $this->user_id, $log_meta);
			\lib\notif::error(T_("Team not set"), 'team', 'arguments');
			return false;
		}

		$id = \lib\utility::request('id');
		$id = \lib\coding::decode($id);
		if(!$id)
		{
			\lib\db\logs::set('api:gateway:id:not:set', $this->user_id, $log_meta);
			\lib\notif::error(T_("Id not set"), 'id', 'arguments');
			return false;
		}

		$team_detail = \lib\db\teams::access_team_code($team, $this->user_id, ['action' => 'edit_gateway']);

		if(!isset($team_detail['id']))
		{
			return false;
		}

		$team_id = $team_detail['id'];

		$check_user_in_team = \lib\db\userteams::get_list(['user_id' => $id, 'team_id' => $team_id, 'rule' => 'gateway', 'limit' => 1]);

		if(!$check_user_in_team)
		{
			\lib\db\logs::set('api:gateway:user:not:in:team', $this->user_id, $log_meta);
			\lib\notif::error(T_("This user is not in this team"), 'id', 'arguments');
			return false;
		}

		$result = $this->ready_gateway($check_user_in_team, ['condition_checked' => true]);

		return $result;
	}


	/**
	 * ready data of gateway to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_gateway($_data, $_options = [])
	{
		$default_options =
		[
			'condition_checked' => false,
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
				case 'user_id':
					$result['user_id'] = \lib\coding::encode($value);
					break;

				case 'displayname':
					$result['name'] = $value;
					break;

				case 'username':
					$split = explode('-', $value);
					if(isset($split[1]))
					{
						$result['username'] = $split[1];

					}
					break;
				case 'status':
					$result[$key] = isset($value) ? (string) $value : null;
					break;


				default:
					continue;
					break;
			}
		}
		krsort($result);
		return $result;
	}
}
?>
<?php
namespace content_api\v1\staff\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;

trait get
{
	/**
	 * Gets the staff.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The staff.
	 */
	public function get_list_staff($_args = [])
	{
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => utility::request(),
			]
		];

		if(!$this->user_id)
		{
			return false;
		}

		if(!utility::request('company'))
		{
			logs::set('api:staff:comany:brand:notfound', null, $log_meta);
			debug::error(T_("Comany not found"), 'comany', 'permission');
			return false;
		}

		$company_id = \lib\db\companies::get_brand(utility::request('company'));

		if(isset($company_id['id']))
		{
			$where               = [];
			$where['company_id'] = $company_id['id'];
			$result               = \lib\db\usercompanies::get($where);
			$temp = [];
			if(is_array($result))
			{
				foreach ($result as $key => $value)
				{
					$temp[] = $this->ready_staff($value);
				}
			}

			return $temp;
		}
	}


	/**
	 * Gets the staff.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The staff.
	 */
	public function get_staff($_args = [])
	{
		debug::title(T_("Operation Faild"));
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => utility::request(),
			]
		];

		if(!$this->user_id)
		{
			logs::set('api:staff:user_id:notfound', null, $log_meta);
			debug::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$company = utility::request("company");
		$staff  = utility::request("staff");

		if(!$company)
		{
			logs::set('api:staff:comany:notfound', $this->user_id, $log_meta);
			debug::error(T_("Invalid comany"), 'company', 'permission');
			return false;
		}

		if(!$staff)
		{
			logs::set('api:staff:staff:notfound', $this->user_id, $log_meta);
			debug::error(T_("Invalid staff"), 'staff', 'permission');
			return false;
		}

		// $result = \lib\db\staffs::get_by_brand($company, $staff);
		// return $result;
	}

	public function usercompany_get_details()
	{
		$usercompany_id  = utility::request("id");

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => utility::request(),
			]
		];

		if(!$this->user_id)
		{
			logs::set('api:staff:user_id:not:set:usercompany_get_details', null, $log_meta);
			debug::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		if(!$usercompany_id)
		{
			logs::set('api:usercompany_id:comany:not:set', $this->user_id, $log_meta);
			debug::error(T_("Invalid comany id"), 'company', 'permission');
			return false;
		}

		$load_detail  = \lib\db\usercompanies::get_by_id($usercompany_id, $this->user_id);

		return $load_detail;
	}


	/**
	 * ready data of staff to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_staff($_data, $_options = [])
	{
		$default_options =
		[

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

					$result[$key] = (int) $value;
					// if(isset($_data['code']) && $_data['code'])
					// {
					// 	continue;
					// }
					// else
					// {
					// 	$result['code'] = \lib\utility\shortURL::encode($value);
					// }
					break;

				case 'code':
					if($value)
					{
						$result[$key] = (int) $value;
					}
					else
					{
						if(isset($_data['id']))
						{
							$result[$key] = (int) $_data['id'];
						}
					}

					// if($value)
					// {
					// 	$result['code'] = (int) $value;
					// }
					// else
					// {
					// 	continue;
					// }
					break;

				case 'company_id':
				case 'user_id':
				case 'telegram_id':
					$result[$key] = (int) $value;
					break;

				case 'email':
				case 'displayname':
				case 'status':
				case 'postion':
				case 'desc':
				case 'mobile':
					$result[$key] = (string) $value;
					break;

				case 'date_enter':
				case 'date_exit':
					$result[$key] = strtotime($value);
					break;

				case 'remote':
				case 'is_default':
				case 'full_time':
					if($value)
					{
						$result[$key] = true;
					}
					else
					{
						$result[$key] = false;
					}
					break;

				case 'createdate':
				case 'date_modified':
				case 'meta':
				default:
					continue;
					break;
			}
		}
		return $result;
	}
}
?>
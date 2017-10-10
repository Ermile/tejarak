<?php
namespace content_api\v1\member\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;

trait add
{
	private $master_user_id         = null;
	private $OLD_USER_ID            = null;
	private $old_user_id            = null;
	private $NEW_USER_ID            = null;
	private $userteam_record_detail = [];
	use check_args;
	use member_id;
	use security;

	/**
	 * Adds a member.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_member($_args = [])
	{

		// ready to insert userteam or userbranch record
		$args = [];

		// default args
		$default_args =
		[
			'method'             => 'post',
			'have_user_id'       => null,
			'update_all_user_id' => false,
			'debug'              => true,
			'save_log'           => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		// merge default args and args
		$_args = array_merge($default_args, $_args);

		// set default title of debug
		if($_args['debug']) debug::title(T_("Operation Faild"));

		// delete member mode
		$delete_mode = false;

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => utility::request(),
			]
		];

		// check user id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) logs::set('api:member:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("User not found"), 'user', 'permission');
			return false;
		}


		// get team and check it
		$team_id = utility::request('team');
		$team_id = utility\shortURL::decode($team_id);
		if(!$team_id)
		{
			if($_args['save_log']) logs::set('api:member:team:not:set', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Team not set"), 'team', 'arguments');
			return false;
		}
		$check_security = $this->check_security($team_id, $_args, $args, $log_meta);

		if($check_security === false || !debug::$status)
		{
			return false;
		}

		$res = $this->find_member_id($_args, $log_meta, $team_id);

		if(!debug::$status || $res === false)
		{
			return false;
		}

		/**
		 * check and set the args
		 */
		$return_function = $this->check_args($_args, $args, $log_meta, $team_id);

		if(!debug::$status || $return_function === false)
		{
			return false;
		}

		if($_args['method'] === 'post')
		{
			$args['team_id']       = $team_id;
		}

		if($this->master_user_id)
		{
			$args['user_id']       = $this->master_user_id;
		}

		// check file is set or no
		// if file is not set and the user have a file load the default file
		if($this->master_user_id && $_args['method'] === 'post' && ((!$args['fileid'] && !$args['fileurl']) || (!$args['firstname'] || !$args['lastname'])))
		{
			$user_detail = \lib\db\users::get(['id' => $args['user_id'], 'limit' => 1]);
			if(isset($user_detail['fileid']))
			{
				$args['fileid'] = $user_detail['fileid'];
			}

			if(isset($user_detail['fileurl']))
			{
				$args['fileurl'] = $user_detail['fileurl'];
			}

			if(isset($user_detail['name']) && !$args['firstname'])
			{
				$args['firstname'] = $user_detail['name'];
			}

			if(isset($user_detail['lastname']) && !$args['lastname'])
			{
				$args['lastname'] = $user_detail['lastname'];
			}
		}



		$return = [];

		// insert new user team
		if($_args['method'] === 'post')
		{
			$user_team_id          = \lib\db\userteams::insert($args);
			$return['userteam_id'] = utility\shortURL::encode($user_team_id);
			$return['user_id']     = utility\shortURL::encode($this->master_user_id);
		}
		elseif($_args['method'] === 'patch')
		{
			$id = utility::request('id');
			$id = utility\shortURL::decode($id);
			if(!$id)
			{
				if($_args['save_log']) logs::set('api:member:pathc:id:not:set', $this->user_id, $log_meta);
				if($_args['debug']) debug::error(T_("Id not set"), 'id', 'arguments');
				return false;
			}

			if($this->OLD_USER_ID && $this->NEW_USER_ID && intval($this->OLD_USER_ID) !== intval($this->NEW_USER_ID))
			{
				// old user id and new user id is set but different
				// we must update all user id in main parent of this team
				\lib\db\userteams::update_all_user_id($this->OLD_USER_ID, $this->NEW_USER_ID, $team_id, $log_meta);
				if(!debug::$status)
				{
					return false;
				}
			}

			unset($args['team_id']);
			if(!utility::isset_request('postion'))               unset($args['postion']);
			if(!utility::isset_request('personnel_code'))        unset($args['personnelcode']);
			if(!utility::isset_request('24h'))                   unset($args['24h']);
			if(!utility::isset_request('remote_user'))           unset($args['remote']);
			if(!utility::isset_request('is_default'))            unset($args['isdefault']);
			if(!utility::isset_request('date_enter'))            unset($args['dateenter']);
			if(!utility::isset_request('date_exit'))             unset($args['dateexit']);
			if(!utility::isset_request('firstname'))             unset($args['firstname']);
			if(!utility::isset_request('lastname'))              unset($args['lastname']);
			if(!utility::isset_request('file'))                  unset($args['fileid'], $args['fileurl']);
			if(!utility::isset_request('allow_plus'))            unset($args['allowplus']);
			if(!utility::isset_request('allow_minus'))           unset($args['allowminus']);
			if(!utility::isset_request('status'))                unset($args['status']);
			if(!utility::isset_request('displayname'))           unset($args['displayname']);
			if(!utility::isset_request('rule'))                  unset($args['rule']);
			if(!utility::isset_request('visibility'))            unset($args['visibility']);
			if(!utility::isset_request('allow_desc_enter'))      unset($args['allowdescenter']);
			if(!utility::isset_request('allow_desc_exit'))       unset($args['allowdescexit']);

			if(!utility::isset_request('national_code'))         unset($args['nationalcode']);
			if(!utility::isset_request('father'))                unset($args['father']);
			if(!utility::isset_request('birthday'))              unset($args['birthday']);
			if(!utility::isset_request('gender'))                unset($args['gender']);
			if(!utility::isset_request('type'))                  unset($args['type']);
			if(!utility::isset_request('marital'))               unset($args['marital']);
			if(!utility::isset_request('child'))                 unset($args['childcount']);
			if(!utility::isset_request('birthcity'))             unset($args['birthplace']);
			if(!utility::isset_request('shfrom'))                unset($args['from']);
			if(!utility::isset_request('shcode'))                unset($args['shcode']);
			if(!utility::isset_request('education'))             unset($args['education']);
			if(!utility::isset_request('job'))                   unset($args['job']);
			if(!utility::isset_request('passport_code'))         unset($args['pasportcode']);

			if(!utility::isset_request('payment_account_number'))unset($args['cardnumber']);
			if(!utility::isset_request('shaba'))                 unset($args['shaba']);

			if(array_key_exists('rule', $args) && !in_array($args['rule'], ['user', 'admin', 'gateway']))
			{
				if($_args['save_log']) logs::set('api:member:rule:invalid:edit', $this->user_id, $log_meta);
				if($_args['debug']) debug::error(T_("Invalid parameter rule"), 'rule', 'arguments');
				return false;
			}

			if(array_key_exists('status', $args) && !in_array($args['status'], ['active', 'deactive', 'suspended']))
			{
				if($_args['save_log']) logs::set('api:member:status:invalid:edit', $this->user_id, $log_meta);
				if($_args['debug']) debug::error(T_("Invalid parameter status"), 'status', 'arguments');
				return false;
			}

			if(array_key_exists('visibility', $args) && !in_array($args['visibility'], ['visible', 'hidden']))
			{
				if($_args['save_log']) logs::set('api:member:visibility:invalid', $this->user_id, $log_meta);
				if($_args['debug']) debug::error(T_("Invalid parameter visibility"), 'visibility', 'arguments');
				return false;
			}
			// check barcode, qrcode and rfid,
			// update it if changed
			// get from utility::request()
			// check from $args
			$this->check_barcode($id);
			// if have error in checking barcode
			if(!debug::$status)
			{
				return;
			}

			if(!empty($args))
			{
				\lib\db\userteams::update($args, $id);
			}

			if(debug::$status)
			{
				$return = true;
			}
		}
		elseif ($_args['method'] === 'delete')
		{
			// \lib\db\members::remove($args);
		}

		if(debug::$status)
		{
			if($_args['debug']) debug::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) debug::true(T_("Member successfully added"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) debug::true(T_("Member successfully updated"));
			}
			else
			{
				if($_args['debug']) debug::true(T_("Member successfully removed"));
			}
		}

		return $return;
	}
}
?>
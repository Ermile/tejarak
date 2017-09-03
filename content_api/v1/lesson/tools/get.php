<?php
namespace content_api\v1\lesson\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;

trait get
{
	public $logo_urls = [];

	/**
	 * ready data of team to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public function ready_lesson($_data, $_options = [])
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
					$result[$key] = utility\shortURL::encode($value);
					break;
				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}



	/**
	 * Gets the team.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The team.
	 */
	public function get_list_lesson($_args = [])
	{
		if(!$this->user_id)
		{
			return false;
		}

		$meta = [];


		$school_id = utility::request('school');
		$school_id = \lib\utility\shortURL::decode($school_id);
		if(!$school_id || !is_numeric($school_id))
		{

			logs::set('api:lesson:get:list:school_id:invalid', $this->user_id);
			debug::error(T_("Invalid school id"), 'school', 'arguments');
			return false;
		}
		$meta['school_id'] = $school_id;

		$search = utility::request('search');
		if($search &&  !is_string($search))
		{
			logs::set('api:lesson:get:list:school_id:invalid', $this->user_id);
			debug::error(T_("Invalid school id"), 'school', 'arguments');
			return false;
		}

		$search = trim($search);

		$result = \lib\db\lessons::search($search, $meta);

		$temp = [];
		foreach ($result as $key => $value)
		{
			$check = $this->ready_lesson($value);
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
	public function get_lesson($_options = [])
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
			debug::title(T_("Operation Faild"));
		}

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

		$id = utility::request("id");

		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			if($_options['debug'])
			{
				logs::set('api:lesson:id:shortname:not:set', $this->user_id, $log_meta);
				debug::error(T_("Team id or shortname not set"), 'id', 'arguments');
			}
			return false;
		}

		$result = \lib\db\lessons::get(['id' => $id, 'limit' => 1]);

		if(!$result)
		{
			logs::set('api:lesson:access:denide', $this->user_id, $log_meta);
			if($_options['debug'])
			{
				debug::error(T_("Can not access to load this lesson details"), 'lesson', 'permission');
			}
			return false;
		}

		if($_options['debug'])
		{
			debug::title(T_("Operation complete"));
		}

		$result = $this->ready_lesson($result);

		return $result;
	}


	/**
	 * get the places title
	 * get the subject title
	 * get the teacher name
	 * get the term title
	 * to insert new lesson
	 *
	 * @return     boolean  To insert.
	 */
	public function get_to_insert()
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
			debug::title(T_("Operation Faild"));
		}

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

		$school_id = utility::request("school");

		$school_id = \lib\utility\shortURL::decode($school_id);

		if(!$school_id)
		{
			if($_options['debug'])
			{
				logs::set('api:lesson:school_id:shortname:not:set', $this->user_id, $log_meta);
				debug::error(T_("Team school_id or shortname not set"), 'school_id', 'arguments');
			}
			return false;
		}

		$result              = [];
		$result['classroom'] = $classroom = [];
		$result['terms']     = $terms = [];
		$result['teacher']   = $teacher = [];
		$result['subject']   = $subject = [];



		if($_options['debug'])
		{
			debug::title(T_("Operation complete"));
		}

		$result = $this->ready_lesson($result);

		return $result;
	}
}
?>
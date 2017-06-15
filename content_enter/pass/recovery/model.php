<?php
namespace content_enter\pass\recovery;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \content_enter\pass\model
{
	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_pass($_args)
	{

	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_pass($_args)
	{
		// check inup is ok
		if(!self::check_input('pass/recovery'))
		{
			debug::error(T_("Dont!"));
			return false;
		}

		if(utility::post('ramzNew'))
		{
			$temp_ramz = utility::post('ramzNew');
			// check min and max of password
			// if not okay make debug error and return false
			if(!$this->check_pass_syntax($temp_ramz))
			{
				return false;
			}

			// hesh ramz to find is this ramz is easy or no
			// creazy password !
			$temp_ramz_hash = \lib\utility::hasher($temp_ramz);
			// if debug status continue
			if(debug::$status)
			{
				self::set_enter_session('temp_ramz', $temp_ramz);
				self::set_enter_session('temp_ramz_hash', $temp_ramz_hash);
			}
			else
			{
				// creazy password
				return false;
			}
		}
		else
		{
			// plus count invalid password
			self::plus_try_session('no_password_send_verify');

			debug::error(T_("Invalid Password"));
			return false;
		}

		// set session verify_from recovery
		self::set_enter_session('verify_from', 'recovery');
		// find send way to send code
		// and send code
		// set step pass is done
		self::set_step_session('pass', true);

		// find send way to send code
		$way = self::send_way();
		if(!$way)
		{
			self::next_step('verify/what');
			// no way to send code
			self::go_to('verify/what');
		}
		else
		{
			// just this module can route after this
			self::next_step('verify/'. $way);
			// go to verify page
			self::go_to('verify/'. $way);
		}
	}
}
?>
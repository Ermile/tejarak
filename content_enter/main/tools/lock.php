<?php
namespace content_enter\main\tools;
use \lib\utility;
use \lib\debug;

trait lock
{

	/**
	 * set redirect url
	 *
	 * @param      <type>  $_url   The url
	 */
	public static function done_step($_module)
	{
		switch ($_module)
		{
			default:
				if(self::get_step_session($_module))
				{
					return true;
				}

				break;
		}
		return false;
	}


	/**
	 * lock or unlock step
	 * and check is lock or not lock
	 *
	 * @param      <type>  $_module  The module
	 * @param      <type>  $_acction     The set
	 */
	public static function lock($_module, $_acction = null)
	{
		if($_acction === true)
		{
			self::set_session('lock', $_module, true);
		}

		if($_acction === false)
		{
			self::set_session('lock', $_module, false);
		}

		if($_acction === null)
		{
			// in dev and when we in debug mode
			// we have not any page to lock!
			if(Tld === 'dev' && self::$debug_mode)
			{
				return false;
			}
			// get lock from session
			$is_lock = self::get_session('lock', $_module);
			// if is lock or not set
			if($is_lock === true || $is_lock === null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}


	/**
	 * Opens a lock.
	 *
	 * @param      <type>  $_module  The module
	 */
	public static function open_lock($_module)
	{
		self::lock($_module, false);
	}


	/**
	 * disable all lock page and set only this module is true
	 *
	 * @param      <type>  $_module  The module
	 */
	public static function next_step($_module)
	{
		// unset all other lock
		unset($_SESSION['lock']);
		// set jusn this lock
		self::set_session('lock', $_module, false);
	}
}
?>
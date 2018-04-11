<?php
namespace lib\app;


class option
{

	/**
	 * make host string
	 *
	 * @param      string  $_type  The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function host($_type = null)
	{

		$host = \dash\url::site();

		if(defined('simulation_com') && simulation_com)
		{
			$host = simulation_com;
		}

		switch ($_type)
		{
			case 'file':
				return $host;
				break;

			case 'without_language':
				return $host;
				break;

			case 'with_language':
				return $host;
				break;

			case 'static_logo':
				return $host . '/static/images/logo.png';
				break;

			case 'siftal_image':
				return $host . '/static/siftal/images/tools/image.png';
				break;

			case 'siftal_user':
				return $host . '/static/siftal/images/useful/user1.png';
				break;

			default:
				return $host;
				break;
		}
	}


	/**
	 * check permission
	 *
	 * @param      <type>  $_content     The content
	 * @param      <type>  $_permission  The permission
	 * @param      <type>  $_actions     The actions
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function check_api_permission($_caller, $_action = null, $_user_id = null)
	{
		\dash\permission::$user_id = $_user_id;
		return \dash\permission::access(...func_get_args());
	}
}
?>
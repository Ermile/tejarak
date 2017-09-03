<?php
namespace content_s\report\month;
use \lib\debug;
use \lib\utility;

class model extends \content_s\main\model
{

	/**
	 * Gets the month time.
	 *
	 * @param      <type>  $_request  The arguments
	 */
	public function get_month_time($_request)
	{
		$this->user_id = $this->login('id');
		utility::set_request_array($_request);
		return $this->report_month_time();
	}
}
?>
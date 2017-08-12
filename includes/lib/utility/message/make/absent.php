<?php
namespace lib\utility\message\make;
use \lib\utility;
use \lib\utility\human;
use \lib\debug;
use \lib\db;


trait absent
{

	public function absent()
	{
		$result = \lib\db\teams::get_deactive_member($this->team_id);
		$msg = null;
		if($result && is_array($result))
		{
			foreach ($result as $key => $value)
			{
				if(isset($value['displayname']))
				{
					$msg .= "\n".  $value['displayname'];
				}
			}
		}
		return $msg;
	}

}
?>
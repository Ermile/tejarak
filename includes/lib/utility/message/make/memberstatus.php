<?php
namespace lib\utility\message\make;


trait memberstatus
{

	public function memberstatus()
	{
		$result = \lib\db\teams::get_member($this->team_id);

		$msg = null;
		if($result && is_array($result))
		{
			foreach ($result as $key => $value)
			{
				if(isset($value['displayname']) && isset($value['status']))
				{
					$msg .= "\n".  $value['displayname']. " ". T_($value['status']);
				}
			}
		}
		return $msg;
	}
}
?>
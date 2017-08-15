<?php
namespace content_cp\teams\detail;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function get_load($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$result = [];
		if($id && is_numeric($id))
		{
			$result = \lib\db\teams::get(['id' => $id, 'limit' => 1]);
		}
		return $result;
	}
}
?>

<?php
namespace content_cp\teams\detail;

class view extends \mvc\view
{
	public function view_detail($_args)
	{
		if(isset($_args->api_callback))
		{
			$data = $_args->api_callback;
			if(isset($data['user_id']))
			{
				$this->data->get_mobile = \lib\db\teams::get_mobile($data['user_id']);
			}
			$this->data->user_record = $data;
		}
	}
}
?>
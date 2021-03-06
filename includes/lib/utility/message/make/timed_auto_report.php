<?php
namespace lib\utility\message\make;


trait timed_auto_report
{
	public function timed_auto_report()
	{

		$tik_emoji    = "▪";
		$zzz_emoji    = "▫";

		$date_now     = date("Y-m-d");
		$result_query = \lib\db\teams::timed_auto_report_query($this->team_id);

		$msg          = [];
		foreach ($result_query as $key => $value)
		{
			if(isset($value['userteam_id']))
			{
				if(!isset($msg[$value['userteam_id']]))
				{
					$msg[$value['userteam_id']] = [];
					$msg[$value['userteam_id']]['emoji'] = [];
				}

				if(!array_key_exists('start_time', $value))
				{
					continue;
				}

				if(!array_key_exists('end_time', $value))
				{
					continue;
				}

				if(array_key_exists('displayname', $value))
				{
					$msg[$value['userteam_id']]['displayname'] = $value['displayname'];
				}

				if(!$value['end_time'])
				{
					array_push($msg[$value['userteam_id']]['emoji'], $tik_emoji);
				}
				else
				{
					if(time() - strtotime($value['start_time']) <= (24 * 60 * 60))
					{
						array_push($msg[$value['userteam_id']]['emoji'], $tik_emoji);
						array_push($msg[$value['userteam_id']]['emoji'], $zzz_emoji);
					}
					else
					{
						array_push($msg[$value['userteam_id']]['emoji'], $zzz_emoji);
					}
				}
			}
		}

		$msg_string = "";

		if(!empty($msg))
		{
			foreach ($msg as $key => $value)
			{
				$msg_string .= $value['displayname']. " ". implode("", $value['emoji']). "\n";
			}
		}

		if($msg_string)
		{
			$msg_string = \dash\date::fit_lang('l j F Y', time() , 'current') . "\n\n" . $msg_string;
			$msg_string = $this->report_header . "\n". $msg_string. "\n". $this->report_footer;
		}

		return $msg_string;
	}
}
?>
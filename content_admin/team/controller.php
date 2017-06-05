<?php
namespace content_admin\team;

class controller extends \content_admin\main\controller
{
	/**
	 * route
	 */
	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		// add team
		$this->get(false, 'add')->ALL('team');
		$this->post('add')->ALL('team');


		// route url like this admin/ermile/branch=sarshomar/member
		// or like this        admin/ermile/branch=sarshomar/member=123
		if(preg_match("/^team\/([a-zA-Z0-9]+)\/branch\=([a-zA-Z0-9]+)\/member(|\=([a-zA-Z0-9]+))$/", $url))
		{
			\lib\router::set_controller('content_admin\member\controller');
			return;
		}

		// the url like this admin/ermile/branch
		if(preg_match("/^team\/([a-zA-Z0-9]+)\/branch(|\=([a-zA-Z0-9]+))$/", $url))
		{
			\lib\router::set_controller('content_admin\branch\controller');
			return;
		}

		// the url like this admin/ermile/sarshomar
		// load the member list
		if(preg_match("/^team\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)$/", $url, $split))
		{
			$check_route = false;

			if(isset($split[1]))
			{
				$check_team = ['brand' => $split[1], 'boss' => $this->login('id'), 'limit' => 1];
				if($team = \lib\db\teams::get($check_team))
				{
					if(isset($split[2]) && isset($team['id']))
					{
						$check_branch = ['brand' => $split[2], 'team_id' => $team['id'], 'boss' => $this->login('id'), 'limit' => 1];
						if(\lib\db\branchs::get($check_branch))
						{
							$check_route = true;
						}
					}
				}
			}


			// muset check $split[2] whit $split[1]
			if($check_route)
			{
				\lib\router::set_controller('content_admin\member\controller');
				return;
			}
		}

		// the url is team/ermile we remove team/ from first of url to get the 'ermile' [team brand]
		$name = str_replace('team/', '', $url);
		// check the team exist or no and this user is the boss ot this team
		// this function in content_admi/main/model
		if($this->model()->is_exist_team($name))
		{
			$this->get(false, 'edit')->ALL("team/$name");
			$this->post('edit')->ALL("team/$name");
		}

	}
}
?>
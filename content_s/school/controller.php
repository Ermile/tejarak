<?php
namespace content_s\school;

class controller extends \content_s\main\controller
{
	/**
	 * route
	 */
	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		// ADD NEW SCHOOL
		$this->get(false, 'add')->ALL('school');
		$this->post('add')->ALL('school');

		// ADD NEW CLASSROOM
		$this->get(false, 'classroom_add')->ALL("/^([a-zA-z0-9]+)\/classroom\/add$/");
		$this->post('classroom_add')->ALL("/^([a-zA-z0-9]+)\/classroom\/add$/");

		// LIST CLASSROOM
		$this->get(false, 'classroom')->ALL("/^([a-zA-z0-9]+)\/classroom$/");
		$this->post('classroom')->ALL("/^([a-zA-z0-9]+)\/classroom$/");

		// EDIT CLASSROOM
		$this->get(false, 'classroom_edit')->ALL("/^([a-zA-z0-9]+)\/classroom\=([a-zA-Z0-9]+)$/");
		$this->post('classroom_edit')->ALL("/^([a-zA-z0-9]+)\/classroom\=([a-zA-Z0-9]+)$/");

		if(preg_match("/^([a-zA-z0-9]+)\/classroom$/", $url))
		{
			$this->display_name = 'content_s\school\classroom\classroomList.html';
		}

		if(preg_match("/^([a-zA-z0-9]+)\/classroom\/add$/", $url) || preg_match("/^([a-zA-z0-9]+)\/classroom\=([a-zA-Z0-9]+)$/", $url))
		{
			$this->display_name = 'content_s\school\classroom\classroomAdd.html';
		}

		$code = \lib\router::get_url(0);

		// check the school exist or no and this user is the boss ot this school
		// this function in content_sdmi/main/model
		if($this->model()->is_exist_team_code($code))
		{
			$this->get(false, 'edit')->ALL("$code/edit");
			$this->post('edit')->ALL("$code/edit");
		}

		unset($_SESSION['first_go_to_setup']);
	}
}
?>
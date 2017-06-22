<?php
namespace content_a\team;

class view extends \content_a\main\view
{

	/**
	 * view to add team
	 */
	public function view_add()
	{
		$this->data->page['title'] = 'Add new team';
		$this->data->page['desc'] = 'Add new team';
	}

	/**
	 * load team data to edit it
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_edit($_args)
	{
		$team_name = \lib\router::get_url(0);
		$this->data->team = $this->model()->edit($team_name);
		$this->data->edit_mode = true;

		if(isset($this->data->team['name']))
		{
			$this->data->page['title'] = 'Edit team';
			$this->data->page['desc'] = 'Edit team'. $this->data->team['name'];
		}
	}
}
?>
<?php
namespace content_a\gateway\add;

class view extends \content_a\main\view
{

	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_add($_args)
	{
		$team                      = \lib\router::get_url(0);
		$this->data->team_default  = $team_default = $this->data->current_team;

		$this->data->page['title'] = T_('Add new gateway');
		$this->data->page['desc']  = T_('Allow to add specefic type of user that only allow to set enter and exit without more permission.');
	}
}
?>
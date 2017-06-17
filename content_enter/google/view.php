<?php
namespace content_enter\google;


class view extends \content_enter\main\view
{
	public function config()
	{
		parent::config();

		$this->data->auth_url = \lib\social\google::auth_url();




		$this->data->page['title']   = T_('Enter to :name with google', ['name' => $this->data->site['title']]);
		$this->data->page['special'] = true;
		$this->data->page['desc']    = $this->data->page['title'];
	}
}
?>
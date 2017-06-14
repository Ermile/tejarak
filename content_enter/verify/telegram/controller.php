<?php
namespace content_enter\verify\telegram;


class controller extends \content_enter\main\controller
{
	public function _route()
	{
		// if the user is login redirect to base
		parent::if_login_not_route();

		// if step mobile is done
		// and step pass is done
		if(self::done_step('mobile') && self::done_step('pass'))
		{
			if(self::get_request_method() === 'get')
			{
				// if the user start my bot and wa have her chat id
				// if user start my bot try to send code to this use
				// if okay route this
				// else go to nex way
				if($this->model()->user_start_telegram_bot() && $this->model()->send_telegram_code())
				{
					$this->get()->ALL('verify/telegram');
				}
				else
				{
					$way = self::send_way();
					if($way)
					{
						// go to next way of send code
						self::go_to($way);
						return;
					}
					else
					{
						self::go_to('what');
					}
				}
			}
			elseif(self::get_request_method() === 'post')
			{
				$this->post('verify')->ALL('verify/telegram');
			}
			else
			{
				self::error_method('verify/telegram');
			}
		}
		else
		{
			self::error_page('verify/telegram');
		}
	}
}
?>
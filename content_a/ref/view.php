<?php
namespace content_a\ref;

class view
{
	public function view_ref($_args)
	{
		$result = self::get_ref();
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				\dash\data::set($key, $value);
			}
		}
	}

	public static function get_ref()
	{
		if(!\dash\user::login())
		{
			return null;
		}

		$meta =
		[
			'get_count' => true,
			'data'  => \dash\user::id(),
		];
		$result = [];

		$result['click'] = \dash\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:set']));
		$result['signup'] = \dash\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:signup']));
		$result['profile'] = \dash\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:complete:profile']));
		return $result;
	}
}
?>
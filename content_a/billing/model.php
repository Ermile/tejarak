<?php
namespace content_a\billing;


class model
{

	/**
	 * the user id
	 *
	 * @var        <type>
	 */
	public static $user_id = null;




	/**
	 * post data and update or insert billing data
	 */
	public static function post()
	{
		if(!\dash\user::login())
		{
			\dash\notif::error(T_("You must login to pay amount"));
			return false;
		}

		if(\dash\request::post('type') === 'promo')
		{
			if(\dash\request::post('promo'))
			{
				self::check_promo();
				return;
			}
			else
			{
				\dash\notif::error(T_("Invalid promo code"), 'promo', 'arguments');
				return false;
			}
		}

		$meta = ['turn_back' => \dash\url::pwd()];

		\dash\utility\payment\pay::start(\dash\user::id(), \dash\request::post('bank'), \dash\request::post('amount'), $meta);
	}



	public static function check_promo()
	{
		$promo     = \dash\request::post('promo');
		$amount    = 0;
		$shcode = null;
		$ref  = null;

		$log_meta =
        [
        	'data' => null,
        	'meta' =>
        	[
				'user'    => \dash\user::id(),
				'ref'     => $ref,
				'post'    => \dash\request::post(),
				'session' => $_SESSION,
        	],
        ];

		if(!preg_match("/^ref\_([A-Za-z0-9]+)$/", $promo, $split))
		{
			\dash\db\logs::set('ref:reqular:invalid', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Invalid promo code"), 'promo', 'arguments');
			return false;
		}
		if(isset($split[1]))
		{
			$shcode = $split[1];
			$ref = \dash\coding::decode($shcode);
			if(!$ref)
			{
				\dash\db\logs::set('ref:shortURL:invalid', \dash\user::id(), $log_meta);
				\dash\notif::error(T_("Invalid promo code"), 'promo', 'arguments');
				return false;
			}
		}

		if(intval(\dash\user::id()) === intval($ref))
		{
			\dash\db\logs::set('ref:yourself', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("You try to referral yourself!"), 'promo', 'arguments');
			return false;
		}

		if(\dash\user::login('ref'))
		{
			\dash\db\logs::set('ref:full', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("You have ref. can not set another ref"), 'promo', 'arguments');
			return false;
		}

		$check_ref = \dash\db\users::get_by_id($ref);

		if(!isset($check_ref['id']))
		{
			\dash\db\logs::set('ref:user:not:found', \dash\user::id(), $log_meta);
			\dash\notif::error(T_("Ref not found"), 'promo', 'arguments');
			return false;
		}

		\dash\db\users::update(['ref' => $ref], \dash\user::id());
		$_SESSION['user']['ref'] = $ref;

		$transaction_set =
        [
			'caller'          => 'promo:ref',
			'title'           => T_("Promo for using ref"),
			'user_id'         => \dash\user::id(),
			'plus'            => 10 * 1000,
			'payment'         => null,
			'related_foreign' => 'users',
			'related_id'      => $ref,
			'verify'          => 1,
			'type'            => 'money',
			'unit'            => 'toman',
			'date'            => date("Y-m-d H:i:s"),
        ];

        \dash\db\transactions::set($transaction_set);


        $notify_ref =
        [
			'to'      => $ref,
			'cat'     => 'ref',
			'content' => T_("Someone used your ref link in her referral"),
        ];
        \dash\db\notifications::set($notify_ref);


        $notify_ref =
        [
			'to'      => \dash\user::id(),
			'cat'     => 'useref',
			'content' => T_("Your are using referral program and your account was charged"),
        ];
        \dash\db\notifications::set($notify_ref);


        if(\dash\engine\process::status())
        {
        	\dash\db\logs::set('user:use:ref', \dash\user::id(), $log_meta);
        	\dash\db\logs::set('user:was:ref', $ref, $log_meta);
        	\dash\notif::ok(T_("Your ref was set and your account was charge"));
        }
	}


	/**
	 * use usage
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function usage()
	{

		if(isset($_SESSION['usage_team']) && isset($_SESSION['usage_team_time']))
		{
			if(time() - strtotime($_SESSION['usage_team_time']) > (60*60))
			{
				$_SESSION['usage_team'] = self::run_usage();
				$_SESSION['usage_team_time'] = date("Y-m-d H:i:s");
			}
		}
		else
		{
			$_SESSION['usage_team'] = self::run_usage();
			$_SESSION['usage_team_time'] = date("Y-m-d H:i:s");
		}

		return $_SESSION['usage_team'];
	}


	/**
	 * { function_description }
	 */
	public static function run_usage()
	{
		if(!\dash\user::login())
		{
			return false;
		}

		$user_id = \dash\user::id();

		$all_creator_team = \lib\db\teams::get(['creator' => $user_id]);

		$total_usage = 0;
		if(is_array($all_creator_team))
		{
			foreach ($all_creator_team as $key => $value)
			{
				if(isset($value['id']))
				{
					$calc = new \lib\utility\calc($value['id']);
					$calc->save(false);
					$calc->notify(false);
					$calc->type('calc');
					$total_usage += $calc->calc();
				}
			}
		}
		return $total_usage;
	}
}
?>
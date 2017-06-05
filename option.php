<?php
/**
@ In the name Of Allah
* The base configurations of the tejarak.
*/
self::$language =
[
	'default' => 'en',
	'list'    => ['fa','en',],
];

self::$config['default_language']             = 'en';

self::$config['redirect_url']                 = 'http://tejarak.dev';
self::$config['multi_domain']                 = true;
self::$config['redirect_to_main']             = true;
self::$config['https']                        = false;
self::$config['default_tld']                  = 'dev';
self::$config['default_permission']           = null;
self::$config['debug']                        = true;
self::$config['coming']                       = false;
self::$config['short_url']                    = null;
self::$config['save_as_cookie']               = false;
self::$config['log_visitors']                 = true;
self::$config['passphrase']                   = null;
self::$config['passkey']                      = null;
self::$config['passvalue']                    = null;
self::$config['default']                      = null;
self::$config['redirect']                     = 'ganje';
self::$config['register']                     = true;
self::$config['recovery']                     = true;
self::$config['fake_sub']                     = null;
self::$config['real_sub']                     = true;
self::$config['force_short_url']              = null;
self::$config['sms']                          = true;

self::$config['account']                      = true;
self::$config['main_account']                 = null;
self::$config['account_status']               = true;
self::$config['use_main_account']             = false;

self::$config['domain_same']                  = true;
self::$config['domain_name']                  = 'tejarak';
self::$config['main_site']                    = 'http://tejarak.dev';


/**
* the social network
*/
self::$social['status']                       = true;

self::$social['list']['telegram']             = 'tejarak';
self::$social['list']['facebook']             = 'tejarak';
self::$social['list']['twitter']              = 'tejarak';
self::$social['list']['googleplus']           = '109727653714508522373';
self::$social['list']['github']               = 'ermile';
self::$social['list']['linkedin']             = null;
self::$social['list']['aparat']               = 'tejarak';

/**
* TELEGRAM
* t.me
*/
self::$social['telegram']['status']           = true;
self::$social['telegram']['name']             = null;
self::$social['telegram']['key']              = null;
self::$social['telegram']['bot']              = null;
self::$social['telegram']['hookFolder']       = null;
self::$social['telegram']['hook']             = null;
self::$social['telegram']['debug']            = true;
self::$social['telegram']['channel']          = null;
self::$social['telegram']['botan']            = null;

/**
* FACEBOOK
*/
self::$social['facebook']['status']           = false;
self::$social['facebook']['name']             = 'tejarak';
self::$social['facebook']['key']              = null;
self::$social['facebook']['app_id']           = null;
self::$social['facebook']['app_secret']       = null;
self::$social['facebook']['redirect_url']     = null;
self::$social['facebook']['required_scope']   = null;
self::$social['facebook']['page_id']          = null;
self::$social['facebook']['access_token']     = null;
self::$social['facebook']['client_token']     = null;

/**
* TWITTER
*/
self::$social['twitter']['status']            = false;
self::$social['twitter']['name']              = 'tejarak';
self::$social['twitter']['key']               = null;
self::$social['twitter']['ConsumerKey']       = null;
self::$social['twitter']['ConsumerSecret']    = null;
self::$social['twitter']['AccessToken']       = null;
self::$social['twitter']['AccessTokenSecret'] = null;

/**
* GOOGLE PLUS
*/
self::$social['googleplus']['status']         = false;
self::$social['googleplus']['name']           = '109727653714508522373';
self::$social['googleplus']['key']            = null;


/**
* GITHUB
*/
self::$social['github']['status']             = false;
self::$social['github']['name']               = 'ermile';
self::$social['github']['key']                = null;


/**
* LINKDIN
*/
self::$social['linkedin']['status']           = false;
self::$social['linkedin']['name']             = null;
self::$social['linkedin']['key']              = null;


/**
* APARAT
*/
self::$social['aparat']['status']             = false;
self::$social['aparat']['name']               = 'tejarak';
self::$social['aparat']['key']                = null;


/**
* sms kavenegar config
*/
self::$sms['kavenegar']['value']              = 'kavenegar_api';
self::$sms['kavenegar']['status']             = true;
self::$sms['kavenegar']['apikey']             = '783067644A597A41716F3734755A683152736F6673773D3D';
self::$sms['kavenegar']['debug']              = null;
self::$sms['kavenegar']['line1']              = '10006660066600';
self::$sms['kavenegar']['line2']              = null;
self::$sms['kavenegar']['iran']               = null;
self::$sms['kavenegar']['header']             = null;
self::$sms['kavenegar']['footer']             = 'tejarak';
self::$sms['kavenegar']['one']                = true;
self::$sms['kavenegar']['signup']             = true;
self::$sms['kavenegar']['verification']       = true;
self::$sms['kavenegar']['recovery']           = true;
self::$sms['kavenegar']['changepass']         = true;

?>
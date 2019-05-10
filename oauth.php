<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

ini_set('display_errors', 'on');
ini_set('error_reporting', E_ALL);

/**
 * 配置项
 */

const APP_ID = 'xxxx';
const APP_SECRET = 'xxxx';
const REDIRECT_URI = 'xxxx';

if (APP_ID === 'xxxx' || APP_SECRET === 'xxxx' || REDIRECT_URI === 'xxxx') {
	throw new Exception('执行此脚本前请先进行 APP_ID、APP_SECRET 和 REDIRECT_URI的配置！');
}

require_once './vendor/autoload.php';
$client = new Client(['timeout' => 5]);
session_start();

/**
 * 业务逻辑
 */

if(!isset($_SESSION['isLogin']) || !$_SESSION['isLogin']) {

	/**
	 * 未登录
	 */

	if (isset($_GET['code'])) {

		/**
		 * 有 code 参数，
		 * 说明是从微信授权页跳转过来的，
		 * 需要进行下一步的微信用户数据获取
		 */

		 // 1. 根据 code 获取 access_token
		 $code = $_GET['code'];
		 try {
			$response = $client->request('GET', 'https://api.weixin.qq.com/sns/oauth2/access_token', [
				'query' => [
					'appid' => APP_ID,
					'secret' => APP_SECRET,
					'code' => $code,
					'grant_type' => 'authorization_code'
				],
			]);
		 } catch (TransferException $e) {
			 throw new Exception('根据 code 值获取 access_token 时出现异常：' . $e->getMessage());
		 }
		$response = json_decode($response->getBody()->getContents(), true);

		// 2. 根据 access_token 获取 用户信息，获取后记录为已登陆，然后重定向至本页面
		if (!isset($response['errcode'])) {
			$access_token = $response['access_token'];
			$openid = $response['openid'];
			try {
				$response = $client->request('GET', 'https://api.weixin.qq.com/sns/userinfo', [
					'query' => [
						'access_token' => $access_token,
						'openid' => $openid,
						'lang' => 'zh_CN',
					],
				]);
			 } catch (TransferException $e) {
				 throw new Exception('根据 access_token 值获取用户信息时出现异常：' . $e->getMessage());
			}
			$response = json_decode($response->getBody()->getContents(), true);

			if (!isset($response['errcode'])) {
				// 记录为登录状态
				$_SESSION['userInfo'] = $response;
				$_SESSION['isLogin'] = true;
				// 重定向至本页面
				header('Location: ' . REDIRECT_URI);
			} else {
				throw new Exception('请求用户信息时接口返回错误：' . $response['errcode'] . ' ' . $response['errmsg']);
			}
		} else {
			throw new Exception('请求 access_token 时接口返回错误：' . $response['errcode'] . ' ' . $response['errmsg']);
		}

	} else {
		
		/**
		 * 没有 code 参数，直接跳转授权页
		 */

		header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APP_ID . '&redirect_uri=' . urlencode(REDIRECT_URI) . '&response_type=code&scope=snsapi_userinfo#wechat_redirect');
	}

} else {

	/**
	 * 已登录
	 */

	echo '<pre>';
	print_r($_SESSION);
}
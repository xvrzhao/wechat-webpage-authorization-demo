# 微信公众号网页授权 demo

## 描述
此 demo 所模拟的逻辑为：
- 当用户访问公众号网站时，开发者服务器要求必须先登录，所以执行检测用户是否登录。
- 若已登陆则展示用户信息。
- 若未登录则要获得微信的授权，从而获得微信用户的数据，作为本网站的登录用户。

## 测试
> 请先阅读官方文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842 ，此 demo 仅仅是模拟业务流程。另外，本 demo 未使用到文档中的 `刷新 access_token` 接口，可以根据自己的业务需要将 `refresh_token` 存起来用于 `access_token` 的刷新。

1. 需要在 公众平台 的 测试号管理页面 里的 体验接口权限表-网页服务-网页账号-网页授权获取用户基本信息 后面设置"授权回调页面域名"(该域名为需要授权的网页的域名，不要添加协议头，测试账号可以为 IP 地址)。
2. 执行 `git clone git@github.com:xvrzhao/wechat-webpage-authorization-demo.git`
3. 执行 `composer install` 安装网络请求依赖。
4. 根据自己的开发测试号修改 `oauth.php` 脚本文件的配置项，其中 `REDIRECT_URI` 常量设置为微信用户允许授权后所跳转的网页URI，也就是本脚本。
5. 将 `oauth.php` 文件和 `vendor` 文件夹上传至开发服务器域名的跟路径。
6. 在微信内打开这个你的公众号网站链接，体验授权流程。
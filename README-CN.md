<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-CN.md"> | CN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/CN.png" alt="CN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | SA <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/SA.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
</div>


## 安装和集成指南

### 最低要求

- WHMCS 7.8 或更高版本
- PHP7.4 或更高版本（建议使用8.1）
- 必须激活 PHP SOAPClient 插件
- 包含身份信息/税号/税务局信息的客户 T.C. 自定义字段（可选）

## 设置

!!!! 注意 !!!!

_**如果你是在升级，请在安装之前备份旧文件。**_

将下载的文件夹中的 "modules" 文件夹放置到 Whmcs 安装的文件夹中（例如：/home/whmcs/public_html）。丢弃 .gitignore、README.md 和 LICENSE 文件。

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- 进入 "系统设置" 部分

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- 进入 "域名注册商" 部分

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- 在你进入的页面上，如果你将模块文件放在了正确的文件夹中，将会显示 "DomainNameAPI"。
- 激活后，输入我们提供的用户名和密码。
- 保存后，你的用户名和当前余额将可见。
- 从你看到的设置中匹配用于获取用户的 TR 身份证号码和税号信息，如果有的话。
- 如果你使用的是除美元以外的单一主要货币，你可以设置 "Exchange Convertion For TLD Sync" 设置。（此设置仅用于地区 TLD 导入的定价同步。否则，你不需要更改）

<a href="https://youtu.be/LEw_iMnquSo">+ Youtube 链接</a>

<hr>

## 定价、TLD 归属和查询设置

<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- 从 "系统设置" 中进入 "域名定价"。
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- 确定你想要销售的 TLD（例如：.com.tr）。
- 选择 "Domain Name API" 进行自动注册。
- 选择 EPP 代码选项。
- 对于定价，你可以手动输入。你也可以设置批量价格（将在下一部分解释）。

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a>

- 你可以使用 "DomainNameApi" 作为域名查询源，而不是使用公共 Whois 服务器。为此，请在 "查询提供商" 部分中点击 "更改" 按钮，在域名注册选项之后选择出现的 "DomainNameApi" 选项，然后选择要使用的 TLD。

获取更多信息：<a href="https://docs.whmcs.com/Domain_Pricing">Whmcs 域名定价</a>

<hr>

## 批量定价和自动定价

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- 从 "实用工具" 部分进入 "注册商 TLD 同步"。从弹出的屏幕中选择 "DomainNameApi"，稍等片刻。
- 在下一个屏幕上，我们系统中的所有 TLD 与 whmcs 中的所有 TLD 进行了交叉比较，计算并显示了批量的利润和损失，从而可以进行导入。

获取更多信息：<a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD 同步</a>

<hr>

## 管理员视角

<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- 你可以发送 "删除请求" 以删除域名。
- 你可以发送 "取消转移" 以取消域名转移。
- 你可以查看域名的实时状态、即时启动和结束。
- 你可以列出你的子域名。
- 你可以查看额外的字段信息。

<hr>

## 通用设置

<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- 从 "系统设置" 中进入 "通用设置"，选择域名选项卡。
- 如果你希望客户能够自行注册域名，请激活 "允许客户自行注册域名" 选项。
- 如果你希望客户能够自行转移域名，请激活 "允许客户转移域名给你" 选项。
- 如果你希望客户能够在到期日期之前续订域名，请激活 "启用续订订单" 选项。
- 如果你希望客户在支付时自动续订，请激活 "付款时自动续订" 选项。
- 如果你希望定期检查和同步当前域名，请激活 "启用域名同步" 选项。我们建议启用此选项。
- 如果你想管理土耳其语、希伯来语、阿拉伯语、俄语等域名，请激活 "允许 IDN 域名" 选项。
- 在 "默认名称服务器" 信息中，输入你的名称服务器信息。

<hr>

## 同步设置

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- 从 "系统设置" 中进入 "自动化设置"。进入 "域名同步设置" 部分。
- 打开域名同步。
- 如果你希望更新时更改到期日期，请激活 "同步下一个到期日期" 选项。
- 根据你的系统负荷调整其他设置。

<hr>

## 错误 - 详细视图

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- 从 "系统日志" 选项中的右侧进入 "模块日志" 部分。
- 找到相关日志并点击日期。
- 可以查看详细的请求、回复和过滤后的回复。

!! 我们建议在日常使用中关闭系统日志，以提高系统性能。详细信息请参阅：<a href="https://docs.whmcs.com/System_Logs">Whmcs 日志记录</a>

## 故障排除

- 我已经添加了新的自定义字段，但在设置中看不到它们。
- 缓存可能已过期。删除缓存文件夹中的所有文件。

<hr>

- 我收到错误消息 "Parsing WSDL: Couldn't load from..."
- 看起来是网络问题。你的服务器 IP 地址可能被注册局阻止。请联系我们以解决问题。


## 返回和错误代码，说明

| 代码   | 说明                                              | 详细信息                                                             |
|------|-------------------------------------------------|------------------------------------------------------------------|
| 1000 | Command completed successfully                  | 命令成功完成                                                           |
| 1001 | Command completed successfully; action pending. | 命令成功完成；操作待处理                                                     |
| 2003 | Required parameter missing                      | 必需的参数缺失。例如：联系信息中缺少电话号码等                                          |
| 2105 | Object is not eligible for renewal              | 对象不适合续订，已锁定更新操作。状态不能为"clientupdateprohibited"。可能是由其他状态引起的。       |
| 2200 | Authentication error                            | 认证错误，权限码错误或域名已在其他注册公司中。                                          |
| 2302 | Object exists                                   | 域名或名称服务器信息已存在于数据库中，无法注册。                                         |
| 2303 | Object does not exist                           | 域名或名称服务器信息在数据库中不存在，需要创建新的注册。                                     |
| 2304 | Object status prohibits operation               | 对象状态禁止操作，无法更新，已锁定更新操作。状态不能为"clientupdateprohibited"。可能是由其他状态引起的。 |

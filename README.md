
# IOS超级签名 App Store Connect API接口
## PHP 编写的接口，已全部测试可用
***

接口调用前，希望你准备好以下：

- 1.一个可用的苹果开发者账号
- 2.请求App Store Connect API访问权限。登录：App Store Connect后台，“用户和访问” - “密钥”，点击“请求访问权限”。只有agent才有权限。
- 3.生成密钥。
- 进入appstore中->用户管理->秘钥->创建（管理）,然后获取到：issuer id,key id,下载秘钥(*.p8) 申请访问权限后，才会看到“生成密钥”的按钮，点击“生成密钥”，根据提示起个名字，完成后会产生一个“Issuer ID”和“密钥ID”，这两个参数后面生成token需要用到。下载密钥，密钥是一个.p8的文件，注意：私钥只能下载一次，永远不会过期，保管好，如果丢失了，去App Store Connect后台撤销密钥，否则别人拿到也可以用。


Apapi.php 该文件是接口初始化入口。需要传递上面生成的相关key和秘钥p8

getCSR()    1.获取CSR文件。

get_distribution_pem()    2.获取distribution_key.pem文件。

exportP12();        3.导出p12文件。

createProvision()     4.生成profile，并下载mobileprovision文件。

里面的内容都有注释，希望对你有帮助！

有现成案例：www.aisignapp.com，可以测试
若需要购买代码也可以联系：208685859

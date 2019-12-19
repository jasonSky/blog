## Blog
 博客首页：
 ![](img/1.png)

 关于：
 ![](img/about.png)

 **后台管理**

 管理登录：
 ![](img/login.png)

 管理首页：
 ![](img/main.png)

## 开源协议

[MIT](./LICENSE)

## 感谢

[ZHENFENG13](https://github.com/ZHENFENG13)
[otale](https://github.com/otale)

##部署安装
1. 新建数据库tale

       进入mysql  运行source sql文件来恢复库表

2. git下来后在根目录 mvn package

       拷贝war包到webapps目录下部署即可

3. blog.jasonsky.com.cn 访问前台<br/>
   blog.jasonsky.com.cn/admin 访问后台 默认账号admin/123456  如果登录不了请修改数据库中t_user数据即可 密码是md5加密

       需要通过域名访问时
       配置Nginx{proxy_cookie_path 以免session取不到的问题} 

            server {
                 listen 80;
                 server_name blog.jasonsky.com.cn;
                 location / {
                         proxy_pass http://localhost:8080/blog/;
                         proxy_cookie_path  /blog      /;
                         proxy_set_header Host $host;
                         proxy_set_header X-Real-IP $remote_addr;
                         proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
                 }
             }

4. nkeditor问题
https://blog.csdn.net/rogerjava/article/details/82658998
编辑的源应该为div [textarea会自动把里面的内容进行转义存储]
修改article的接收方式html()

5. php支持
https://blog.csdn.net/sl543001/article/details/12999737  tomcat php支持
web.xml + jar 


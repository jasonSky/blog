## Blog
 博客首页：
 ![](img/index.png)

 归档：
 ![](img/metas.png)

 友链：
 ![](img/links.png)
 
 关于：
 ![](img/about.png)
 
 搜索：
 ![](img/search.png)
 
 **后台管理**
 
 管理登录：
 ![](img/admin-login.png)
 
 管理首页：
 ![](img/admin-index.png)
 
 发布文章：
 ![](img/admin-publish.png)
 
 文章管理：
 ![](img/admin-article.png)
 
 页面管理：
 ![](img/admin-pages.png)
 
 分类标签：
 ![](img/admin-category.png)
 
 文件管理：
 ![](img/admin-upload.png)
  
 友链管理：
 ![](img/admin-links.png)
   
 系统设置：
 ![](img/admin-setting.png)
 
## 开源协议

[MIT](./LICENSE)

## 感谢

[ZHENFENG13](https://github.com/ZHENFENG13)
[otale](https://github.com/otale)

##部署安装
1. 新建数据库tale
进入mysql  运行source sql文件来恢复库表

2. git下来后在根目录 mvn install
nohub  mvn spring-boot:run

3. http://ip:8081/ 访问前台
后面加admin/login访问后台 默认账号admin/123456  如果登录不了请修改数据库中t_user数据即可 密码是md5加密

1. /opt/           --blog + withme + survery
   /home/php/www/  --phpgraphy
	源文件及项目目录

2. /usr/local/nginx  -- nginx
/usr/local/ngrok  -- nat
/usr/local/go
/usr/local/php
/usr/bin/docker

3. /usr/tomcat  -- tomcat

4. /home/ftpUsers -- ftp

5.  配置文件
/etc/php.ini
/etc/shadowsocks.json  --/usr/bin/python /usr/bin/ssserver -c /etc/shadowsocks.json -d start
/usr/local/nginx/sbin  --./nginx -c /usr/local/nginx/conf/nginx.conf
/usr/local/ngrok       --nohup ./bin/ngrokd -tlsKey=server.key -tlsCrt=server.crt -domain="jasonsky.com.cn" -httpAddr=":9000" -httpsAddr=":443" &

6. service开机启动（vsftpd）
service vsftpd start
service enable vsftpd

7. php-fpm(nginx php支持)
vi /etc/php-fpm.d/www.conf
service php-fpm restart

网站(不分先后)
photo
php
info
blog
www


python3: 
编译安装 configure -> make -> make install
ln -s /usr/local/python3.6.4/bin/python3  /usr/bin/python3
ln -s /usr/local/python3.6.4/bin/pip3  /usr/bin/pip3
1. python3: error while loading shared libraries: libpython3.5m.so.1.0: cannot open shared object file:
vi /etc/ld.so.conf.d/python3.conf  ->  /usr/local/python3.6.4/lib

Mongodb:/usr/local/mongodb/
sys/!QAZ2wsx(root) jason/!QAZ2wsx(admin)
wget -> mv
1. add log dir
2. ln -s
3. modify /etc/profile ->source
mongod --config /usr/local/mongod3.4.2/mongodb.conf
mongod --config /usr/local/mongod3.4.2/mongodb.conf --shutdown

chplayer:
/home/php/www/phpgraphy/chplayer --photo.jasonsky.com.cn/chplayer

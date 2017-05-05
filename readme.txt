需要打开iptables或者BSD的pf防火墙3306 TCP端口

通用Mysql3306端口的iptables（vi /etc/sysconfig/iptables）防火墙规则
/sbin/iptables -A INPUT -i eth0 -p tcp --destination-port 3306 -j ACCEPT
/sbin/iptables -A INPUT -i eth0 -p tcp --destination-port 6379 -j ACCEPT
或者你只允许web那台服务器（IP为10.5.1.3）可以访问:

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.30 -p tcp --destination-port 3306 -j ACCEPT
或者只允许局域网192.168.1.0/24段访问

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.30 -p tcp --destination-port 3306 -j ACCEPT
或者只允许局域网192.168.1.0/24段访问

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.0/24 -p tcp --destination-port 3306 -j ACCEPT
 最后重启防火墙使配置生效(RHEL / CentOS):
# service iptables save
#或者是
# /etc/init.d/iptables restart


1.mongodb启动
/usr/local/mongodb2.8/bin/mongod --fork --nojournal --config /usr/local/mongodb2.8/mongod.conf

2.php安装
apt-get install php5 php5-fpm php5-mysql php5-mysqlnd php5-gd php5-mbstring


3.安装php mongoclient
wget https://pecl.php.net/get/mongo-1.6.14.tgz
tar -zxvf mongo-1.6.14.tgz
cd mongo-1.6.14
phpize 
./configure
make
make install

看扩展安装路径
vim php.ini
添加 
extension=mongo.so


4.etldb.php

db.createUser(  
  {  
    user: "debian",  
    pwd: "debian",  
    roles: [ { role: "readWrite", db: "empery" } ]  
  }  
)  

/wwwdata/etldb.php
php etldb.php export   mysql导出数据到mongodb


5.新手引导任务
/wwwdata/yw_web/updatetask.php

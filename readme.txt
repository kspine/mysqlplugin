��Ҫ��iptables����BSD��pf����ǽ3306 TCP�˿�

ͨ��Mysql3306�˿ڵ�iptables��vi /etc/sysconfig/iptables������ǽ����
/sbin/iptables -A INPUT -i eth0 -p tcp --destination-port 3306 -j ACCEPT
/sbin/iptables -A INPUT -i eth0 -p tcp --destination-port 6379 -j ACCEPT
������ֻ����web��̨��������IPΪ10.5.1.3�����Է���:

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.30 -p tcp --destination-port 3306 -j ACCEPT
����ֻ���������192.168.1.0/24�η���

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.30 -p tcp --destination-port 3306 -j ACCEPT
����ֻ���������192.168.1.0/24�η���

/sbin/iptables -A INPUT -i eth0 -s 192.168.1.0/24 -p tcp --destination-port 3306 -j ACCEPT
 �����������ǽʹ������Ч(RHEL / CentOS):
# service iptables save
#������
# /etc/init.d/iptables restart


1.mongodb����
/usr/local/mongodb2.8/bin/mongod --fork --nojournal --config /usr/local/mongodb2.8/mongod.conf

2.php��װ
apt-get install php5 php5-fpm php5-mysql php5-mysqlnd php5-gd php5-mbstring


3.��װphp mongoclient
wget https://pecl.php.net/get/mongo-1.6.14.tgz
tar -zxvf mongo-1.6.14.tgz
cd mongo-1.6.14
phpize 
./configure
make
make install

����չ��װ·��
vim php.ini
��� 
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
php etldb.php export   mysql�������ݵ�mongodb


5.������������
/wwwdata/yw_web/updatetask.php

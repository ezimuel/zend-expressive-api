# Vagrant configuration for zend-expressive workshop
# @author Enrico Zimuel (enrico@zend.com)

VAGRANTFILE_API_VERSION = '2'

$script = <<SCRIPT
# Fix for Temporary failure resolving 'archive.ubuntu.com'
echo "nameserver 8.8.8.8" | sudo tee /etc/resolv.conf > /dev/null

# Install dependencies
apt-get update
apt-get install -y nginx git curl sqlite3 php7.2 php7.2-cli php7.2-fpm php7.2-sqlite3 php7.2-pdo php7.2-xml

# Configure Nginx
echo "server {
    listen 8080;

    root /home/ubuntu/zend-expressive-api/public;
    server_name ubuntu-xenial;

    # Logs
    access_log /home/ubuntu/zend-expressive-api/log/access_log;
    error_log /home/ubuntu/zend-expressive-api/log/error_log;

    index index.php index.html index.htm;

    location / {
        try_files \\$uri \\$uri/ /index.php;
    }
    location ~ \\.php\$ {
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        include snippets/fastcgi-php.conf;
    }
    # Block access to .htaccess
    location ~ \\.htaccess {
        deny all;
    }
}" > /etc/nginx/sites-available/zend-expressive-api
chmod 644 /etc/nginx/sites-available/zend-expressive-api
ln -s /etc/nginx/sites-available/zend-expressive-api /etc/nginx/sites-enabled/zend-expressive-api
service nginx restart

if [ -e /usr/local/bin/composer ]; then
    /usr/local/bin/composer self-update
else
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

chmod +r /home/ubuntu/zend-expressive-api/data/oauth2/*
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = 'bento/ubuntu-18.04'
  config.vm.network "forwarded_port", guest: 8080, host: 8080
  config.vm.synced_folder ".", "/home/ubuntu/zend-expressive-api", id: "vagrant-root",
     owner: "vagrant",
     group: "www-data",
     mount_options: ["dmode=775,fmode=660"]
  config.vm.provision 'shell', inline: $script

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--name", "zend-expressive-api"]
  end
end

# Trident Test

This project is made with Symfony 5, microservices edition.

##### Requirements

- **PHP >= 7.2**
- **WebServer** ([Apache](https://www.apache.org/) or [NGINX](https://www.nginx.com/))
- **MariaDB or MySQL**
- **Composer**
- **PHP ext-curl**
- **PHP ext-json**

##### Virtualhost Configuration:

You can use your favourite webserver, I suggest you NGINX.

You can find an example of **Symfony5** Virtualhost for [Apache](https://symfony.com/doc/current/setup/web_server_configuration.html#apache-with-mod-php-php-cgi) and [NGINX](https://symfony.com/doc/current/setup/web_server_configuration.html#nginx)

##### Installation:

Clone this repository and run composer install:

`cd trident-test-app && composer install`

Put in your **/etc/hosts** file this line:

`127.0.0.1	test.trident.local`

Go to **sandbox**: http://test.trident.local[:your_web_server_port]/api/doc for all API endpoints.




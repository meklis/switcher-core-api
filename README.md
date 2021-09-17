# Switcher-Core-Api 
RestAPI for library [switcher-core](https://github.com/meklis/switcher-core)  based on roadrunner.   
## List of supported devices [here](https://github.com/meklis/switcher-core/blob/master/docs/DEVICES.md)     

## Features  
* Automate detect device vendor and model
* Prometheus metrics 
* Batch calling 

## Installation and running 
### Over docker 
* Use docker-compose file    

Create file `docker-compose.yml` with content 
```yaml
version: '2'

services:
  switcher-core-api:
    image: meklis/switcher-core-api
    ports:
      - 8080:5990 # HTTP RestAPI port 
      - 2112:2112 # prometheus metrics 
    volumes:
      - ./logs:/app/logs

```
Start command `docker-compose up -d`

### Native
For native install you must use php >=7.4 with dependencies ([see here](#php_dependencies)), git, composer    
After installing all dependencies  run commands:   
```shell
git clone https://github.com/meklis/switcher-core-api
cd switcher-core-api
composer install
sudo ./vendor/bin/rr get -l /usr/local/bin/roadrunner
```
Whats next?
You can run server use command `roadrunner serve -c </path/to/.rr.yaml>`      
     
**Recomenation: [configure running server as deamon](https://roadrunner.dev/docs/beep-beep-systemd)** 

## Usage   
### See examples of API in [postman documenter](https://documenter.getpostman.com/view/6612340/U16qJNqV)

### Prometheus metrics
URL: **http://127.0.0.1:2112/metrics**   
    
Custom metrics: 
```
# HELP calling_devices_counter Information about calling devices
# TYPE calling_devices_counter counter
calling_devices_counter{ip="10.1.1.11",module="fdb",status="success"} 2
calling_devices_counter{ip="10.1.1.11",module="non_existed_module",status="failed"} 1
calling_devices_counter{ip="10.1.1.11",module="system",status="success"} 3
# HELP calling_devices_duration Spent time from device calling
# TYPE calling_devices_duration counter
calling_devices_duration{ip="10.1.1.11",module="fdb",status="success"} 1.4123849868774414
calling_devices_duration{ip="10.1.1.11",module="non_existed_module",status="failed"} 0.028479814529418945
calling_devices_duration{ip="10.1.1.11",module="system",status="success"} 0.4323270320892334
```

## Based on details
- PHP
- Slim framework
- roadrunner 
- composer 
 
<a name="php_dependencies"></a>
PHP dependencies:
- sockets
- yaml
- opcache
- snmp
- zip
- curl
- mbstring
- json

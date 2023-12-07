# Magento2 Module Gaiterjones GeoIP


  

## Main Functionalities

Add Geo IP functionality to Magento 2 using Maxmind GeoIP2 PHP API

    $geoData=$this->_getGeoData->geoIPLookup($ip);


Example usage includes an observer to restrict customer registration by Country.
  
  

## Installation

Copy installation files to app/code/Gaiterjones

composer require geoip2/geoip2:~2.0

Register at [https://www.maxmind.com/en/home](https://www.maxmind.com/en/home) and download *GeoLite2-City.mmdb*
Copy to server and update path at Gaiterjones\GeoIP\Model\GetGeoData

    $_pathToGeoIpDb='/var/www/dev/PAJ/Library/GeoIP/db/GeoLite2-City.mmdb';

Test with command line

bin/magento get:geo:ip --ip 1.1.1.1
  
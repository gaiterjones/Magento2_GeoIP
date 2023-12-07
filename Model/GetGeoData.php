<?php
/**
 *  Gaiterjones GetGEOIP Model
 *
 *  @category    Gaiterjones
 *  @package     Gaiterjones_GetGEOIP
 *  @author      modules@gaiterjones.com
 *
 */

namespace Gaiterjones\GeoIP\Model;
use Gaiterjones\GeoIP\Helper\Data;
use GeoIp2\Database\Reader as GeoIP2Reader;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * GetGeoData Model
 */
class GetGeoData
{
    /**
     * @var _remoteAddress
     */
    private $_remoteAddress;

    public function __construct(
        RemoteAddress $remoteAddress
    ) {
        $this->_remoteAddress = $remoteAddress;
    }

    public function geoIPLookup($ip=false)
	{


            if (!$ip)
            {
				$ip=$this->getUserIP();
            }

            try // get geoip info
			{

				// edit this path...
				//
				$_pathToGeoIpDb='/var/www/dev/PAJ/Library/GeoIP/db/GeoLite2-City.mmdb';

				if (!file_exists($_pathToGeoIpDb)){throw new \Exception('Cannot find Geo IP database.');}

				$_reader = new GeoIP2Reader($_pathToGeoIpDb);

				$_record = $_reader->city($ip);

				$_geoIPData=array(
					'ip' => $ip,
					'country' => $_record->country->name,
					'isocode' => $_record->country->isoCode,
					'region' => $_record->mostSpecificSubdivision->name,
					'city' => $_record->city->name,
					'postcode' => $_record->postal->code,
					'latitude' => $_record->location->latitude,
					'longitude' => $_record->location->longitude,
					'googlemap' => 'https://maps.google.com/?q='. $_record->location->latitude. ','. $_record->location->longitude,
					'isineurope' => $this->isInEurope($_record->country->isoCode),
                    'success' => true,
					'error' => 'No errors detected.'
				);

			}
			catch (\Exception $e)
			{
				$_geoIPData=array(
					'ip' => $ip,
					'country' => 'Not Found',
					'isocode' => 'Not Found',
					'region' => 'Not Found',
					'city' => 'Not Found',
					'postcode' => 'Not Found',
					'latitude' => 'Not Found',
					'longitude' => 'Not Found',
					'googlemap' => 'Not Found',
					'isineurope' => false,
                    'success' => false,
					'error' => $e->getMessage()
				);

				$this->log('EXCEPTION - '.$e->getMessage());
			}

		unset($_reader);

		return ($_geoIPData);

	}

	private function isInEurope($isocode)
	{
		$europe=array(
			'AX',
			'AL',
			'AD',
			'AT',
			'BY',
			'BE',
			'BA',
			'BG',
			'HR',
			'CZ',
			'DK',
			'EE',
			'FO',
			'FI',
			'FR',
			'DE',
			'GI',
			'GR',
			'GG',
			'VA',
			'HU',
			'IS',
			'IE',
			'IM',
			'IT',
			'JE',
			'LV',
			'LI',
			'LT',
			'LU',
			'MT',
			'MD',
			'MC',
			'ME',
			'NL',
			'MK',
			'NO',
			'PL',
			'PT',
			'RO',
			'RU',
			'SM',
			'RS',
			'SK',
			'SI',
			'ES',
			'SJ',
			'SE',
			'CH',
			'UA',
			'GB'
		);

		if (in_array($isocode, $europe)) { return true;}

		return false;
	}

	protected function getUserIP() {
		
		if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
				$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	public function log($text)
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/gaiterjones_geoip.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info($text);
	}

}

<?php

declare(strict_types=1);

namespace Gaiterjones\GeoIP\Console\Command\GeoIP;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State as AppState;
use Gaiterjones\GeoIP\Model\GetGeoData;

/**
 * Class GetGeoIP
 *
 */
class GetGeoIP extends Command
{
    private const OPTION_IP = 'ip';

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var AppState
     */
    private $_appState;

    /**
     * @var getGeoData
     */
    private $_getGeoData;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AppState $appState,
        GetGeoData $getGeoData
    ) {
        $this->_appState = $appState;
        $this->_scopeConfig = $scopeConfig;
        $this->_getGeoData = $getGeoData;
        parent::__construct();
    }

    /**
     * Initialization of the command
     *
     * @return void
     */
     protected function configure(): void
     {
         $this
             ->setName('get:geo:ip')
             ->setDescription('Get geo data from an IP address')
             ->addOption(
                 self::OPTION_IP,
                 null,
                 InputOption::VALUE_REQUIRED,
                 'IP Address'
             );
     }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip=$input->getOption(self::OPTION_IP);
        $geoData=$this->_getGeoData->geoIPLookup($ip);

        if (isset($geoData['success']) && $geoData['success'])
        {
            $output->writeln(
                '<info>GEO IP:'. $ip. ' - '. implode('-',$geoData) . '</info>'
            );

            $this->_getGeoData->log('Console Commaind GEO IP:'. $ip. ' - '. implode('-',$geoData));
            
        } else {
            
            $output->writeln(
                '<error>GEO IP:'. $ip. ' ERROR - '. $geoData['error'].'</error>'
            );

        }

        return 0;
        
    }

}

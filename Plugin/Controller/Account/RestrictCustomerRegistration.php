<?php
declare(strict_types=1);

namespace Gaiterjones\GeoIP\Plugin\Controller\Account;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Gaiterjones\GeoIP\Model\GetGeoData;

class RestrictCustomerRegistration
{

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var getGeoData
     */
    private $getGeoData;    

    /**
     * RestrictCustomerEmail constructor.
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        UrlFactory $urlFactory,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        GetGeoData $getGeoData

    )
    {
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->getGeoData = $getGeoData;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param \Closure $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure $proceed
    )
    {
        /** @var \Magento\Framework\App\RequestInterface $request */

        $geoData=$this->getGeoData->geoIPLookup();

        if (isset($geoData['success']) && $geoData['success'])
        {
            if (isset($geoData['isineurope']) && !$geoData['isineurope'])
            {
                // ip is not in europe
                $this->messageManager->addErrorMessage(
                    'Registration is disabled for your country.'
                );

                $this->getGeoData->log('Geo IP Customer Registration restricted for '. implode(' ',$geoData));

                $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
    
                return $resultRedirect->setUrl($defaultUrl);

            } else {
                
                $this->getGeoData->log('Geo IP Customer Registration allowed for '. implode(' ',$geoData));
            }
            
        } else {
            
            // Geo IP Error ???

        }

        return $proceed();
    }
}
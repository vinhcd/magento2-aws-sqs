<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;

class Config extends DataObject
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(ScopeConfigInterface $scopeConfig, array $data = [])
    {
        $this->scopeConfig = $scopeConfig;
        
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getAccessKey()
    {
        return $this->scopeConfig->getValue('vinhcd_aws/general/access_key');
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->scopeConfig->getValue('vinhcd_aws/general/secret');
    }

    /**
     * @return mixed
     */
    public function getDefaultRegion()
    {
        return $this->scopeConfig->getValue('vinhcd_aws/general/region');
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('vinhcd_aws/sqs/active') == 1;
    }

    /**
     * @return mixed
     */
    public function getOrderPlaceQueueUrl()
    {
        return $this->scopeConfig->getValue('vinhcd_aws/sqs/order_place_queue_url');
    }

    /**
     * @return mixed
     */
    public function getMaxMessagePerQueue()
    {
        $value = $this->scopeConfig->getValue('vinhcd_aws/sqs/max_message_per_queue');

        return $value ? $value : 10;
    }

    /**
     * @return mixed
     */
    public function getDeadQueueDays()
    {
        $value = $this->scopeConfig->getValue('vinhcd_aws/sqs/dead_queue_days');

        return $value ? $value : 3;
    }
}

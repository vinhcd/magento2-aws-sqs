<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @method string getQueueUrl()
 * @method $this setQueueUrl(string $value)
 * @method string getMessage()
 * @method $this setMessage(string $value)
 * @method string getCreatedAt()
 */
class SqsQueue extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Vinhcd\AwsSqs\Model\ResourceModel\SqsQueue::class);
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->setData('created_at', time());

        return parent::beforeSave();
    }
}

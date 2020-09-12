<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SqsQueue extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('vinhcd_aws_sqs_queue', 'entity_id');
    }
}

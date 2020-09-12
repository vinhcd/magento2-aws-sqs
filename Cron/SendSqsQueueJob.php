<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSqs\Model\Config\Config;
use Vinhcd\AwsSqs\Model\ResourceModel\SqsQueue\Collection;
use Vinhcd\AwsSqs\Model\ResourceModel\SqsQueue\CollectionFactory;
use Vinhcd\AwsSqs\Model\SqsAdapter;
use Vinhcd\AwsSqs\Model\SqsQueue;

class SendSqsQueueJob
{
    /**
     * @var SqsAdapter
     */
    protected $sqsAdapter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendSqsQueueJob constructor.
     * @param SqsAdapter $sqsAdapter
     * @param CollectionFactory $collectionFactory
     * @param Config $config
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        SqsAdapter $sqsAdapter,
        CollectionFactory $collectionFactory,
        Config $config,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->sqsAdapter = $sqsAdapter;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->removeDeadQueues();

        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $i = 0;
        /* @var SqsQueue $queue */
        foreach ($collection as $queue) {
            try {
                $this->sqsAdapter->sendMessage($queue->getQueueUrl(), $queue->getMessage());
                $queue->delete();
                $i++;
            } catch (\Exception $e) {
                $this->logger->critical('SQS Queue error:');
                $this->logger->critical($e->getMessage());
            }
            if ($i >= $this->config->getMaxMessagePerQueue()) {
                break;
            }
        }
    }

    /**
     * @return void
     */
    protected function removeDeadQueues()
    {
        /* @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $deadQueueDays = '-' . $this->config->getDeadQueueDays() . ' days';
        try {
            $collection->addFieldToFilter(
                'created_at',
                ['lt' => $this->dateTime->date('Y-m-d H:i:s', strtotime($deadQueueDays))]
            )->walk('delete');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

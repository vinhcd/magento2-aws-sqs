<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Observer;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Vinhcd\AwsSqs\Model\Config\Config;
use Vinhcd\AwsSqs\Model\SqsQueueFactory;

class AddOrderPlaceToSqsQueue implements ObserverInterface
{
    /**
     * @var SqsQueueFactory
     */
    protected $sqsQueueFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SqsQueueFactory $sqsQueueFactory
     * @param SerializerInterface $serializer
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        SqsQueueFactory $sqsQueueFactory,
        SerializerInterface $serializer,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->sqsQueueFactory = $sqsQueueFactory;
        $this->serializer = $serializer;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $queueUrl = $this->config->getOrderPlaceQueueUrl();

        if (!$this->config->isEnabled() || empty($queueUrl)) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $data = $order->getData();

        $data['items'] = [];
        /* @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            if (!($item->getProductType() == Type::TYPE_SIMPLE && $item->getParentItem())) {
                $data['items'][] = $item->getData();
            }
        }
        $data['addresses'] = [];
        foreach ($order->getAddresses() as $address) {
            $data['addresses'][] = $address->getData();
        }
        $data['payment'] = $order->getPayment()->getMethod();

        /* @var \Vinhcd\AwsSqs\Model\SqsQueue $sqsQueue */
        $sqsQueue = $this->sqsQueueFactory->create();
        try {
            $sqsQueue->setQueueUrl($queueUrl);
            $sqsQueue->setMessage($this->serializer->serialize($data));
            $sqsQueue->save();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}

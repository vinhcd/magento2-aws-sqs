<?php
/**
 * Copyright 2020 VinhCD Co.Ltd. or its affiliates. All Rights Reserved.
 *
 * Please contact vinhcd.co.ltd@gmail.com for more information
 */

namespace Vinhcd\AwsSqs\Model;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Vinhcd\AwsSqs\Model\Config\Config;

class SqsAdapter
{
    /**
     * @var SqsClient
     */
    protected $client;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SqsAdapter constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->client = new SqsClient([
            'version'     => 'latest',
            'region'      => $config->getDefaultRegion(),
            'credentials' => [
                'key'    => $config->getAccessKey(),
                'secret' => $config->getSecret()
            ],
        ]);
    }

    /**
     * @return \Aws\Result
     * @throws AwsException
     */
    public function getQueues()
    {
        return $this->client->listQueues();
    }

    /**
     * @param string $queueUrl
     * @param string $body
     * @param array $attributes
     * @param int $delay
     * @return void
     * @throws AwsException
     */
    public function sendMessage($queueUrl, $body, $attributes = [], $delay = 0)
    {
        $params = [
            'DelaySeconds' => $delay,
            'MessageAttributes' => $attributes,
            'MessageBody' => $body,
            'QueueUrl' => $queueUrl
        ];
        $this->client->sendMessage($params);
    }
}

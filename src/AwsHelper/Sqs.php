<?php
/**
 *
 * Author: mav peri
 *
 */
namespace Mph2labs\AwsHelper;

use Aws\Sdk;

class Sqs
{
    protected $client;
    protected $queues;
    protected $region;
    protected $key;
    protected $secret;

    public $data;
    public $csv;

    /**
     *
     * AwsSqsHelper constructor.
     *
     * @param $region
     * @param $key
     * @param $secret
     */
    public function __construct($region, $key, $secret)
    {
        //initialise
        $this->region = $region;
        $this->key = $key;
        $this->secret = $secret;
        $this->data = [];
        $this->csv = "";

        //get data
        $this->getClient();
        $this->getAllQueues();
        $this->getCSV();
    }

    public function getClient()
    {
        $sdk = new \Aws\Sdk([
                'version' => 'latest',
                'region' => $this->region,
                'credentials' => [
                    'key' => $this->key,
                    'secret' => $this->secret,
                ]
            ]
        );
        // Get the client from the builder by namespace
        $this->client = $sdk->createSqs();
    }

    /**
     * get a list of all queues
     */
    private function getAllQueues()
    {
        $result = $this->client->listQueues();
        foreach ($result->get('QueueUrls') as $queueUrl) {
            $this->getQueueVisible($queueUrl);
        }
    }

    /**
     *
     * get values of all visible messages
     *
     * @param $queueURL
     */
    private function getQueueVisible($queueURL)
    {
        $result = $this->client->getQueueAttributes(
            [
                'QueueUrl' => $queueURL, // QueueUrl is required
                'AttributeNames' => ['ApproximateNumberOfMessages']
            ]
        );
        $this->data[$queueURL]['visibleMessages'] = $result->get('Attributes')['ApproximateNumberOfMessages'];
        $this->data[$queueURL]['queue'] = substr($queueURL, strrpos($queueURL, '/') + 1);
    }

    /**
     * gets the csv string to save the file
     */
    private function getCSV()
    {
        $lines[] = "queue,messages visbile";
        foreach ($this->data as $key => $queue) {
            if ($queue['visibleMessages'] > 0) {
                $lines[] = $queue['queue'] . "," . $queue['visibleMessages'];
            }
        }
        $this->csv = implode("\n", $lines);
        //you can now store the csv file somewhere ...
    }

    /**
     *
     * returns the number of visible messages for a queue
     *
     * @param $queueName
     * @return bool|integer
     */
    public function getVisibleMessagesForQueue($queueName){
          $result = false;
          foreach($this->data as $key=>$queue){
              if($queue['queue'] === $queueName){
                  $result = $queue['visibleMessages'];
              }
          }
          return $result;
    }

    /**
     *
     * check if a queue exists or not
     *
     * @param $queueName
     * @return bool
     */
    public function getQueueExists($queueName){
        $result = false;
        foreach($this->data as $key=>$queue){
            if($queue['queue'] === $queueName){
                $result = true;
            }
        }
        return $result;
    }
}
# sqs-helper

A simple helper class to work with SQS queue

## install 

Best to install using composer 

```bash
composer install mph2labs/aws-sqs-helper
```

alternatively you can also add the following to your composer.json
```
"mph2labs/aws-sqs-helper": "dev-master"
```

## usage
Constructor expects AWS region, key, secret 

### available properties
The class has a number of properties containing data from SQS

* data

   Associative array with a list of your queues and their values. The full queue endpoint is the array key and the number of visible messages and queue name is returned.   

* csv

   A csv version of each queue that can be used for exporting   

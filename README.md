# rabbitmq-php-app
This is a simple cloudfoundry rabbitmq PHP app that uses the php-amqplib(https://github.com/php-amqplib/php-amqplib).

## Requirements
- Already have a rabbitmq service created
- Updated RMQ_SERVICE in manifest.yml to your specific version of service (ex. p-rabbitmq-35)

## Usage
```
cf push
```
Visit the webpage and if it says "OK" that means your rabbitmq is working

# symfony.bundle.api-hrottler

Adds a service to throttle API calls

## Test

`phpunit` or `vendor/bin/phpunit`

coverage reports will be available in `var/coverage`

## Requirements

1. Redis

## Use

```php
use Jalismrs\Symfony\Bundle\ApiThrottlerBundle\ApiThrottler;

class SomeApiClass {
    private ApiThrottler $apiThrottler;

    public function __construct(
        ApiThrottler $apiThrottler
    ) {
        $this->apiThrottler = $apiThrottler;
        
        /*
         * register rate limits HERE
         * https://packagist.org/packages/maba/gentle-force
         */
         $this->apiThrottler->registerRateLimits(
            'useCase',
            [],
         );
    }
    
    public function someApiCall() {
        $this->apiThrottler->waitAndIncrease(
            'useCase',
            'identifier',
        );
        
        // api call HERE
    }
}
```

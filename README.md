# symfony.bundle.api-throttler

Adds a service to throttle API calls

## Test

`phpunit` or `vendor/bin/phpunit`

coverage reports will be available in `var/coverage`

## Requirements

1. Redis

## Use

```php
use Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\ApiThrottler;

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
            'useCaseKey', // string $useCaseKey
            [],           // array  $rateLimits
         );
    }
    
    public function someApiCall(): void {
        $this->apiThrottler->waitAndIncrease(
            'useCaseKey', // string   $useCaseKey
            'identifier', // string   $identifier
            4,            // int|null $cap
        );
        
        // api call HERE
    }
}
```

## Configuration

Try limits can be configured:
* for all calls
* for all 'useCaseKey' calls
* for all 'useCaseKey.identifier' calls
* for a specific call with $cap parameter

```yaml
# config/packages/jalismrs_api_throttler.yaml

jalismrs_api_throttler:
    cap: 1
    caps:
        useCaseKey: 2
        useCaseKey.identifier: 0
```

## Environment
```dotenv
REDIS_HOST='REDIS_HOST'
```

<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle;

use Exception;
use Maba\GentleForce\Exception\RateLimitReachedException;
use Maba\GentleForce\RateLimitProvider;
use Maba\GentleForce\ThrottlerInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use function random_int;
use function usleep;

/**
 * Class ApiThrottler
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle
 */
class ApiThrottler
{
    /**
     * cap
     *
     * @var int
     */
    private int   $cap;
    /**
     * caps
     *
     * @var array|int[]
     */
    private array $caps;
    
    /**
     * rateLimitProvider
     *
     * @var \Maba\GentleForce\RateLimitProvider
     */
    private RateLimitProvider $rateLimitProvider;
    /**
     * throttler
     *
     * @var \Maba\GentleForce\Throttler|\Maba\GentleForce\ThrottlerInterface
     */
    private ThrottlerInterface $throttler;
    
    /**
     * ApiThrottler constructor.
     *
     * @param \Maba\GentleForce\RateLimitProvider  $rateLimitProvider
     * @param \Maba\GentleForce\ThrottlerInterface $throttler
     * @param int                                  $cap
     * @param array                                $caps
     */
    public function __construct(
        RateLimitProvider $rateLimitProvider,
        ThrottlerInterface $throttler,
        int $cap,
        array $caps
    ) {
        $this->cap               = $cap;
        $this->caps              = $caps;
        $this->rateLimitProvider = $rateLimitProvider;
        $this->throttler         = $throttler;
    }
    
    /**
     * registerRateLimits
     *
     * @param string $useCaseKey
     * @param array  $rateLimits
     *
     * @return void
     */
    public function registerRateLimits(
        string $useCaseKey,
        array $rateLimits
    ) : void {
        $this->rateLimitProvider->registerRateLimits(
            $useCaseKey,
            $rateLimits
        );
    }
    
    /**
     * waitAndIncrease
     *
     * @param string   $useCaseKey
     * @param string   $identifier
     * @param int|null $cap
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     */
    public function waitAndIncrease(
        string $useCaseKey,
        string $identifier,
        int $cap = null
    ) : void {
        $loop = 0;
        
        $cap = $cap
            ?? $this->caps["{$useCaseKey}.{$identifier}"]
            ?? $this->caps[$useCaseKey]
            ?? $this->cap;
        
        do {
            ++$loop;
            
            try {
                $this->throttler->checkAndIncrease(
                    $useCaseKey,
                    $identifier
                );
                $loop = $cap;
            } catch (RateLimitReachedException $rateLimitReachedException) {
                try {
                    $epsilon = random_int(
                        100,
                        1000
                    );
                } catch (Exception $exception) {
                    $epsilon = 666;
                }
    
                $waitInSeconds = (int)$rateLimitReachedException->getWaitForInSeconds();
                
                if ($loop === $cap) {
                    throw new TooManyRequestsHttpException(
                        $waitInSeconds,
                        'Loop limit was reached',
                        $rateLimitReachedException
                    );
                }
                
                usleep(1000000 * $waitInSeconds + $epsilon);
            }
        } while ($loop !== $cap);
    }
    
    /**
     * decrease
     *
     * @param string $useCaseKey
     * @param string $identifier
     *
     * @return void
     */
    public function decrease(
        string $useCaseKey,
        string $identifier
    ) : void {
        $this->throttler->decrease(
            $useCaseKey,
            $identifier
        );
    }
    
    /**
     * reset
     *
     * @param string $useCaseKey
     * @param string $identifier
     *
     * @return void
     */
    public function reset(
        string $useCaseKey,
        string $identifier
    ) : void {
        $this->throttler->reset(
            $useCaseKey,
            $identifier
        );
    }
}

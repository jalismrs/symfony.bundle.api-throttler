<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle;

use Jalismrs\Symfony\Bundle\JalismrsApiThrottlerBundle\DependencyInjection\Configuration;
use Maba\GentleForce\Exception\RateLimitReachedException;
use Maba\GentleForce\RateLimitProvider;
use Maba\GentleForce\ThrottlerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use function array_combine;
use function array_map;
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
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     * @param \Maba\GentleForce\RateLimitProvider                                       $rateLimitProvider
     * @param \Maba\GentleForce\ThrottlerInterface                                      $throttler
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        RateLimitProvider $rateLimitProvider,
        ThrottlerInterface $throttler
    ) {
        $caps       = $parameterBag->get(Configuration::CONFIG_ROOT . '.caps');
        $capsKeys   = array_map(
            static function(
                array $cap
            ) : string {
                $identifier = $cap['identifier'] ?? '';
                $useCase    = $cap['use_case'] ?? '';
                
                return self::buildKey($useCase, $identifier);
            },
            $caps
        );
        $capsValues = array_map(
            static function(
                array $cap
            ) : int {
                return (int)$cap['cap'];
            },
            $caps
        );
        
        $this->rateLimitProvider = $rateLimitProvider;
        $this->throttler         = $throttler;
        
        $this->cap  = $parameterBag->get(Configuration::CONFIG_ROOT . '.cap');
        $this->caps = array_combine(
            $capsKeys,
            $capsValues,
        );
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
    
    private static function buildKey(
        string $useCaseKey,
        string $identifier
    ): string {
        return "{$useCaseKey}.{$identifier}";
    }
    
    /**
     * waitAndIncrease
     *
     * @param string $useCaseKey
     * @param string $identifier
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     */
    public function waitAndIncrease(
        string $useCaseKey,
        string $identifier
    ) : void {
        $loop = 0;
        
        $key = self::buildKey($useCaseKey, $identifier);
        $cap = $this->caps[$key] ?? $this->cap;
        
        while ($loop !== $cap) {
            try {
                $this->throttler->checkAndIncrease(
                    $useCaseKey,
                    $identifier
                );
                $loop = $cap;
            } catch (RateLimitReachedException $rateLimitReachedException) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $epsilon = random_int(
                    100,
                    1000
                );
                
                $waitInSeconds = (int)$rateLimitReachedException->getWaitForInSeconds();
                
                ++$loop;
                if ($loop === $cap) {
                    throw new TooManyRequestsHttpException(
                        $waitInSeconds,
                        'Loop limit was reached',
                        $rateLimitReachedException
                    );
                }
                
                usleep(1000000 * $waitInSeconds + $epsilon);
            }
        }
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

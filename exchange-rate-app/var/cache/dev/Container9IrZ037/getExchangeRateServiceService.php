<?php

namespace Container9IrZ037;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getExchangeRateServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Services\ExchangeRateService' shared autowired service.
     *
     * @return \App\Services\ExchangeRateService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Services/ExchangeRateService.php';

        return $container->services['App\\Services\\ExchangeRateService'] = new \App\Services\ExchangeRateService();
    }
}
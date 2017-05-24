<?php

namespace ServiceProvider;

use Evaneos\Archi\DataAccess\PokemonDataAccess;
use Evaneos\Archi\Services\PokemonService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class PokemonServiceProvider
 *
 * @package ServiceProvider
 **/
class PokemonServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        // Ugly registration ... but don't care
        $app['application.dataAccess.pokemon'] = function () use ($app) {
            return new PokemonDataAccess($app['db']);
        };

        $app['application.service.pokemon'] = function () use ($app) {
            return new PokemonService($app['application.dataAccess.pokemon']);
        };
    }
}

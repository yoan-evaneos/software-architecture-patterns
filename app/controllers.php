<?php

use ControllerProvider\PokemonControllerProvider;
use ServiceProvider\ControllerServiceProvider;
use ServiceProvider\PokemonServiceProvider;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

/** @var Application $app */
$app->register(new PokemonServiceProvider());
$app->register(new ControllerServiceProvider());

// Pokemon
$app->mount('/', new PokemonControllerProvider());

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    return new Response($e->getMessage() . PHP_EOL . $e->getTraceAsString(), $code);
});
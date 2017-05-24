<?php

namespace Evaneos\Archi\Controllers;

use Doctrine\DBAL\DBALException;
use Evaneos\Archi\Services\PokemonService;
use Evaneos\Archi\ValueObjects\Pokemon;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PokemonController
{
    /**
     * @var PokemonService
     */
    private $pokemonService;

    /**
     * PokemonController constructor.
     *
     * @param PokemonService $pokemonService
     */
    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws DBALException
     */
    public function pokedex(Request $request)
    {
        $pokemons = $this->pokemonService->getAll();
        return new JsonResponse([$pokemons]);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
     * @throws DBALException
     */
    public function getInformation($uuid)
    {
        $pokemon = $this->pokemonService->getPokemonByUuid($uuid);

        if ($pokemon === false) {
            return new JsonResponse(new \stdClass(), 404);
        }

        return new JsonResponse($pokemon);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function capture(Request $request)
    {
        $uuid = (string)Uuid::uuid4();
        $type = $request->get('type');
        $level = (int)$request->get('level');


        $pokemon = new Pokemon($uuid, $type, $level);

        $this->pokemonService->capturePokemon($pokemon);

        return new JsonResponse([
            'uuid' => $uuid,
            'type' => $type,
            'level' => $level
        ]);
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function evolve($uuid)
    {
        $pokemon = $this->pokemonService->getPokemonByUuid($uuid);

        return new JsonResponse($pokemon);
    }


}

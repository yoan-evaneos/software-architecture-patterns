<?php

namespace Evaneos\Archi\Controllers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PokemonController
{
    /** @var Connection */
    private $connection;

    /**
     * PokemonController constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
        $sql = 'SELECT uuid, type, level FROM pokemon.collection';
        $query = $this->connection->query($sql);

        return new JsonResponse([$query->fetchAll()]);
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
        $pokemon = $this->getPokemonByUuid($uuid);

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

        // TODO check type exists
        if ($level <= 0) {
            throw new InvalidArgumentException('Pokemon\'s level min level is 1');
        }
        if ($level > 30) {
            throw new InvalidArgumentException('Pokemon\'s level could not exceed 30');
        }

        if (!in_array($type, ['pikachu', 'salameche', 'carapuce', 'bulbizare'])) {
            throw new InvalidArgumentException('Unknown Pokemon\'s type !!! CALL PROF CHEN RIGHT NOOOW');
        }

        $sql = 'INSERT INTO pokemon.collection (uuid, type, level) VALUES (:uuid, :type, :level)';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $uuid);
        $query->bindValue('type', $type);
        $query->bindValue('level', $level);
        $query->execute();

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
        $pokemon = $this->getPokemonByUuid($uuid);

        if ($pokemon['level'] < 7) {
            throw new InvalidArgumentException('Oh maaaan ! This pokemon is too young to evolve :(');
        }
        if ($pokemon['level'] > 30) {
            throw new InvalidArgumentException('Pokemon\'s level could not exceed level 30');
        }

        switch (true) {
            case $pokemon['type'] === "salameche" && $pokemon['level'] >= 7:
                $this->evolvePokemon($pokemon, 'reptincele');
                break;
            case $pokemon['type'] === "reptincele" && $pokemon['level'] >= 15;
                $this->evolvePokemon($pokemon, 'dracofeu');
                break;
            case $pokemon['type'] === "carapuce" && $pokemon['level'] >= 7:
                $this->evolvePokemon($pokemon, 'carabaffe');
                break;
            case $pokemon['type'] === "carabaffe" && $pokemon['level'] >= 15;
                $this->evolvePokemon($pokemon, 'tortank');
                break;
        }
        return $this->getInformation($uuid);
    }

    /**
     * @param array $pokemon
     * @param $newType
     */
    private function evolvePokemon(array $pokemon, $newType)
    {
        $sql = 'UPDATE pokemon.collection SET type = :type WHERE uuid = :uuid';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $pokemon['uuid']);
        $query->bindValue('type', $newType);
        $query->execute();
    }

    /**
     * @param $uuid
     *
     * @return mixed
     */
    private function getPokemonByUuid($uuid)
    {
        $sql = 'SELECT uuid, type, level FROM pokemon.collection WHERE uuid = :uuid';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $uuid);
        $query->execute();

        $pokemon = $query->fetch();
        return $pokemon;
    }
}

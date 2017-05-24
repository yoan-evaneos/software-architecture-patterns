<?php

namespace Evaneos\Archi\DataAccess;

use Doctrine\DBAL\Driver\Connection;

/**
 * Class DataAccess
 *
 * @package Evaneos\Archi\DataAccess
 **/
class PokemonDataAccess
{
    /**
     * PokemonService constructor.
     *
     * @param \Doctrine\DBAL\Driver\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $sql = 'SELECT uuid, type, level FROM pokemon.collection';
        $query = $this->connection->query($sql);
        return $query->fetchAll();
    }

    /**
     * @param $uuid
     *
     * @return mixed
     */
    public function getByUuid($uuid)
    {
        $sql = 'SELECT uuid, type, level FROM pokemon.collection WHERE uuid = :uuid';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $uuid);
        $query->execute();

        return $query->fetch();
    }

    /**
     * @param string $uuid
     * @param string $type
     * @param int $level
     */
    public function insert($uuid, $type, $level) {
        $sql = 'INSERT INTO pokemon.collection (uuid, type, level) VALUES (:uuid, :type, :level)';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $uuid);
        $query->bindValue('type', $type);
        $query->bindValue('level', $level);
        $query->execute();
    }

    /**
     * @param string $uuid
     * @param string $type
     */
    public function updateType($uuid, $type) {
        $sql = 'UPDATE pokemon.collection SET type = :type WHERE uuid = :uuid';
        $query = $this->connection->prepare($sql);
        $query->bindValue('uuid', $uuid);
        $query->bindValue('type', $type);
        $query->execute();
    }
}

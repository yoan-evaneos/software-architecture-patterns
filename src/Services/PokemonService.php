<?php

namespace Evaneos\Archi\Services;

use Doctrine\Common\Proxy\Exception\UnexpectedValueException;
use Evaneos\Archi\DataAccess\PokemonDataAccess;
use Evaneos\Archi\ValueObjects\Pokemon;
use InvalidArgumentException;

/**
 * Class PokemonService
 **/
class PokemonService
{
    /**
     * @var \Evaneos\Archi\DataAccess\PokemonDataAccess
     */
    private $dataAccess;

    /**
     * PokemonService constructor.
     *
     * @param \Evaneos\Archi\DataAccess\PokemonDataAccess $dataAccess
     *
     */
    public function __construct(PokemonDataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    /**
     * @return Pokemon[]
     */
    public function getAll()
    {
        $result = $this->dataAccess->getAll();

        $pokemons = [];
        foreach ($result as $pokemon) {
            $pokemons[] = $this->hidrate($pokemon);
        }
        return $pokemons;
    }

    /**
     * @param string $uuid
     *
     * @return Pokemon
     */
    public function getPokemonByUuid($uuid)
    {
        $result = $this->dataAccess->getByUuid($uuid);

        return $this->hidrate($result);
    }

    /**
     * @param Pokemon $pokemon
     *
     * @return Pokemon
     */
    public function capturePokemon(Pokemon $pokemon)
    {
        $this->dataAccess->insert($pokemon->getUuid(), $pokemon->getType(), $pokemon->getLevel());
        return $this->getPokemonByUuid($pokemon->getUuid());
    }

    /**
     * @param Pokemon $pokemon
     *
     * @return Pokemon
     */
    public function evolvePokemon(Pokemon $pokemon)
    {
        if ($pokemon->getLevel() < 7) {
            throw new InvalidArgumentException('Oh maaaan ! This pokemon is too young to evolve :(');
        }
        if ($pokemon->getLevel() > 30) {
            throw new InvalidArgumentException('Oh maaaan ! This pokemon is too old to evolve :(');
        }

        switch (true) {
            case $pokemon->getType() === "salameche" && $pokemon->getLevel() >= 7:
                $this->evolve($pokemon, 'reptincele');
                break;
            case $pokemon->getType() === "reptincele" && $pokemon->getLevel() >= 15;
                $this->evolve($pokemon, 'dracofeu');
                break;
            case $pokemon->getType() === "carapuce" && $pokemon->getLevel() >= 7:
                $this->evolve($pokemon, 'carabaffe');
                break;
            case $pokemon->getType() === "carabaffe" && $pokemon->getLevel() >= 15;
                $this->evolve($pokemon, 'tortank');
                break;
            case $pokemon->getType() === "pikachu" && $pokemon->getLevel() >= 7:
                $this->evolve($pokemon, 'raichu');
                break;
            default:
                throw new UnexpectedValueException(
                    sprintf("Call Pr. Chen again ... We don't know how to evolve %s", $pokemon->getType()
                    )
                );
                break;
        }

        return $this->getPokemonByUuid($pokemon->getUuid());
    }


    /**
     * @param array $pokemon
     *
     * @return Pokemon
     */
    private function hidrate(array $pokemon)
    {
        return new Pokemon($pokemon['uuid'], $pokemon['type'], (int)$pokemon['level']);
    }

    /**
     * @param Pokemon $pokemon
     * @param string $newType
     */
    private function evolve(Pokemon $pokemon, $newType)
    {
        $this->dataAccess->updateType($pokemon->getUuid(), $newType);
    }
}

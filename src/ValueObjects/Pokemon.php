<?php

namespace Evaneos\Archi\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class Pokemon
 **/
class Pokemon
{
    /**
     * @var string
     */
    public $uuid;
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $level;

    /**
     * Pokemon constructor.
     *
     * @param string $uuid
     * @param string $type
     * @param int $level
     */
    public function __construct($uuid, $type, $level)
    {

        Assert::string($uuid);
        Assert::lessThan($level, 30, 'Pokemon\'s level could not exceed 30');
        Assert::greaterThan($level, 0, 'Pokemon\' level could not lower than 1');
        Assert::string($type, 'Type must be a string');
        Assert::oneOf($type, $this->getPokemonList(), 'Unknown Pokemon\'s type : %s !!! CALL PROF CHEN RIGHT NOOOW');

        $this->uuid = $uuid;
        $this->type = $type;
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return array
     */
    private function getPokemonList()
    {
        return [
            'pikachu',
            'raichu',
            'salameche',
            'salameche',
            'dracofeu',
            'carapuce',
            'bulbizare',
            'aspicot',
            'chenipan',
            'roucool',
            'rattata',
        ];
    }
}

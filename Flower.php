<?php

/**
 * Class Flower
 */
class Flower
{
    const FLOWER_SPEC_REGEX = "/([a-z])(S|L)/";

    private $species;

    private $size;

    public function __construct($spec)
    {
        preg_match(self::FLOWER_SPEC_REGEX, $spec, $matches);

        $spec = trim($spec);

        if(($matches[0]??'') != $spec)
        {
            echo "Error : {$spec} is not a valid flower spec.\n";
            exit(1);
        }

        $this->species = $matches[1] ?? '';

        $this->size = $matches[2] ?? '';
    }

    /**
     * @return string - flower species
     */
    public function getSpecies() : string
    {
        return $this->species;
    }

    /**
     * @return string - flower size
     */
    public function getSize() : string
    {
        return $this->size;
    }

}
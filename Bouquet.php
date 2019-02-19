<?php

/**
 * Class Bouquet
 */
class Bouquet {

    const BOUQUET_SPEC_GROUPS_REGEX = "/([A-Z]{1})([S|L]{1})([1-9]{1}[0-9]*[0-9a-z]*[a-z]{1})([1-9]{1}[0-9]*)/";

    private $name;

    private $size;

    private $spec;

    private $flowers;

    private $flowersSpec;

    private $totalRequiredFlowers;

    public function __construct(string $spec)
    {
        $this->flowers = new DataKeyOrganizer();

        $this->spec = trim($spec);

        preg_match(self::BOUQUET_SPEC_GROUPS_REGEX, $this->spec, $matches);

        if(($matches[0]??'') !== $this->spec)
        {
            echo "Error : {$this->spec} is not a valid bouquet spec.\n";
            exit(1);
        }

        $this->name = $matches[1] ?? '';

        $this->size = $matches[2] ?? '';

        $this->flowersSpec =  $matches[3] ?? '';

        $this->totalRequiredFlowers = $matches[4] ?? 0;

    }

    /**
     * @param Flower $flower
     * insert a flower in the suitable bouquet key.
     */
    public function insertFlower(Flower $flower) : void
    {
        $this->flowers->attach($flower->getSpecies(), $flower);
    }

    /**
     * @return string : string
     * return bouquet name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     * return bouquet size
     */
    public function getSize() : string
    {
        return $this->size;
    }

    /**
     * @return int
     * return bouquet's total required flowers
     */
    public function getTotalRequiredFlowers() : int
    {
        return $this->totalRequiredFlowers;
    }

    /**
     * @return DataKeyOrganizer
     * returns actual bouquet's flowers.
     */
    public function getFlowers() : DataKeyOrganizer
    {
        return $this->flowers;
    }

    /**
     * @return int
     *  returns total actual bouquet's flowers.
     */
    public function getTotalFlowers() : int
    {
        return $this->flowers->count();
    }

    /**
     * @return string
     * magic function to stringify bouquet
     */
    public function __toString() : string
    {
        return $this->getName().$this->getSize().$this->flowers."\n";
    }

    /**
     * @return string
     * return bouquet flower's spec
     */
    public function getFlowersSpec() : string
    {
        return $this->flowersSpec;
    }


}

<?php

/**
 * Class MatchingAlgorithm
 * flower matching algorithm to implement the flower matching interface.
 */
class MatchingAlgorithm implements FlowerMatchingInterface {

    const MAX_STORAGE_CAPACITY = 256;
    const POSITIVE_NUMBER_REGEX = "/[1-9]{1}[0-9]*/";
    const ONE_SMALL_LETTER_REGEX = "/[a-z]{1}/";
    const EXTRA_FLOWER_SLOT_KEY = "extra";

    private $bouquets;

    private $notMatchedFlowers;

    private $capacity;

    private $bouquetsSlots;

    public function __construct()
    {
        $this->bouquets = new DataKeyOrganizer();
        $this->notMatchedFlowers = new DataKeyOrganizer();
        $this->bouquetsSlots = array();
        $this->capacity = 0;
    }

    /**
     * @param Bouquet $bouquet
     * @return string
     * return hash unique id for the given bouquet.
     */
    public function getBouquetHash(Bouquet $bouquet) : string
    {
        return spl_object_hash($bouquet);
    }

    /**
     * @param Bouquet $bouquet
     * @return int
     * return total slots of given bouquet.
     */
    public function getBouquetTotalSlots(Bouquet $bouquet) : int
    {
        return array_sum($this->bouquetsSlots[$this->getBouquetHash($bouquet)]);
    }

    /**
     * @param Bouquet $bouquet
     * @return bool
     * return true if all bouquet slots are used, false otherwise.
     */
    public function isBouquetFinished(Bouquet $bouquet) : bool
    {
        return ($this->getBouquetTotalSlots($bouquet) == 0) ? true : false;
    }

    /**
     * @param Bouquet $bouquet
     * @param string $species
     * reduce flower species slots of given bouquet.
     * and check if the bouquet is ready then detach the bouqet from
     * organizer and echo the bouquet out.
     */
    public function reduceFlowerSlots(Bouquet $bouquet, string $species) : void
    {
        $this->bouquetsSlots[$this->getBouquetHash($bouquet)][$species]--;

        if ($this->isBouquetFinished($bouquet)) {
            $this->bouquets->detach($bouquet);
            $this->setCapacity($this->getCapacity() - $bouquet->getTotalFlowers());
            echo $bouquet;
        }
    }

    /**
     * @param Bouquet $bouquet
     * @param string $species
     * @return int
     * return specific flower species slots of a given bouquet
     */
    public function getBouquetSpeciesSlots(Bouquet $bouquet, string $species) : int
    {
        return $this->bouquetsSlots[$this->getBouquetHash($bouquet)][$species] ?? 0;
    }

    /**
     * @param Bouquet $bouquet
     * @return int
     * return extra slots of a given bouquet
     */
    public function getBouquetExtraFlowersSlots(Bouquet $bouquet) : int
    {
        return $this->bouquetsSlots[$this->getBouquetHash($bouquet)][self::EXTRA_FLOWER_SLOT_KEY] ?? 0;
    }

    /**
     * @param Bouquet $bouquet
     * build flowers slots for given bouquet.
     */
    public function buildFlowerSlots(Bouquet $bouquet) : void
    {
        $hashId = $this->getBouquetHash($bouquet);

        $flowersSpec = $bouquet->getFlowersSpec();

        $totalRequiredFlowers = $bouquet->getTotalRequiredFlowers();

        $flowersSpecies = preg_split(self::POSITIVE_NUMBER_REGEX, $flowersSpec, NULL,  PREG_SPLIT_NO_EMPTY);

        $flowersSizes = preg_split(self::ONE_SMALL_LETTER_REGEX, $flowersSpec, NULL,  PREG_SPLIT_NO_EMPTY);

        $totalSpecFlowers = array_sum($flowersSizes);

        foreach ($flowersSpecies AS $key=>$val)
        {
            $this->bouquetsSlots[$hashId][$val] = (int)($flowersSizes[$key] ?? 0);
        }

        $this->bouquetsSlots[$hashId][self::EXTRA_FLOWER_SLOT_KEY] = $totalRequiredFlowers - $totalSpecFlowers;

    }

    /**
     * @param Bouquet $bouquet
     * push bouquet to bouqet organizer and build the flower slots
     * for the given bouquet.
     */
    public function pushToBouquets(Bouquet $bouquet) : void
    {
        $bouquetSize = $bouquet->getSize();
        $this->bouquets->attach($bouquetSize, $bouquet);
        $this->buildFlowerSlots($bouquet);
    }

    /**
     * check capacity. if capacity is full
     * stop processing and exit code of 1.
     */
    public function checkCapacity() : void
    {
        if($this->getCapacity() == self::MAX_STORAGE_CAPACITY)
        {
            echo "Error : the facility can not proceed with more than ".self::MAX_STORAGE_CAPACITY
                . " flowers.\n";
            exit(1);
        }
    }

    /**
     * @param Flower $flower
     * @return bool
     * this is the implemented FlowerMatchingInterface method.
     * it returns true if we could match a flower to a bouquet, false otherwise.
     * first it trying to match a flower to the bouquet with the same size
     * it sort bouquets by number of flowers slot ASC to get first the bouquet with the
     * min flowers slot in running time.
     * if the flower is a required species and we have a slot for it then insert it.
     * else if there is extra slots insert it.
     * else it means that there is no any bouquet can be matched store the flower in
     * not matched organizer in facility for future processing.
     */
    public function match(Flower $flower) : bool
    {
        $this->checkCapacity();

        $flowerSize = $flower->getSize();

        $flowerSpecies = $flower->getSpecies();

        $relevantBouquets = $this->bouquets->getKeyBlock($flowerSize);

        $this->sortArrayBySlots($relevantBouquets);

        foreach ($relevantBouquets AS $key=>$bouquet)
        {
            if($this->getBouquetSpeciesSlots($bouquet, $flowerSpecies))
            {
                $this->matchFlowerToBouquet($flower, $bouquet);
                $this->reduceFlowerSlots($bouquet, $flowerSpecies);
                return true;
            }
            else if($this->getBouquetExtraFlowersSlots($bouquet))
            {
                $this->matchFlowerToBouquet($flower, $bouquet);
                $this->reduceFlowerSlots($bouquet, self::EXTRA_FLOWER_SLOT_KEY);
                return true;
            }
        }

        $this->storeNotMatchedFlower($flower);
        return false;
    }

    /**
     * @param Flower $flower
     * @param Bouquet $bouquet
     * match flower to bouquet and increase storage capacity.
     */
    public function matchFlowerToBouquet(Flower $flower, Bouquet $bouquet) : void
    {
        $bouquet->insertFlower($flower);
        $this->increaseCapacity();
    }

    /**
     * increase storage capacity.
     */
    public function increaseCapacity() : void
    {
        $this->setCapacity($this->getCapacity() + 1);
    }

    /**
     * @param Flower $flower
     * attach flower to not matched flowers orgnaizer.
     * and increase capacity.
     */
    public function storeNotMatchedFlower(Flower $flower) : void
    {
        $this->notMatchedFlowers->attach($flower->getSpecies(), $flower);
        $this->increaseCapacity();
    }

    /**
     * @param Bouquet $b1
     * @param Bouquet $b2
     * @return int
     * compare function, to sort bouquets array by total slots.
     */
    public function compare(Bouquet $b1, Bouquet $b2) : int
    {
        $slots1 = $this->getBouquetTotalSlots($b1);
        $slots2 = $this->getBouquetTotalSlots($b2);
        if ($slots1 == $slots2) {
            return 0;
        }
        return ($slots1 < $slots2) ? -1 : 1;
    }

    /**
     * @param $array
     * sort bouquets array by total slots.
     */
    public function sortArrayBySlots(&$array) : void
    {
        usort($array, array($this, "compare"));
    }

    /**
     * @return int
     * return current storage capacity.
     */
    public function getCapacity() : int
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     */
    public function setCapacity($capacity) : void
    {
        $this->capacity = $capacity;
    }
}
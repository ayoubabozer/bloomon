<?php

/**
 * Class Facility
 */
class Facility
{
    private $flowerMathcing;

    private $readBouquetsFlag;

    private $readFlowersFlag;

    public function __construct(FlowerMatchingInterface $flowerMathcing)
    {
        $this->flowerMathcing = $flowerMathcing;
        $this->readBouquetsFlag = 1;
        $this->readFlowersFlag = 0;
    }

    /**
     * switch reading flags.
     */
    public function switchFlags() : void
    {
        if($this->readBouquetsFlag)
        {
            $this->readBouquetsFlag = 0;
            $this->readFlowersFlag = 1;
        }
        else
        {
            $this->readBouquetsFlag = 1;
            $this->readFlowersFlag = 0;
        }
    }

    /**
     * Start working from file.
     */
    public function startFromFile() : void
    {
        $file = fopen("input.txt","r");

        while(! feof($file))
        {
            if($this->readBouquetsFlag)
            {
                $bouquetSpec =  trim(fgets($file));
                if(! empty($bouquetSpec))
                {
                    $this->flowerMathcing->pushToBouquets(new Bouquet($bouquetSpec));
                }
                else
                {
                    $this->switchFlags();
                }
            }

            if($this->readFlowersFlag)
            {
                $flowerSpec =  trim(fgets($file));
                if(! empty($flowerSpec))
                {
                    $this->flowerMathcing->match(new Flower($flowerSpec));
                }
            }

        }

    }

    /**
     * Start working from inputs.
     */
    public function startFromInputs() : void
    {

        while( $bouquetSpec = trim(fgets(STDIN)) ){
            $this->flowerMathcing->pushToBouquets(new Bouquet($bouquetSpec));
        }

        while( $flowerSpec = trim(fgets(STDIN)) ) {
            $this->flowerMathcing->match(new Flower($flowerSpec));
        }

        exit(1);
    }

}
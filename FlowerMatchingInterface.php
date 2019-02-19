<?php

/**
 * Interface FlowerMatchingInterface
 * this interface can be implemented to provide an algorithm
 * for flower matching
 */
interface FlowerMatchingInterface {
    public function match(Flower $flower);
}
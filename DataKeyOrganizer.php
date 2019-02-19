<?php

/**
 * Class DataKeyOrganizer
 * this is a custom data structure for an array of objects.
 * based on key => array of objects
 */
class DataKeyOrganizer
{
    private $organizer;

    public function __construct()
    {
        $this->organizer = array();
    }

    /**
     * @param string $key
     * @param object $object
     * attach object to the organizer in the given key.
     */
    public function attach(string $key, object $object) : void
    {
        $this->organizer[$key][] = $object;
    }

    /**
     * @param object $object
     * remove object from organizer
     */
    public function detach(object $object) : void
    {
        foreach ($this->organizer as $key=>$block)
        {
            if(($objectKey = array_search($object, $block, true)) !== FALSE) {
                unset($this->organizer[$key][$objectKey]);
            }
        }
    }

    /**
     * @param string $key
     * @return array
     * return organizer block of data for the given key.
     */
    public function getKeyBlock(string $key) : array
    {
        return $this->organizer[$key] ?? [];
    }

    /**
     * @return iterable
     * return iterator of all the objects in the organizer
     */
    public function iterator() : iterable
    {
        $iterator = new AppendIterator;
        foreach ($this->organizer as $keyBlock)
        {
            $iterator->append(new ArrayIterator($keyBlock));
        }
        return $iterator;
    }

    /**
     * @return int
     * return total objects in organizer
     */
    public function count() : int
    {
        $count = 0;
        foreach ($this->organizer as $keyBlock) {
            $count+= count($keyBlock);
        }
        return $count;
    }

    /**
     * sort the organizer by keys.
     */
    public function sort() : void
    {
        ksort($this->organizer);
    }

    /**
     * @return string
     * magic funcion to stringify organizer
     */
    public function __toString() : string
    {
      $this->sort();
      $str = "";
      foreach ($this->organizer AS $key=>$block)
      {
          $str .= count($block).$key;
      }
      return $str;
    }
}
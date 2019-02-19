<?php

require_once "init.php";

/* Instantiate matching algorithm */
$algorithm = new MatchingAlgorithm();

/* Inject algorithm to facility */
$facility = new Facility($algorithm);

/* Start working .... */
$facility->startFromFile();
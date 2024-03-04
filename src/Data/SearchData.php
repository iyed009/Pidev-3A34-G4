<?php

namespace App\Data;

class SearchData
{
    /**
     * @var int
     */
    public $page = 1;
    /**
     * @var string
     */
    public $q = '';
    /**
     * @var array
     */
    public $categorieP = [];
    /**
     * @var null/integer
     */
    public $max;
    /**
     * @var null/integer
     */
    public $min;
}
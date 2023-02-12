<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Article extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => flase,
        'slug' => false,
    ];
}
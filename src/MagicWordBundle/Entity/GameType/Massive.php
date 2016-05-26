<?php

namespace MagicWordBundle\Entity\GameType;

use Doctrine\ORM\Mapping as ORM;
use MagicWordBundle\Entity\Game as Game;

/**
 * Massive.
 *
 * @ORM\Entity
 * @ORM\Table(name="game_type_massive")
 */
class Massive extends Game
{
    protected $discr = 'massive';

    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    public function getDiscr()
    {
        return $this->discr;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return Massive
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }
}

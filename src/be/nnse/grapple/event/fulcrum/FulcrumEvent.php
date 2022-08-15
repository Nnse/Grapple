<?php

declare(strict_types=1);

namespace be\nnse\grapple\event\fulcrum;

use be\nnse\grapple\object\Fulcrum;
use pocketmine\event\Event;
use pocketmine\player\Player;

abstract class FulcrumEvent extends Event
{
    /** @var Fulcrum */
    protected Fulcrum $fulcrum;

    /**
     * @return Fulcrum
     */
    public function getFulcrum() : Fulcrum
    {
        return $this->fulcrum;
    }

    /**
     * @return Player|null
     */
    public function getOwner() : ?Player
    {
        $owner = $this->fulcrum->getOwningEntity();
        if ($owner instanceof Player) return $owner;
        return null;
    }
}
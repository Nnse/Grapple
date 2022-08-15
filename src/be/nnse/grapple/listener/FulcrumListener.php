<?php

declare(strict_types=1);

namespace be\nnse\grapple\listener;

use be\nnse\grapple\event\fulcrum\FulcrumHitEvent;
use be\nnse\grapple\event\fulcrum\FulcrumRetrieveEvent;
use be\nnse\grapple\event\fulcrum\FulcrumSpawnEvent;
use be\nnse\grapple\GrappleConstants;
use be\nnse\grapple\PlaySoundTrait;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class FulcrumListener implements Listener
{
    use PlaySoundTrait;

    public function onSpawn(FulcrumSpawnEvent $event)
    {
        $fulcrum = $event->getFulcrum();
        $this->play($fulcrum->getPosition(), GrappleConstants::SOUND_FULCRUM_SPAWN);
    }

    public function onHit(FulcrumHitEvent $event)
    {
        $fulcrum = $event->getFulcrum();
        $owner = $event->getOwner();
        if ($owner instanceof Player) {
            $this->play($owner->getPosition(), GrappleConstants::SOUND_FULCRUM_HIT);
        }
        $this->play($fulcrum->getPosition(), GrappleConstants::SOUND_FULCRUM_HIT);
    }

    public function onRetrieve(FulcrumRetrieveEvent $event)
    {
        $fulcrum = $event->getFulcrum();
        $owner = $event->getOwner();
        if ($owner instanceof Player) {
            $this->play($owner->getPosition(), GrappleConstants::SOUND_FULCRUM_RETRIEVE);
        }
        $this->play($fulcrum->getPosition(), GrappleConstants::SOUND_FULCRUM_RETRIEVE);
    }
}
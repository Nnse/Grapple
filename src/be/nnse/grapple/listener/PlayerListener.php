<?php

declare(strict_types=1);

namespace be\nnse\grapple\listener;

use be\nnse\grapple\GrappleConstants;
use be\nnse\grapple\Main;
use be\nnse\grapple\object\Fulcrum;
use be\nnse\grapple\object\FulcrumData;
use be\nnse\grapple\PlaySoundTrait;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class PlayerListener implements Listener
{
    use PlaySoundTrait;

    public function onItemUse(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item->getId() != Main::getInstance()->getConfig()->get(GrappleConstants::CONFIG_ITEM_ID)) return;
        if ($item->getMeta() != Main::getInstance()->getConfig()->get(GrappleConstants::CONFIG_ITEM_META)) return;

        if (!FulcrumData::hasFulcrum($player)) {
            $fulcrum = Fulcrum::eject($player);
            FulcrumData::setFulcrum($player, $fulcrum);
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if (FulcrumData::hasFulcrum($player)) {
            FulcrumData::resetFulcrum($player);
        }
    }

    public function onTeleport(EntityTeleportEvent $event)
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) return;

        $from = $event->getFrom();
        $to = $event->getTo();
        if ($from->getWorld() === $to->getWorld()) return;

        if (FulcrumData::hasFulcrum($entity)) {
            FulcrumData::resetFulcrum($entity);
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if (FulcrumData::hasFulcrum($player)) {
            FulcrumData::resetFulcrum($player);
        }
    }
}
<?php

namespace be\nnse\grapple;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\Position;

trait PlaySoundTrait
{
    /**
     * @param Position $position
     * @param string $soundName
     * @return void
     */
    protected function play(Position $position, string $soundName) : void
    {
        $pk = new PlaySoundPacket();
        $pk->pitch = 1.0;
        $pk->volume = 0.5;
        $pk->soundName = $soundName;
        $pk->x = $position->getX();
        $pk->y = $position->getY();
        $pk->z = $position->getZ();

        foreach ($position->getWorld()->getPlayers() as $player) {
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }
}
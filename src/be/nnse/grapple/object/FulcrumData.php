<?php

declare(strict_types=1);

namespace be\nnse\grapple\object;

use be\nnse\grapple\object\Fulcrum;
use pocketmine\player\Player;

class FulcrumData
{
    /** @var Fulcrum[] */
    private static array $fulcrums = [];

    /**
     * Get fulcrum player has
     * @param Player $player
     * @return Fulcrum|null
     */
    public static function getFulcrum(Player $player) : ?Fulcrum
    {
        return self::$fulcrums[spl_object_id($player)] ?? null;
    }

    /**
     * Set fulcrum player ejected
     * @param Player $player
     * @param Fulcrum $fulcrum
     */
    public static function setFulcrum(Player $player, Fulcrum $fulcrum) : void
    {
        self::$fulcrums[spl_object_id($player)] = $fulcrum;
    }

    /**
     * Unset fulcrum player ejected
     * @param Player $player
     */
    public static function resetFulcrum(Player $player) : void
    {
        unset(self::$fulcrums[spl_object_id($player)]);
    }

    /**
     * Check player's fulcrum
     * @param Player $player
     * @return bool
     */
    public static function hasFulcrum(Player $player) : bool
    {
        return isset(self::$fulcrums[spl_object_id($player)]);
    }
}
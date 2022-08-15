<?php

declare(strict_types=1);

namespace be\nnse\grapple;

class GrappleConstants
{
    public const CONFIG_ITEM_ID = "item-id";
    public const CONFIG_ITEM_META = "item-meta";

    public const FULCRUM_MAX_DISTANCE = 40;
    public const FULCRUM_MIN_DISTANCE = 3;

    public const SOUND_FULCRUM_SPAWN = "item.trident.throw";
    public const SOUND_FULCRUM_HIT = "item.trident.hit_ground";
    public const SOUND_FULCRUM_RETRIEVE = "dig.ancient_debris";

    public const SOUND_DRAG = "item.trident.riptide_1";
}
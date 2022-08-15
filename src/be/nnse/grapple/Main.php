<?php

declare(strict_types=1);

namespace be\nnse\grapple;

use be\nnse\grapple\listener\FulcrumListener;
use be\nnse\grapple\listener\PlayerListener;
use be\nnse\grapple\object\Fulcrum;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Main extends PluginBase
{
    public function onEnable() : void
    {
        self::$instance = $this;

        EntityFactory::getInstance()->register(
            Fulcrum::class,
            function (World $world, CompoundTag $nbt) : Fulcrum {
                return new Fulcrum(EntityDataHelper::parseLocation($nbt, $world), null);
            },
            ["Fulcrum"],
            EntityLegacyIds::ARROW
        );

        if (!$this->checkConfigValidation()) {
            $this->saveResource("config.yml", true);
        }

        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
    }

    /**
     * @return bool
     */
    private function checkConfigValidation() : bool
    {
        if (!$this->getConfig()->exists(GrappleConstants::CONFIG_ITEM_ID)) return false;
        if (!$this->getConfig()->exists(GrappleConstants::CONFIG_ITEM_META)) return false;
        return true;
    }


    /** @var self */
    private static self $instance;

    public static function getInstance() : self
    {
        return self::$instance;
    }
}
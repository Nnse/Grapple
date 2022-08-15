<?php

declare(strict_types=1);

namespace be\nnse\grapple\object;

use be\nnse\grapple\GrappleConstants;
use be\nnse\grapple\Main;
use be\nnse\grapple\event\fulcrum\FulcrumHitEvent;
use be\nnse\grapple\event\fulcrum\FulcrumRetrieveEvent;
use be\nnse\grapple\event\fulcrum\FulcrumSpawnEvent;
use be\nnse\grapple\PlaySoundTrait;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\particle\MobSpawnParticle;

class Fulcrum extends Throwable
{
    use PlaySoundTrait;

    /** @var float */
    private float $distance = 0;

    /** @var float */
    private float $length = -1;

    /** @var float */
    private float $accelerate = 0.55;

    /** @var Entity|null */
    private ?Entity $grappledEntity = null;

    /** @var bool */
    private bool $grappling = false; // whether grappling now

    /** @var bool */
    private bool $isStuck = false; // whether grappled wall or player

    public static function getNetworkTypeId() : string
    {
        return EntityIds::ARROW;
    }

    public function spawnToAll() : void
    {
        parent::spawnToAll();

        $this->canCollide = false;
        Main::getInstance()->getScheduler()->scheduleDelayedTask(
            new ClosureTask(function () {
                $this->canCollide = true;
                $this->getNetworkProperties()->setLong(
                    EntityMetadataProperties::LEAD_HOLDER_EID,
                    $this->getOwningEntityId()
                );
            }),
            3
        );

        (new FulcrumSpawnEvent($this))->call();
    }

    protected function entityBaseTick(int $tickDiff = 1) : bool
    {
        $update = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if (!$owner instanceof Player) return $update;
        if ($this->isFlaggedForDespawn()) return $update;

        $oV = $owner->getLocation()->asVector3();
        $fV = $this->getLocation()->asVector3();
        $this->distance = $oV->distance($fV);

        if (!$this->grappling) {
            if (!$this->isStuck) {
                if ($this->distance > GrappleConstants::FULCRUM_MAX_DISTANCE) {
                    $this->flagForDespawn();
                }
            } else {
                $this->grappling = true;
                $dragMotion = ($this->getDragVector() ?? Vector3::zero())->normalize();
                $owner->setMotion($dragMotion->multiply($this->accelerate));

                $this->play($owner->getPosition(), GrappleConstants::SOUND_DRAG);
            }
            return $update;
        }

        $minDistance = GrappleConstants::FULCRUM_MIN_DISTANCE;
        if ($owner->isSneaking() || $this->distance > $this->length || $this->distance <= $minDistance) {
            $this->flagForDespawn();
            return $update;
        }

        $dragMotion = $this->getDragVector()->normalize();

        $dirV = $owner->getDirectionVector();
        $dirV->y = max(0, $dirV->y);
        $newMotion = $dragMotion->addVector($dirV->multiply(1.2));
        $owner->setMotion($newMotion->multiply($this->accelerate));

        $foot = $owner->getPosition()->add(0, 0.5, 0);
        $owner->getWorld()->addParticle($foot, new MobSpawnParticle());

        $this->accelerate += 0.005;

        return $update;
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult) : void
    {
        $this->blockHit = clone $blockHit;
        $this->isStuck = true;
        $this->length = $this->getOwningEntity()?->getPosition()->distance($this->getPosition()) ?? $this->distance;
        $this->setImmobile();

        (new FulcrumHitEvent($this))->call();
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void
    {
        $this->flagForDespawn();

        // TODO: Grappling other player
    }

    public function flagForDespawn() : void
    {
        $this->getNetworkProperties()->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, -1);

        $owner = $this->getOwningEntity();
        if ($owner instanceof Player) {
            FulcrumData::resetFulcrum($owner);
        }

        (new FulcrumRetrieveEvent($this))->call();

        parent::flagForDespawn();

    }

    /**
     * @return bool
     */
    public function isGrappling() : bool
    {
        return $this->grappling;
    }

    /**
     * @return bool
     */
    public function isStuck() : bool
    {
        return $this->isStuck;
    }

    /**
     * @return Vector3
     */
    public function getDragVector() : Vector3
    {
        $owner = $this->getOwningEntity();
        if (!($owner instanceof Player)) return Vector3::zero();

        $oV = $owner->getLocation()->asVector3();
        $aV = $this->getLocation()->asVector3();

        return $aV->subtractVector($oV);
    }


    /**
     * @param Player $player
     * @return self
     */
    public static function eject(Player $player) : self
    {
        $position = clone $player->getEyePos();
        $location = Location::fromObject($position, $player->getWorld());
        $fulcrum = new Fulcrum($location, $player);
        $fulcrum->setMotion($player->getDirectionVector()->multiply(2.5));
        $fulcrum->spawnToAll();
        return $fulcrum;
    }
}
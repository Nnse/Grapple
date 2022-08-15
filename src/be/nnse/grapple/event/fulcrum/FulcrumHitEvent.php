<?php

declare(strict_types=1);

namespace be\nnse\grapple\event\fulcrum;

use be\nnse\grapple\object\Fulcrum;

class FulcrumHitEvent extends FulcrumEvent
{
    public function __construct(Fulcrum $fulcrum)
    {
        $this->fulcrum = $fulcrum;
    }
}
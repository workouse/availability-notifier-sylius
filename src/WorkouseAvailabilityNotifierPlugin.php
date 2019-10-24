<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class WorkouseAvailabilityNotifierPlugin extends Bundle
{
    use SyliusPluginTrait;
}

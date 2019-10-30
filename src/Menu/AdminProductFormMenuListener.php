<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class AdminProductFormMenuListener
{
    public function addItems(ProductMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $product = $event->getProduct();

        if ($product->getId()) {
            $menu
                ->addChild('waiting_customers')
                ->setAttribute('template', '@WorkouseAvailabilityNotifierPlugin\Admin\waiting_customers.html.twig')
                ->setLabel('workouse_availability_notifier_plugin.ui.waiting_customers');
        }
    }
}

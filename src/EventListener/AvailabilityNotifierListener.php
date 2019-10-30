<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Product\Model\Product;
use Workouse\AvailabilityNotifierPlugin\Entity\AvailabilityNotifier;
use Workouse\AvailabilityNotifierPlugin\Entity\AvailabilityNotifierInterface;
use Workouse\AvailabilityNotifierPlugin\Repository\AvailabilityNotifierRepository;

class AvailabilityNotifierListener
{
    /** @var SenderInterface */
    private $mailSender;

    /** @var AvailabilityNotifierRepository */
    private $notifierRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        AvailabilityNotifierRepository $notifierRepository,
        SenderInterface $mailSender,
        EntityManagerInterface $entityManager
    ) {
        $this->mailSender = $mailSender;
        $this->notifierRepository = $notifierRepository;
        $this->entityManager = $entityManager;
    }

    public function sendNotifierVariant(ResourceControllerEvent $event)
    {
        /** @var ProductVariant $productVariant */
        $productVariant = $event->getSubject();
        $this->sendNotifier($productVariant);
    }

    public function sendNotifierProduct(ResourceControllerEvent $event)
    {
        /** @var Product $product */
        $product = $event->getSubject();
        if ($product->getVariants()->count() === 1) {
            $this->sendNotifier($product->getVariants()->first());
        }
    }

    private function sendNotifier(ProductVariant $productVariant)
    {
        if ($productVariant->isInStock()) {
            /** @var Product $product */
            $product = $productVariant->getProduct();

            $availabilityNotifiers = $this->notifierRepository->findBy([
                'product' => $product,
                'status' => false,
                'type' => AvailabilityNotifierInterface::EMAIL_TYPE,
            ]);

            /** @var AvailabilityNotifier $availabilityNotifier */
            foreach ($availabilityNotifiers as $availabilityNotifier) {
                $availabilityNotifier->setStatus(true);

                /** @var Customer $cutomer */
                $cutomer = $availabilityNotifier->getCustomer();

                $this->mailSender->send('product_stock_notifier', [$cutomer->getEmail()], [
                    'product' => $product,
                    'cutomer' => $cutomer,
                ]);
                $this->entityManager->flush();
            }
        }
    }
}

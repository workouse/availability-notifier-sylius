<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface AvailabilityNotifierInterface extends ResourceInterface
{
    public const EMAIL_TYPE = 1;

    public function getCustomer();

    public function setCustomer($customer);

    public function getProduct();

    public function setProduct($product);

    public function getStatus();

    public function setStatus(bool $status);

    public function getType();

    public function setType(int $type);

    public function getCreatedAt();

    public function setCreatedAt($created_at);
}

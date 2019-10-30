<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AvailabilityNotifierType extends AbstractResourceType
{
    /** @var Security */
    private $security;

    public function __construct(string $dataClass, array $validationGroups = [], Security $security)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer', EmailType::class, [
            'label' => 'workouse_availability_notifier_plugin.form.customer_email',
            'constraints' => [
                new NotBlank([
                    'message' => 'workouse_availability_notifier_plugin.customer_email.not_blank',
                ]),
                new Email(),
            ],
        ]);

        if ($this->security->getUser()) {
            $builder->remove('customer');
        }
    }

    public function getBlockPrefix(): string
    {
        return 'workouse_availability_notifier_plugin_form_availability_notifier';
    }
}

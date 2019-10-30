<?php

declare(strict_types=1);

namespace Workouse\AvailabilityNotifierPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Workouse\AvailabilityNotifierPlugin\Entity\AvailabilityNotifier;
use Workouse\AvailabilityNotifierPlugin\Entity\AvailabilityNotifierInterface;
use Workouse\AvailabilityNotifierPlugin\Form\Type\AvailabilityNotifierType;
use Workouse\AvailabilityNotifierPlugin\Repository\AvailabilityNotifierRepository;

class AvailabilityNotifierController
{
    /** @var TwigEngine */
    private $templatingEngine;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FactoryInterface */
    private $availabilityNotifierFactory;

    /** @var AvailabilityNotifierRepository */
    private $availabilityNotifierRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Security */
    private $security;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(
        TwigEngine $templatingEngine,
        FormFactoryInterface $formFactory,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        EntityManagerInterface $entityManager,
        FactoryInterface $availabilityNotifierFactory,
        AvailabilityNotifierRepository $availabilityNotifierRepository,
        ProductRepositoryInterface $productRepository,
        Security $security,
        ValidatorInterface $validator
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->formFactory = $formFactory;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->entityManager = $entityManager;
        $this->availabilityNotifierFactory = $availabilityNotifierFactory;
        $this->availabilityNotifierRepository = $availabilityNotifierRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->validator = $validator;
    }

    public function indexAction($productId): Response
    {
        $form = $this->formFactory->create(AvailabilityNotifierType::class);

        return $this->templatingEngine->renderResponse(
            '@WorkouseAvailabilityNotifierPlugin/_outOfStock.html.twig',
            ['form' => $form->createView(), 'productId' => $productId]
        );
    }

    public function newAction(Request $request, $productId): Response
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new NotFoundHttpException();
        }

        /** @var AvailabilityNotifier $availabilityNotifier */
        $availabilityNotifier = $this->availabilityNotifierFactory->createNew();
        $availabilityNotifier->setProduct($product);
        $availabilityNotifier->setType(AvailabilityNotifierInterface::EMAIL_TYPE);
        $form = $this->formFactory->create(AvailabilityNotifierType::class, $availabilityNotifier);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $availabilityNotifier = $form->getData();
            $customerEmail = $availabilityNotifier->getCustomer();

            if (!$this->security->getUser()) {
                $violations = $this->validator->validate($customerEmail, [
                    new Email(),
                    new NotBlank(),
                ]);
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }

                if (0 < count($errors)) {
                    return new JsonResponse(['errors' => $errors], 400);
                }
            }

            $availabilityNotifier->setCustomer($this->getUserByEmail($customerEmail));

            if (!$this->availabilityNotifierRepository->findOneBy(['customer' => $availabilityNotifier->getCustomer(), 'status' => false])) {
                $this->entityManager->persist($availabilityNotifier);
                $this->entityManager->flush();
            }

            return new JsonResponse([
                'content' => $this->templatingEngine->render('@WorkouseAvailabilityNotifierPlugin/_successful.html.twig'),
            ], 201);
        }
    }

    public function getUserByEmail($customerEmail)
    {
        /** @var ShopUser $user */
        $user = $this->security->getUser();

        $customer = $user !== null ? $user->getCustomer() : $this->customerRepository->findOneBy([
            'email' => $customerEmail,
        ]);

        if (!$customer) {
            /** @var Customer $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($customerEmail);
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
        }

        return $customer;
    }

    public function adminIndexAction($productId): Response
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new NotFoundHttpException();
        }

        $availabilityNotifiers = $this->availabilityNotifierRepository->findBy([
            'product' => $product,
            'status' => false,
        ], [], 20);

        $availabilityNotifiersTotal = count($this->availabilityNotifierRepository->findBy([
            'product' => $product,
            'status' => false,
        ]));

        return $this->templatingEngine->renderResponse(
            '@WorkouseAvailabilityNotifierPlugin/Admin/_waiting_customers.html.twig',
            [
                'availabilityNotifiers' => $availabilityNotifiers,
                'availabilityNotifiersTotal' => $availabilityNotifiersTotal,
            ]
        );
    }
}

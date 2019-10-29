<?php


namespace Tests\Workouse\AvailabilityNotifierPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

class ProductContext implements Context
{

    /** @var ShowPageInterface */
    private $showPage;

    public function __construct(ShowPageInterface $showPage)
    {
        $this->showPage = $showPage;
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When /^I check (this product)'s details in the ("([^"]+)" locale)$/
     * @When I view product :product
     * @When I view product :product in the :localeCode locale
     */
    public function iOpenProductPage(ProductInterface $product, $localeCode = 'en_US')
    {
        $this->showPage->open(['slug' => $product->getTranslation($localeCode)->getSlug(), '_locale' => $localeCode]);
    }

    /**
     * @Then I should see that it is out of stock
     */
    public function iShouldSeeItIsOutOfStock()
    {
        Assert::true($this->showPage->isOutOfStock());
    }

    /**
     * @Then I should be unable to add it to the cart
     */
    public function iShouldBeUnableToAddItToTheCart()
    {
        Assert::false($this->showPage->hasAddToCartButton());
    }

    /**
     * @When I fill the Email with :amount
     */
    public function iFillTheEmailWith(string $email): void
    {
        $this->showPage->fillEmail($email);
    }

    /**
     * @When Press Email Me
     */
    public function iAddIt(): void
    {
        $this->showPage->pressEmailMe();
    }

    /**
     * @Then I should be notified that the notify has been added
     */
    public function iShouldBeNotifiedThatNewNotifyWasAdded(): void
    {
        $this->showPage->checkNotification();
    }
}
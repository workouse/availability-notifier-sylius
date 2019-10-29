<?php


namespace Tests\Workouse\AvailabilityNotifierPlugin\Behat\Page\Shop;

use Behat\Mink\Driver\Selenium2Driver;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use Sylius\Behat\Service\JQueryHelper;

class ShowPage extends BaseShowPage
{
    public function fillEmail(string $email): void
    {
        $this->getDocument()->fillField('Email', $email);
    }

    public function pressEmailMe()
    {
        $this->getDocument()->pressButton('Email Me');
        if ($this->getDriver() instanceof Selenium2Driver) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
        }
    }

    public function checkNotification()
    {
        return $this->hasElement('success_message');
    }

    protected function getDefinedElements(): array
    {
        return  array_merge(parent::getDefinedElements(), [
            'success_message' => '#sylius-product-out-of-stock .success'
        ]);
    }

}
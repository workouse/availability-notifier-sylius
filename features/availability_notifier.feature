@availability_notifier
Feature: Availability Notifier

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Angel T-Shirt" priced at "$39.00"

  @ui
  Scenario: Not being able to add a product to the cart when it is out of stock
    Given the product "Angel T-Shirt" is out of stock
    When I check this product's details
    Then I should see that it is out of stock
    And I should be unable to add it to the cart

  @ui @javascript
  Scenario: Notify Me When Available
    Given the product "Angel T-Shirt" is out of stock
    When I check this product's details
    And I fill the Email with "omer@eresbiotech.com"
    And Press Email Me
    Then I should be notified that the notify has been added
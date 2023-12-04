Feature: Update tenant status
  As a user
  I want to be able to change the tenant status

  Scenario:
    Given I am authenticated as system user
    And I have a tenant with:
      | field        | value                |
      | name         | my homespot          |
      | code         | my-homespot          |
      | domain email | @myhomespot.com      |
      | status       | waiting_provisioning |
      | is active    | true                 |
    When I update the tenant "my-homespot" with:
      | status       |
      | provisioning |

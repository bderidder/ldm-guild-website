<?php

namespace LaDanse\ServicesBundle\Service\DTO\Account;

use LaDanse\DomainBundle\Entity as Entity;

class AccountFactory
{
    public static function create(Entity\Account $account)
    {
        $factory = new AccountFactory();

        return $factory->createAccount($account);
    }

    protected function createAccount(Entity\Account $account)
    {
        return new Account(
            $account->getId(),
            $account->getUsername(),
            $account->getDisplayName(),
            $account->getEmail(),
            $account->isEnabled(),
            $account->getLastLogin()
        );
    }
}
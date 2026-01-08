<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\framework\event\EventTestCase;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(BankAccountHtmlProjector::class)]
#[Group('integration/application')]
#[Group('integration/application/projection')]
#[Group('visual-documentation')]
#[Medium]
#[TestDox('BankAccountHtmlProjector')]
final class BankAccountHtmlProjectorTest extends EventTestCase
{
    public function testProjectsBankAccountAsHtmlPage(): void
    {
        $this->given(
            $this->accountOpened('the-owner'),
            $this->moneyDeposited(
                Money::from(123, Currency::from('EUR')),
                'the-description',
            ),
            $this->moneyDeposited(
                Money::from(456, Currency::from('EUR')),
                'the-description',
            ),
            $this->moneyWithdrawn(
                Money::from(789, Currency::from('EUR')),
                'the-description',
            ),
            $this->moneyDeposited(
                Money::from(123, Currency::from('EUR')),
                'the-description',
            ),
            $this->moneyWithdrawn(
                Money::from(456, Currency::from('EUR')),
                'the-description',
            ),
        );

        $projector = $this->bankAccountHtmlProjector();

        $this->assertStringEqualsFile(
            __DIR__ . '/../../../expectation/bank-account.html',
            $projector->project(
                Uuid::from('2c569fd6-ea17-427c-8288-65da6ce7a37d'),
            ),
        );
    }
}

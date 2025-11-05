<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventCollection;
use example\framework\event\EventCollectionIterator;
use example\framework\event\EventReader;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccountEventSourcer::class)]
#[UsesClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(EventCollection::class)]
#[UsesClass(EventCollectionIterator::class)]
#[UsesClass(AccountOpenedEvent::class)]
#[UsesClass(MoneyDepositedEvent::class)]
#[UsesClass(MoneyWithdrawnEvent::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class BankAccountEventSourceTest extends TestCase
{
    public function testSourcesBankAccountFromEvents(): void
    {
        $owner = 'the-owner';

        $reader = $this->createStub(EventReader::class);

        $reader
            ->method('topic')
            ->willReturn(
                EventCollection::fromArray(
                    [
                        new AccountOpenedEvent(
                            Uuid::from('0797781d-8b12-43d1-a76e-6a05c82fed6c'),
                            $owner,
                        ),
                    ],
                ),
                EventCollection::fromArray(
                    [
                        new MoneyDepositedEvent(
                            Uuid::from('44a2d037-3228-4d00-ac30-b49431988a4c'),
                            Money::from(123, Currency::from('EUR')),
                            'deposit',
                        ),
                        new MoneyWithdrawnEvent(
                            Uuid::from('f8eae644-0ffd-4686-8d72-3535cdbfa851'),
                            Money::from(456, Currency::from('EUR')),
                            'withdrawal',
                        ),
                        new AccountClosedEvent(
                            Uuid::from('28674fd4-60a4-47ff-b988-be8a94cb2692'),
                        ),
                    ],
                ),
            );

        $sourcer = new BankAccountEventSourcer($reader);

        $bankAccount = $sourcer->source();

        $this->assertSame($owner, $bankAccount->owner());
        $this->assertSame(-333, $bankAccount->balance()->amount());
        $this->assertFalse($bankAccount->isActive());
    }
}

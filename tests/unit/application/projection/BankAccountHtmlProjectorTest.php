<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyFormatter;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventCollection;
use example\framework\event\EventCollectionIterator;
use example\framework\event\EventReader;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccountHtmlProjector::class)]
#[UsesClass(MoneyDepositedEvent::class)]
#[UsesClass(MoneyWithdrawnEvent::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(MoneyFormatter::class)]
#[UsesClass(EventCollection::class)]
#[UsesClass(EventCollectionIterator::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class BankAccountHtmlProjectorTest extends TestCase
{
    public function testProjectsBankAccountAsHtmlPage(): void
    {
        $reader = $this->createStub(EventReader::class);

        $reader
            ->method('topic')
            ->willReturn(
                EventCollection::fromArray(
                    [
                        new MoneyDepositedEvent(
                            Uuid::from('4ea931eb-abd4-4fb5-97b4-b84eca17225d'),
                            Money::from(123, Currency::from('EUR')),
                            'the-description',
                        ),
                        new MoneyDepositedEvent(
                            Uuid::from('80521c43-056d-41ff-ae54-f98e07e7e68e'),
                            Money::from(456, Currency::from('EUR')),
                            'the-description',
                        ),
                        new MoneyWithdrawnEvent(
                            Uuid::from('f70e090c-f7c3-4bbb-9557-a77c6e9005ee'),
                            Money::from(789, Currency::from('EUR')),
                            'the-description',
                        ),
                        new MoneyDepositedEvent(
                            Uuid::from('8e0171ff-bfad-463a-9948-b1660a8521ae'),
                            Money::from(123, Currency::from('EUR')),
                            'the-description',
                        ),
                        new MoneyWithdrawnEvent(
                            Uuid::from('02d70a8e-c05d-4880-9ada-ea0089eb7ba1'),
                            Money::from(456, Currency::from('EUR')),
                            'the-description',
                        ),
                    ],
                ),
            );

        $projector = new BankAccountHtmlProjector($reader);

        $this->assertStringEqualsFile(
            __DIR__ . '/../../../expectation/bank-account.html',
            $projector->project(),
        );
    }
}

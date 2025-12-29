<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use Eris\Generator;
use Eris\Generators;
use Eris\TestTrait;
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
use example\framework\library\RandomUuidGenerator;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
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
#[UsesClass(RandomUuidGenerator::class)]
#[UsesClass(Uuid::class)]
#[Small]
#[TestDox('BankAccountEventSourcer')]
final class BankAccountEventSourcerTest extends TestCase
{
    use TestTrait;

    public function testSourcesBankAccountFromEvents(): void
    {
        $this
            ->forAll($this->generateEvents())
            ->then(
                /**
                 * @param list<array{type: class-string, amount: ?positive-int}> $_events
                 */
                function (array $_events): void
                {
                    /** @var list<array{type: class-string, amount: ?positive-int}> $_events */
                    $events = $this->mapEvents($_events);
                    $owner  = 'the-owner';

                    $reader = $this->createStub(EventReader::class);

                    $reader
                        ->method('topic')
                        ->willReturn(
                            EventCollection::fromArray(
                                [
                                    new AccountOpenedEvent(
                                        $this->uuid(),
                                        $owner,
                                    ),
                                ],
                            ),
                            $events,
                        );

                    $sourcer = new BankAccountEventSourcer($reader);

                    $bankAccount = $sourcer->source();

                    $this->assertSame($owner, $bankAccount->owner());
                    $this->assertSame($this->expectedBalance($events)->amount(), $bankAccount->balance()->amount());
                    $this->assertSame($this->expectedActive($events), $bankAccount->isActive());
                },
            );
    }

    /**
     * @return Generator<list<array{type: class-string, amount: ?positive-int}>>
     */
    private function generateEvents(): Generator
    {
        return Generators::suchThat(
            /**
             * @param list<array{type: class-string, amount: ?positive-int}> $events
             */
            static function (array $events): bool
            {
                // Event stream must not be empty
                if ($events === []) {
                    return false;
                }

                $closed = false;

                foreach ($events as $event) {
                    // "Account Closed" must be the final event when it is part of the event stream
                    if ($closed) {
                        return false;
                    }

                    /** @var array{type: class-string, amount: ?positive-int} $event */
                    if ($event['type'] === AccountClosedEvent::class) {
                        $closed = true;
                    }
                }

                return true;
            },
            $this->validEventsGenerator(),
        );
    }

    /**
     * @return Generator<list<array{type: class-string, amount: ?positive-int}>>
     */
    private function validEventsGenerator(): Generator
    {
        /** @phpstan-ignore return.type */
        return Generators::seq(
            Generators::oneOf(
                Generators::associative([
                    'type'   => Generators::constant(AccountClosedEvent::class),
                    'amount' => null,
                ]),
                Generators::associative([
                    'type'   => Generators::constant(MoneyDepositedEvent::class),
                    'amount' => Generators::choose(1, 100_000),
                ]),
                Generators::associative([
                    'type'   => Generators::constant(MoneyWithdrawnEvent::class),
                    'amount' => Generators::choose(1, 100_000),
                ]),
            ),
        );
    }

    /**
     * @param list<array{type: class-string, amount: ?positive-int}> $events
     */
    private function mapEvents(array $events): EventCollection
    {
        $result = [];

        foreach ($events as $event) {
            if ($event['type'] === MoneyDepositedEvent::class) {
                assert($event['amount'] !== null);

                $result[] = new MoneyDepositedEvent(
                    $this->uuid(),
                    Money::from($event['amount'], Currency::from('EUR')),
                    'deposit',
                );
            } elseif ($event['type'] === MoneyWithdrawnEvent::class) {
                assert($event['amount'] !== null);

                $result[] = new MoneyWithdrawnEvent(
                    $this->uuid(),
                    Money::from($event['amount'], Currency::from('EUR')),
                    'withdrawal',
                );
            } else {
                $result[] = new AccountClosedEvent($this->uuid());
            }
        }

        return EventCollection::fromArray($result);
    }

    private function expectedActive(EventCollection $events): bool
    {
        foreach ($events as $event) {
            if ($event instanceof AccountClosedEvent) {
                return false;
            }
        }

        return true;
    }

    private function expectedBalance(EventCollection $events): Money
    {
        $balance = Money::from(0, Currency::from('EUR'));

        foreach ($events as $event) {
            if ($event instanceof MoneyDepositedEvent) {
                $balance = $balance->plus($event->amount());

                continue;
            }

            if ($event instanceof MoneyWithdrawnEvent) {
                $balance = $balance->minus($event->amount());
            }
        }

        return $balance;
    }

    private function uuid(): Uuid
    {
        return (new RandomUuidGenerator)->generate();
    }
}

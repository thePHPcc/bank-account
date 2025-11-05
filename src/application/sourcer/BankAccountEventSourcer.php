<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use function count;
use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventReader;

/**
 * @no-named-arguments
 */
final readonly class BankAccountEventSourcer implements BankAccountSourcer
{
    private EventReader $reader;

    public function __construct(EventReader $reader)
    {
        $this->reader = $reader;
    }

    public function source(): BankAccount
    {
        $bankAccount = BankAccount::from(
            $this->owner(),
            Money::from(0, Currency::from('EUR')),
            true,
        );

        foreach ($this->reader->topic('banking.money-deposited', 'banking.money-withdrawn', 'banking.account-closed') as $event) {
            if ($event instanceof MoneyDepositedEvent) {
                $bankAccount->deposit($event->amount());
            }

            if ($event instanceof MoneyWithdrawnEvent) {
                $bankAccount->withdraw($event->amount());
            }

            if ($event instanceof AccountClosedEvent) {
                $bankAccount->close();
            }
        }

        return $bankAccount;
    }

    /**
     * @return non-empty-string
     */
    private function owner(): string
    {
        $events = $this->reader->topic('banking.account-opened')->asArray();

        assert(count($events) === 1);

        $event = $events[0];

        assert($event instanceof AccountOpenedEvent);

        return $event->owner();
    }
}

<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventReader;
use example\framework\library\Uuid;

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

    public function source(Uuid $accountId): BankAccount
    {
        foreach ($this->reader->correlationId($accountId) as $event) {
            if ($event instanceof AccountOpenedEvent) {
                $bankAccount = BankAccount::from(
                    $event->owner(),
                    Money::from(0, Currency::from('EUR')),
                    true,
                );

                continue;
            }

            assert(isset($bankAccount));

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

        assert(isset($bankAccount));

        return $bankAccount;
    }
}

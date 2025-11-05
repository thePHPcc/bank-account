<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use function file_get_contents;
use function is_string;
use function str_replace;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyFormatter;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventReader;

/**
 * @no-named-arguments
 */
final readonly class BankAccountHtmlProjector
{
    private EventReader $reader;

    public function __construct(EventReader $reader)
    {
        $this->reader = $reader;
    }

    public function project(): string
    {
        $rows    = '';
        $balance = Money::from(0, Currency::from('EUR'));

        foreach ($this->reader->topic('banking.money-deposited', 'banking.money-withdrawn') as $event) {
            if ($event instanceof MoneyDepositedEvent) {
                $balance = $balance->add($event->amount());

                $rows .= $this->row($event->description(), $event->amount(), $balance);
            }

            if ($event instanceof MoneyWithdrawnEvent) {
                $balance = $balance->subtract($event->amount());

                $rows .= $this->row($event->description(), $event->amount()->negate(), $balance);
            }
        }

        return str_replace(
            '{{rows}}',
            $rows,
            $this->pageTemplate(),
        );
    }

    /**
     * @return non-empty-string
     */
    private function pageTemplate(): string
    {
        return $this->template('page');
    }

    /**
     * @return non-empty-string
     */
    private function rowTemplate(): string
    {
        return $this->template('row');
    }

    /**
     * @return non-empty-string
     */
    private function template(string $name): string
    {
        $buffer = file_get_contents(__DIR__ . '/../../../templates/' . $name . '.html');

        assert(is_string($buffer));
        assert($buffer !== '');

        return $buffer;
    }

    private function row(string $description, Money $amount, Money $balance): string
    {
        $formatter = new MoneyFormatter('de_DE');

        return str_replace(
            [
                '{{description}}',
                '{{amountColor}}',
                '{{amount}}',
                '{{balanceColor}}',
                '{{balance}}',
            ],
            [
                $description,
                $amount->amount() >= 0 ? '#000000' : '#ff0000',
                $formatter->format($amount),
                $balance->amount() >= 0 ? '#000000' : '#ff0000',
                $formatter->format($balance),
            ],
            $this->rowTemplate(),
        );
    }
}

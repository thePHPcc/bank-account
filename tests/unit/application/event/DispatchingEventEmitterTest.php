<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventDispatcher;
use example\framework\library\Uuid;
use example\framework\library\UuidGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[TestDox('DispatchingEventEmitter')]
#[CoversClass(DispatchingEventEmitter::class)]
#[UsesClass(AccountOpenedEvent::class)]
#[UsesClass(AccountClosedEvent::class)]
#[UsesClass(MoneyDepositedEvent::class)]
#[UsesClass(MoneyWithdrawnEvent::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class DispatchingEventEmitterTest extends TestCase
{
    private DispatchingEventEmitter $emitter;
    private EventDispatcher&MockObject $dispatcher;
    private Stub&UuidGenerator $uuidGenerator;

    protected function setUp(): void
    {
        $this->uuidGenerator = $this->createStub(UuidGenerator::class);
        $this->dispatcher    = $this->createMock(EventDispatcher::class);

        $this->emitter = new DispatchingEventEmitter(
            $this->dispatcher,
            $this->uuidGenerator,
        );
    }

    #[TestDox('accountOpened() emits AccountOpened event')]
    public function testAccountOpenedDispatchesAccountOpenedEvent(): void
    {
        $uuid = Uuid::from('e3890992-beb0-43a6-a2ab-4fc583cc4693');

        $this
            ->uuidGenerator
            ->method('generate')
            ->willReturn($uuid);

        $owner = 'the-owner';

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new AccountOpenedEvent(
                    $uuid,
                    $owner,
                ),
            );

        $this->emitter->accountOpened($owner);
    }

    #[TestDox('accountClosed() emits AccountClosed event')]
    public function testAccountClosedDispatchesAccountClosedEvent(): void
    {
        $uuid = Uuid::from('6ebf72d7-9eaa-4e23-a79e-398a65d384bb');

        $this
            ->uuidGenerator
            ->method('generate')
            ->willReturn($uuid);

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new AccountClosedEvent(
                    $uuid,
                ),
            );

        $this->emitter->accountClosed();
    }

    #[TestDox('moneyDeposited() emits MoneyDeposited event')]
    public function testMoneyDepositedDispatchesMoneyDepositedEvent(): void
    {
        $uuid = Uuid::from('3ad4ff02-5bef-48b4-b61f-d60abd608e78');

        $this
            ->uuidGenerator
            ->method('generate')
            ->willReturn($uuid);

        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new MoneyDepositedEvent(
                    $uuid,
                    $amount,
                    $description,
                ),
            );

        $this->emitter->moneyDeposited($amount, $description);
    }

    #[TestDox('moneyWithdrawn() emits MoneyWithdrawn event')]
    public function testMoneyWithdrawnDispatchesMoneyWithdrawnEvent(): void
    {
        $uuid = Uuid::from('07550574-22f9-447e-ac1e-6849bcc6c229');

        $this
            ->uuidGenerator
            ->method('generate')
            ->willReturn($uuid);

        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                new MoneyWithdrawnEvent(
                    $uuid,
                    $amount,
                    $description,
                ),
            );

        $this->emitter->moneyWithdrawn($amount, $description);
    }
}

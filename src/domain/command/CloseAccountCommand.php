<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class CloseAccountCommand extends Command
{
    private Uuid $accountId;

    public function __construct(Uuid $accountId)
    {
        $this->accountId = $accountId;
    }

    public function accountId(): Uuid
    {
        return $this->accountId;
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return 'Close account';
    }
}

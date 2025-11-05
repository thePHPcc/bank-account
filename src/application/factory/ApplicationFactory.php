<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequestRouter;
use example\framework\http\Kernel;
use example\framework\http\PostRequestRouter;

/**
 * @no-named-arguments
 *
 * @codeCoverageIgnore
 */
final readonly class ApplicationFactory
{
    public function createApplication(): Kernel
    {
        return new Kernel(
            $this->createGetRequestRouter(),
            $this->createPostRequestRouter(),
        );
    }

    private function createGetRequestRouter(): GetRequestRouter
    {
        return new GetRequestRouter(
            new BankAccountRoute($this->createQueryFactory()),
            new NotFoundGetRoute,
        );
    }

    private function createPostRequestRouter(): PostRequestRouter
    {
        return new PostRequestRouter(
            new OpenAccountRoute($this->createCommandFactory()),
            new CloseAccountRoute($this->createCommandFactory()),
            new DepositMoneyRoute($this->createCommandFactory()),
            new WithdrawMoneyRoute($this->createCommandFactory()),
            new NotFoundPostRoute,
        );
    }

    private function createCommandFactory(): CommandFactory
    {
        return new ProductionCommandFactory;
    }

    private function createQueryFactory(): QueryFactory
    {
        return new ProductionQueryFactory;
    }
}

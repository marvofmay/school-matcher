<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\HTTP;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', priority: 300)]
final readonly class UnifiedApiExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        #[Autowire('%kernel.debug%')] private bool $isDebug = false
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error(sprintf(
            '-- Exception: %s in %s:%d --',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));

        $status = 500;
        $message = 'Internal Server Error';

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP error occurred';
        } elseif ($this->isDebug) {
            $message = $exception->getMessage();
        }

        $event->setResponse(new JsonResponse([
            'message' => $message,
        ], $status));
    }
}

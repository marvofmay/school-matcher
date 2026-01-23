<?php

declare(strict_types=1);

namespace App\Tests\Common\Infrastructure\HTTP;

use App\Common\Infrastructure\HTTP\UnifiedApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class UnifiedApiExceptionListenerTest extends TestCase
{
    private function createLoggerStub(): LoggerInterface
    {
        return new class () implements LoggerInterface {
            public function emergency(string|\Stringable $message, array $context = []): void
            {
            }
            public function alert(string|\Stringable $message, array $context = []): void
            {
            }
            public function critical(string|\Stringable $message, array $context = []): void
            {
            }
            public function error(string|\Stringable $message, array $context = []): void
            {
            }
            public function warning(string|\Stringable $message, array $context = []): void
            {
            }
            public function notice(string|\Stringable $message, array $context = []): void
            {
            }
            public function info(string|\Stringable $message, array $context = []): void
            {
            }
            public function debug(string|\Stringable $message, array $context = []): void
            {
            }
            public function log($level, string|\Stringable $message, array $context = []): void
            {
            }
        };
    }

    public function testHandlesHttpException(): void
    {
        $logger = $this->createLoggerStub();

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = new Request();
        $exception = new NotFoundHttpException('Not found');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $listener = new UnifiedApiExceptionListener($logger);
        $listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Not found', $data['message']);
    }

    public function testHandlesGenericExceptionInProd(): void
    {
        $logger = $this->createLoggerStub();

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = new Request();
        $exception = new \RuntimeException('Something went wrong');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $listener = new UnifiedApiExceptionListener($logger, false);
        $listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Internal Server Error', $data['message']);
    }

    public function testHandlesGenericExceptionInDebug(): void
    {
        $logger = $this->createLoggerStub();

        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = new Request();
        $exception = new \RuntimeException('Something went wrong');

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        $listener = new UnifiedApiExceptionListener($logger, true);
        $listener->__invoke($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Something went wrong', $data['message']);
    }
}

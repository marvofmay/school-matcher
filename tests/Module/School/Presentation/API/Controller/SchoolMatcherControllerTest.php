<?php

declare(strict_types=1);

namespace App\Tests\Module\School\Presentation\API\Controller;

use App\Module\School\Application\DTO\Request\SchoolQueryDTO;
use App\Module\School\Application\Query\SchoolMatcherQuery;
use App\Module\School\Presentation\API\Controller\SchoolMatcherController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SchoolMatcherControllerTest extends TestCase
{
    public function testControllerReturnsMatches(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'Test School';

        $expectedResult = [
            ['school' => 'I Liceum Ogólnokształcące', 'score' => 100],
        ];

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())
            ->method('dispatch')
            ->willReturn(
                new Envelope(
                    new SchoolMatcherQuery($dto),
                    [new HandledStamp($expectedResult, 'handler')]
                )
            );


        $controller = new SchoolMatcherController($bus);

        $response = $controller->__invoke($dto);

        $this->assertSame(
            json_encode(['matches' => $expectedResult]),
            $response->getContent()
        );
    }

    public function testControllerThrowsOnHandlerFailedException(): void
    {
        $dto = new SchoolQueryDTO();
        $dto->name = 'Test School';

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())
            ->method('dispatch')
            ->willThrowException(
                new HandlerFailedException(
                    new Envelope(new \stdClass()),
                    [new \Exception('Something went wrong')]
                )
            );

        $controller = new SchoolMatcherController($bus);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something went wrong');

        $controller->__invoke($dto);
    }
}

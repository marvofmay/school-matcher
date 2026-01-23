<?php

declare(strict_types=1);

namespace App\Module\School\Presentation\API\Controller;

use App\Module\School\Application\DTO\SchoolQueryDTO;
use App\Module\School\Application\Query\SchoolMatcherQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SchoolMatcherController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'query.bus')] MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    #[Route('/api/school/match', name: 'school_match', methods: ['GET'])]
    public function __invoke(#[MapQueryString] SchoolQueryDTO $queryDTO): JsonResponse
    {
        try {
            $matches = $this->handle(new SchoolMatcherQuery($queryDTO));
            return new JsonResponse(['matches' => $matches]);
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious();
        }
    }
}

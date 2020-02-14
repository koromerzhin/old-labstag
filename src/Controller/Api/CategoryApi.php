<?php

namespace Labstag\Controller\Api;

use Knp\Component\Pager\PaginatorInterface;
use Labstag\Entity\Category;
use Labstag\Handler\CategoryPublishingHandler;
use Labstag\Lib\ApiControllerLib;
use Labstag\Repository\CategoryRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class CategoryApi extends ApiControllerLib
{

    /**
     * @var CategoryPublishingHandler
     */
    protected $publishingHandler;

    public function __construct(
        CategoryPublishingHandler $handler,
        ContainerInterface $container,
        PaginatorInterface $paginator,
        RequestStack $requestStack,
        RouterInterface $router,
        LoggerInterface $logger
    )
    {
        parent::__construct($container, $paginator, $requestStack, $router, $logger);
        $this->publishingHandler = $handler;
    }

    public function __invoke(Category $data): Category
    {
        $this->publishingHandler->handle($data);

        return $data;
    }

    /**
     * @Route("/api/categories/trash", name="api_categorytrash")
     */
    public function trash(CategoryRepository $repository): Response
    {
        return $this->trashAction($repository);
    }

    /**
     * @Route("/api/categories/trash", name="api_categorytrashdelete", methods={"DELETE"})
     */
    public function delete(CategoryRepository $repository): JsonResponse
    {
        return $this->deleteAction($repository);
    }

    /**
     * @Route("/api/categories/restore", name="api_categoryrestore", methods={"POST"})
     */
    public function restore(CategoryRepository $repository): JsonResponse
    {
        return $this->restoreAction($repository);
    }

    /**
     * @Route("/api/categories/empty", name="api_categoryempty", methods={"POST"})
     */
    public function vider(CategoryRepository $repository): JsonResponse
    {
        return $this->emptyAction($repository);
    }
}

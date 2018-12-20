<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class GroupController extends FOSRestController implements ClassResourceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository
    ) {
        $this->entityManager       = $entityManager;
        $this->groupRepository     = $groupRepository;
    }

    public function cgetAction()
    {
        $articles = $this->groupRepository->findAll();
        // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
        return View::create($articles, Response::HTTP_OK);

//        die('test');
//        $entities = $this->groupRepository->findAll();
//        var_dump($entities);
//
//        $view = new View($entities);
//        $view->setFormat('json');
//        return $this->handleView($view);
    }

//    public function getAction(): View
//    {
//        $articles = $this->articleRepository->findAll();
//        // In case our GET was a success we need to return a 200 HTTP OK response with the collection of article object
//        return View::create($articles, Response::HTTP_OK);
//    }

    public function getArticle(int $articleId): View
    {
        $article = $this->articleRepository->findById($articleId);
        // In case our GET was a success we need to return a 200 HTTP OK response with the request object
        return View::create($article, Response::HTTP_OK);
    }

}

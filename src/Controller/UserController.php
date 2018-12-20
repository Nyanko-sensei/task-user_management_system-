<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;

class UserController extends FOSRestController implements ClassResourceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->entityManager       = $entityManager;
        $this->userRepository      = $userRepository;
    }

    public function cgetAction()
    {
        $entities = $this->userRepository->findAll();

        $view = new View($entities);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function postAction()
    {
        die('test');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends FOSRestController implements ClassResourceInterface
{
    private $entityManager;
    private $userRepository;

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

    public function getAction(int $userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new HttpException(404, "User not found");
        }

        $view = new View($user);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function putAction(int $userId, Request $request)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new HttpException(404, "User not found");
        }

        $user->setUsername($request->get('name'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $view = new View($user);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        $name = $request->get('name');
        $user = $this->userRepository->findOneBy(['username' => $name]);
        if ($user) {
            throw new HttpException(422, "User with this name already exists");
        }

        $user = new User();
        $user->setUsername($name);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $view = new View($user);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function deleteAction(int $userId)
    {
        $user = $this->userRepository->find($userId);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        $view = new View([]);
        $view->setFormat('json');
        return $this->handleView($view);
    }
}

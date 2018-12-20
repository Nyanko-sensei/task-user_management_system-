<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupController extends FOSRestController implements ClassResourceInterface
{
    private $entityManager;
    private $userGroupRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserGroupRepository $userGroupRepository
    ) {
        $this->entityManager       = $entityManager;
        $this->userGroupRepository = $userGroupRepository;
    }

    public function cgetAction()
    {
        $userGroups = $this->userGroupRepository->findAll();

        $view = new View($userGroups);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function getAction(int $userGroupId)
    {
        $userGroup = $this->userGroupRepository->find($userGroupId);

        if (!$userGroup) {
            throw new HttpException(404, "User group not found");
        }

        $view = new View($userGroup);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function putAction(int $userGroupId, Request $request)
    {
        $userGroup = $this->userGroupRepository->find($userGroupId);

        if (!$userGroup) {
            throw new HttpException(404, "User not found");
        }

        $userGroup->setName($request->get('name'));
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        $view = new View($userGroup);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        $name = $request->get('name');
        $userGroup = $this->userGroupRepository->findOneBy(['name' => $name]);
        if ($userGroup) {
            throw new HttpException(422, "User group with this name already exists");
        }

        $userGroup = new UserGroup();
        $userGroup->setName($name);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        $view = new View($userGroup);
        $view->setFormat('json');
        return $this->handleView($view);
    }

    public function deleteAction(int $userGroupId)
    {
        $userGroup = $this->userGroupRepository->find($userGroupId);
        if ($userGroup) {
            $this->entityManager->remove($userGroup);
            $this->entityManager->flush();
        }

        $view = new View([]);
        $view->setFormat('json');
        return $this->handleView($view);
    }

}

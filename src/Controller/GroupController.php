<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GroupController extends FOSRestController implements ClassResourceInterface
{
    private $entityManager;
    private $userGroupRepository;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserGroupRepository $userGroupRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager       = $entityManager;
        $this->userGroupRepository = $userGroupRepository;
        $this->userRepository      = $userRepository;
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
            throw new HttpException(409, "User group with this name already exists");
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
            if ($userGroup->getUsers()->count() > 0) {
                throw new HttpException(409, "Can't delete groupe with users");
            }

            $this->entityManager->remove($userGroup);
            $this->entityManager->flush();
        }

        $view = new View([]);
        $view->setFormat('json');
        return $this->handleView($view);
    }


    public function linkUserGroupUserAction(int $groupId, int $userId)
    {
        $user = $this->userRepository->find($userId);
        $userGroup = $this->userGroupRepository->find($groupId);

        if (!$user || !$userGroup) {
            throw new HttpException(404, "User or group not found");
        }

        $userGroup->addUser($user);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($userGroup , null, array('attributes' => array('name', 'Users' => ['username'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    public function unlinkUserGroupUserAction(int $groupId, int $userId)
    {

        $user = $this->userRepository->find($userId);
        $userGroup = $this->userGroupRepository->find($groupId);

        if (!$user || !$userGroup) {
            throw new HttpException(404, "User or group not found");
        }

        $userGroup->removeUser($user);
        $this->entityManager->persist($userGroup);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($userGroup , null, array('attributes' => array('name', 'Users' => ['username'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

}

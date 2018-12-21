<?php

namespace App\Controller;

use App\Entity\User;
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

class UserController extends FOSRestController implements ClassResourceInterface
{
    private $entityManager;
    private $userRepository;
    private $userGroupRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserGroupRepository $userGroupRepository
    ) {
        $this->entityManager       = $entityManager;
        $this->userRepository      = $userRepository;
        $this->userGroupRepository      = $userGroupRepository;
    }

    public function cgetAction()
    {
        $users = $this->userRepository->findAll();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($users, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    public function getAction(int $userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new HttpException(404, "User not found");
        }

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($user, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    public function putAction(int $userId, Request $request)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new HttpException(404, "User not found");
        }

        $user->setUsername($request->get('username'));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($user, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        $name = $request->get('username');
        $user = $this->userRepository->findOneBy(['username' => $name]);
        if ($user) {
            throw new HttpException(409, "User with this name already exists");
        }

        $user = new User();
        $user->setUsername($name);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($user, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
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

    public function linkUserUserGroupAction(int $userId, int $groupId)
    {
        $user = $this->userRepository->find($userId);
        $userGroup = $this->userGroupRepository->find($groupId);

        if (!$user || !$userGroup) {
            throw new HttpException(404, "User or group not found");
        }

        $user->addUserGroup($userGroup);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($user, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    public function unlinkUserUserGroupAction(int $userId, int $groupId)
    {

        $user = $this->userRepository->find($userId);
        $userGroup = $this->userGroupRepository->find($groupId);

        if (!$user || !$userGroup) {
            throw new HttpException(404, "User or group not found");
        }

        $user->removeUserGroup($userGroup);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($user, null, array('attributes' => array('username', 'UserGroups' => ['name'])));

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}

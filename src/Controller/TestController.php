<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
class TestController extends AbstractController
{
    public const DataUsers = [
        [
            'id'    => '1',
            'email' => 'ipzk241_iao1@student.ztu.edu.ua',
            'name'  => 'Andriy1'
        ],
        [
            'id'    => '2',
            'email' => 'ipzk241_iao2@student.ztu.edu.ua',
            'name'  => 'Andriy2'
        ],
        [
            'id'    => '3',
            'email' => 'ipzk241_iao3@student.ztu.edu.ua',
            'name'  => 'Andriy3'
        ],
        [
            'id'    => '4',
            'email' => 'ipzk241_iao4@student.ztu.edu.ua',
            'name'  => 'Andriy4'
        ],
    ];

    #[Route('/users', name: 'app_collection_users', methods: ['GET'])]
    public function getCollection(): JsonResponse
    {
        return new JsonResponse([
            'data' => self::DataUsers
        ], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'app_item_users', methods: ['GET'])]
    public function getItem(string $id): JsonResponse
    {
        $dataUser = $this->findUserById($id);

        return new JsonResponse([
            'data' => $dataUser
        ], Response::HTTP_OK);
    }

    #[Route('/users', name: 'app_create_users', methods: ['POST'])]
    public function createItem(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['email'], $requestData['name'])) {
            throw new UnprocessableEntityHttpException("Name and email are required!");
        }

        $countOfUsers = count(self::DataUsers);
        $newUser = [
            'id'   => $countOfUsers + 1,
            'name' => $requestData['name'],
            'email' => $requestData['email']
        ];

        return new JsonResponse([
            'data' => $newUser
        ], Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}', name: 'app_delete_users', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse
    {
        $this->findUserById($id);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}', name: 'app_update_users', methods: ['PATCH'])]
    public function updateItem(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['name'])) {
            throw new UnprocessableEntityHttpException("Name is required!");
        }

        $userData = $this->findUserById($id);


        $userData['name'] = $requestData['name'];

        return new JsonResponse(['data' => $userData], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function findUserById(string $id)
    {
        $userData = null;
        foreach (self::DataUsers as $user) {
            if (!isset($user['id'])) {
                continue;
            }
            if ($user['id'] == $id) {
                $userData = $user;
                break;
            }
        }
        if (!$userData) {
            throw new NotFoundHttpException("User with id " . $id . " not found");
        }

        return $userData;
    }
}

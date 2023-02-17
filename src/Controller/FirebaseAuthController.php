<?php

namespace App\Controller;

use App\Services\FirebaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FirebaseAuthController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * @Route("/singup", methods={"POST"})
     */
    public function register(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $auth = $this->firebaseService->getAuth();
        $firestore = $this->firebaseService->getFirestore();

        try {
            $user = $auth->createUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'emailVerified' => false,
                'disabled' => false,
            ]);
            $uid = $user->uid;
            $collectionReference = $firestore->collection("Users");
            $collectionReference->document($uid)->set([
                'email' => $data['email'],
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => $data['date_naissance'],
                'img_profil' => $data['img_profil'] ?? ''
            ]);

            return new JsonResponse(['message' => 'success', 'user' => $user, ]);
        } catch (\Kreait\Firebase\Auth\AuthError $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/user/{id}", methods={"GET"})
     */
    public function getUserById($id)
    {
        $firestore = $this->firebaseService->getFirestore();
        $userDocument = $firestore->collection('Users')->document($id);
        $user = $userDocument->snapshot()->data();
        if ($user) {
            return new JsonResponse(['message' => 'success', 'user' => $user]);
        } else {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
    }

    /**
     * @Route("/user/{id}", methods={"PUT"})
     */
    public function updateUserById(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $firestore = $this->firebaseService->getFirestore();
        $userDocument = $firestore->collection('Users')->document($id);
        $user = $userDocument->snapshot()->data();
        if ($user) {
            $userDocument->update([
                ['path' => 'email', 'value' => $data['email'] ?? $user['email']],
                ['path' => 'nom', 'value' => $data['nom'] ?? $user['nom']],
                ['path' => 'prenom', 'value' => $data['prenom'] ?? $user['prenom']],
                ['path' => 'date_naissance', 'value' => $data['date_naissance'] ?? $user['date_naissance']],
                ['path' => 'img_profil', 'value' => $data['img_profil'] ?? $user['img_profil']],
            ]);
            $updatedUser = $userDocument->snapshot()->data();
            return new JsonResponse(['message' => 'success', 'user' => $updatedUser]);
        } else {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
    }




    
    /**
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $auth = $this->firebaseService->getAuth();

        try {
            $user = $auth->signInWithEmailAndPassword($data['email'], $data['password']);
            return new JsonResponse(['message' => 'Connexion réussie.', 'user' => $user->data()]);
        } catch (\Kreait\Firebase\Auth\AuthError $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}

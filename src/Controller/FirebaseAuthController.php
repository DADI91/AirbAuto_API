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
            
            $collectionReference = $firestore->collection("test");
            $collectionReference->add([
                'email' => $data['email'],
                'password' => $data['password'],
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

    public function createUserInFirestore($data)
    {
        
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

<?php

namespace App\Controller;

use Firebase\Auth\FirebaseAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FirebaseAuthController extends AbstractController
{
    /**
     * @Route("/register", methods={"POST"})
     *
     * @param Request $request
     * @param FirebaseAuth $auth
     *
     * @return JsonResponse
     */
    public function register(Request $request, FirebaseAuth $auth)
    {
        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);

        // Création d'un utilisateur avec Firebase Auth
        $user = $auth->createUserWithEmailAndPassword($data['email'], $data['password']);

        return new JsonResponse(['user' => $user->data()]);
    }

    /**
     * @Route("/login", methods={"POST"})
     *
     * @param Request $request
     * @param FirebaseAuth $auth
     *
     * @return JsonResponse
     */
    public function login(Request $request, FirebaseAuth $auth)
    {
        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);

        // Connexion d'un utilisateur avec Firebase Auth
        $user = $auth->signInWithEmailAndPassword($data['email'], $data['password']);

        return new JsonResponse(['user' => $user->data()]);
    }

    /**
     * @Route("/logout", methods={"POST"})
     *
     * @param FirebaseAuth $auth
     *
     * @return JsonResponse
     */
    public function logout(FirebaseAuth $auth)
    {
        // Déconnexion de l'utilisateur connecté
        $auth->signOut();

        return new JsonResponse(['success' => true]);
    }
}

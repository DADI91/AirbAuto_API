<?php

namespace App\Controller;

use App\Services\FirebaseService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Kreait\Firebase\Storage;

class TypePublicationController extends AbstractController
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }





    /**
     * @Route("type_publications", methods={"GET"})
     */
    public function getAllTypePublications()
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $userReference = $firestore->collection("TypePublication");

        // Récupérer toutes les publications de l'utilisateur
        $typePublication = [];
        $query = $userReference->documents();
        foreach ($query as $document) {
            $typePublication[] = $document->data();
        }

        return new JsonResponse([
            "message" => "Récuperation des types de publication avec succès",
            "publications" => $typePublication,
        ]);
    }

    /**
     * @Route("type_publication/{typeId}", methods={"GET"})
     */
    public function getTypePublicationById($typeId)
    {
        // Récupérer la référence aux services Firestore
        $firestore = $this->firebaseService->getFirestore();

        // Récupérer la référence à la collection "Publication" de Firestore
        $typeReference = $firestore->collection("TypePublication")->document($typeId);

        // Récupérer toutes les publications de l'utilisateur
        $type = $typeReference->snapshot()->data();



        return new JsonResponse([
            "message" => "Récuperation des types de publication avec succès",
            "publications" => $type,
        ]);
    }
 


    
}
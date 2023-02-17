<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Google\Cloud\Firestore\FirestoreClient;



class FirebaseService
{
    private $firebase;
    private $firestore;


    public function __construct(Factory $factory)
    {
        $credentialsPath = realpath("/Users/walid/Desktop/Airb_Auto_Project/AirbAuto_API/config/firebase_credentials.json");

        $this->firebase = (new Factory)
            ->withServiceAccount($credentialsPath);
        
    }

    public function getAuth()
    {
        return $this->firebase->createAuth();
    }

    public function getFirestore()
    {
        return $this->firebase->createFirestore()->database();
    }

    public function getStorage()
    {
        //return $this->firebase->createStorage();
    }


}

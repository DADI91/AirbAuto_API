<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Storage\StorageClient;
use Kreait\Firebase\Storage;



class FirebaseService
{
    private $firebase;
    private $firestore;
    private $storage;

    public function __construct(Factory $factory) {
        $credentialsPath = realpath("/Users/walid/Documents/GitHub/AirbAuto_API/config/firebase_credentials.json");

        $this->firebase = (new Factory)
            ->withServiceAccount($credentialsPath);

            $this->storage = $this->firebase->createStorage("gs://airbauto.appspot.com"); // Modifiez cette ligne

    }

    public function getAuth(){

        return $this->firebase->createAuth();
    }

    public function getFirestore() {

        return $this->firebase->createFirestore()->database();
    }

    public function getStorage(): Storage
    {
        return $this->storage;

    }
    public function getBucket() // Ajoutez cette méthode
    {
        return $this->storage->getBucket();
    }



}

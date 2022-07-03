<?php

namespace App\Services;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageService extends AbstractController{

    public function moveImage($file, Produit $produit ){
        $dossier_upload = $this->getParameter("upload_directory");
        $photo = md5(uniqid()) . "." . $file->guessExtension(); // .jpg
        $file->move( $dossier_upload, $photo);
        $produit->setPhoto($photo);
    }
    public function deleteImage(Produit $produit){
        $dossier_upload = $this->getParameter("upload_directory");
        $photo = $produit->getPhoto();
        $oldPhoto = $dossier_upload . "/" . $photo; 
        if(file_exists($oldPhoto)){
            unlink($oldPhoto);
        }
    }
    public function updateImage($file, Produit $produit){

        $this->deleteImage($produit);
        $this->moveImage($file, $produit);
    }
}
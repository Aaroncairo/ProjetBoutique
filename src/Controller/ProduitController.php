<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Services\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController{

    private $em;
    private $imgService;

    public function __construct(EntityManagerInterface $em, ImageService $imgService)
    {
        $this->em = $em;
        $this->imgService = $imgService;
    }

    #[Route("/list", name:"produit_list")]
    public function list():Response{
        $produits = $this->em->getRepository(Produit::class)->findAll();
        return $this->render( "produit/list.html.twig" , ["produits" => $produits]);
    }

    #[Route("/update/{id}", name:"produit_update")]
    public function update(Request $request, $id):Response{
        $produit = $this->em->getRepository(Produit::class)->find($id);

        if($produit === null) return $this->redirectToRoute("produit_list");

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $file = $form["photo"]->getData();
            if($file){
                $this->imgService->updateImage($file, $produit);
            }
        
            $this->em->persist($produit);
            $this->em->flush();
            return $this->redirectToRoute("produit_list");
        }

        return $this->render("produit/new.html.twig" , [
            "form" => $form->createView()
        ]);
    }

    #[Route("/suppr/{id}", name:"produit_suppr")]
    public function suppr($id):RedirectResponse{

        $produitASupprimer = $this->em->getRepository(Produit::class)->find($id);

        if($produitASupprimer){
            // suppression du fichier dans le dossier upload
            $this->imgService->deleteImage($produitASupprimer);
            // fin suppression du fichier dans le dossier upload
            $this->em->remove($produitASupprimer);
            $this->em->flush();
        }
        return $this->redirectToRoute("produit_list");
    }
    
    #[Route("/new", name:"produit_new")]
    public function new(Request $request):Response{

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //$file = $request->files->get("vehicule")["photo"];
            $file = $form["photo"]->getData();

           $this->imgService->moveImage($file, $produit);

            // récupérer le fichier
            // le nommer le déplacer
            $this->em->persist($produit);
            $this->em->flush();
            return $this->redirectToRoute("produit_list");
        }

        return $this->render("produit/new.html.twig", ["form" => $form->createView()]);
    }
}
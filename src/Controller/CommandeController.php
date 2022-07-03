<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route("/commande/list", name:"commande_list")]
    public function list():Response{
        $commandes = $this->em->getRepository(Commande::class)->findAll();

        return $this->render("commande/list.html.twig", compact("commandes"));
    }

    #[Route("/commande/new", name:"commande_new")]
    public function new(Request $request, Commande $commande = null):Response{

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $produit = $form->get("produit")->getData();

            $prix = $produit->getPrix();
            $quantite = $commande->getQuantite();

            $commande->setMontant($prix * $quantite);
            $this->em->persist($commande);
            $this->em->flush();
            return $this->redirectToRoute("commande_list");
        }

        return $this->render("commande/new.html.twig", ["form" => $form->createView()]);
    }

    #[Route("/commande/update/{id}", name:"commande_update")]
    public function update(Request $request, $id):Response{
        $commande = $this->em->getRepository(Commande::class)->find($id);

        if($commande === null) return $this->redirectToRoute("commande_list");

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
        
            $this->em->persist($commande);
            $this->em->flush();
            return $this->redirectToRoute("commande_list");
        }

        return $this->render("commande/new.html.twig" , [
            "form" => $form->createView()
        ]);
    }

    #[Route("/commande/suppr/{id}", name:"commande_suppr")]
    public function delete(Commande $commandeASupprimer){

        if($commandeASupprimer !== null){
            $this->em->remove($commandeASupprimer);
            $this->em->flush();
        }
        return $this->redirectToRoute("commande_list");
    }
}
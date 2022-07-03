<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController{

    private $paginator;
    public function __construct(PaginatorInterface $paginator){
        $this->paginator = $paginator;
    }

    #[Route("/index" , name:"home_index")]
    public function index (Request $request, ManagerRegistry $doctrine) :Response{
       
        $articles = $doctrine->getRepository(Produit::class)->findAll();

        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render("front/index.html.twig", ["articles" => $pagination]);
    }

    #[Route("/contact", name:"home_contact")]
    public function contact():Response{

        return $this->render("front/contact.html.twig");
    }
}
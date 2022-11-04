<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Affichage de tous les genres stockés dans la BDD
class HomeController extends AbstractController
{
    /**
     * @Route("/Home", name="Home_page")
     */
    public function index(): Response
    {
      
        return $this->render('home/home.html.twig');
    }
}
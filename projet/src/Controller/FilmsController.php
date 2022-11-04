<?php
namespace App\Controller;
use App\Entity\Films;
use App\Entity\Genres;
use App\Entity\Acteurs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FilmsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//Affichage de la liste des films
class FilmsController extends AbstractController
{
    /**
     * @Route("/Films", name="app_films")
     */
    public function index(): Response
    {
        $films = $this->getDoctrine()
            ->getRepository(Films::class)
            ->findAll();
        return $this->render('Films/films.html.twig', [
            'controller_name' => 'FilmsController',
            'films'          => $films
        ]);
    }
//Affichage film par film
     /**
     * @Route("/film/{id}", name="film")
     */
    public function tr(FilmsRepository $res ,$id): Response
    {
        $film=$res->find($id);
   
        return $this->render('films/film.html.twig', [
            'controller_name' => 'FilmsController',
            'film'          => $film
        ]);
    }
     /**
     * @Route("/film_genre/{genre_id}", name="film_genre")
     */
    public function liste(FilmsRepository $res ,$genre_id): Response
    {
        $films=$res->findByGenre($genre_id);

        return $this->render('Films/films.html.twig', [
            'controller_name' => 'FilmsController',
            'films'=>$films,
            'genre' => $this->getDoctrine()
            ->getRepository(Genres::class)
            ->find($genre_id)
        ]);
    }

//Création d'un nouveau film qui sera stocké dans la BDD
    /**
    * @Route("/films/create", name="app_films_create")
    */
    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $film              = new Films;
//Insertion des photos
            $affiche = $request->files->get('affiche');
            $affiche_name = $affiche->getClientOriginalName();
            $affiche->move($this->getParameter('affiche_directory'),$affiche_name);

            $film->setTitre($request->request->get('titre'))
                ->setResume($request->request->get('resume'))
                ->setDateDeSortie(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeSortie'))
                )
                -> setAffiche($affiche_name);
//Séléction d'un genre pour le film créé 
            $genre = $this->getDoctrine()
                ->getRepository(Genres::class)
                ->find($request->request->get('genre'));
            $film->setGenre($genre);
//Séléction d'un acteur pour le film créé
                $acteurs = $request->request->get('acteur');
                foreach($acteurs as $acteurID) {
                    $acteur = $this->getDoctrine()
                    ->getRepository(Acteurs::class)
                    ->find($acteurID);
                     $film->addActeur($acteur);

                }

            $manager->persist($film);
            $manager->flush();
       
            return $this->redirectToRoute('app_films');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genres::class)
                ->findAll();
            
            $acteurs = $this->getDoctrine()
                ->getRepository(Acteurs::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('Films/create.html.twig', [
                'controller_name' => 'FilmsController',
                'genres'        => $genres,   
                'acteurs'        => $acteurs       
            ]);
        }
    }
//Modifier un film préexistant
    /**
    * @Route("/films/{film}/edit", name="app_films_edit")
    */
    public function edit(Request $request, Films $film): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();

            $affiche = $request->files->get('affiche');
            $affiche_name = $affiche->getClientOriginalName();
            $affiche->move($this->getParameter('affiche_directory'),$affiche_name);

            $film->setTitre($request->request->get('titre'))
                ->setResume($request->request->get('resume'))
                ->setDateDeSortie(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeSortie'))
                )
                -> getAffiche($affiche_name);

            $genre = $this->getDoctrine()
                ->getRepository(Genres::class)
                ->find($request->request->get('genre'));
            $film->setGenre($genre);
            $film->setAffiche($affiche_name);
            $manager->flush();
            return $this->redirectToRoute('app_films');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genres::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('Films/edit.html.twig', [
                'controller_name' => 'FilmsController',
                'film'           => $film,
                'genres'        => $genres
            ]);
        }
    }
//Supprimer un film de laa BDD
    /**
    * @Route("/films/{film}/delete", name="app_films_delete")
    */
    public function delete(Request $request, Films $film): Response
    {
        $this->getDoctrine()->getManager()->remove($film);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_films');
    }
}
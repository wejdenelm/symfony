<?php
namespace App\Controller;
use App\Entity\Genres;
use App\Repository\FilmsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Affichage de tous les genres stockés dans la BDD
class GenresController extends AbstractController
{
    /**
     * @Route("/Genres", name="app_genres")
     */
    public function index(): Response
    {
        $genres = $this->getDoctrine()
            ->getRepository(Genres::class)
            ->findAll();
        return $this->render('Genres/genres.html.twig', [
            'controller_name' => 'GenresController',
            'genres'          => $genres
        ]);
    }

     /**
     * @Route("/genre_film/{id}", name="genre_film")
     */
    public function liste(FilmsRepository $res ,$id): Response
    {
        $films=$res->findByGenre($id);

        return $this->render('Films/films.html.twig', [
            'controller_name' => 'FilmsController',
            'films'=>$films,
            'genre' => $this->getDoctrine()
            ->getRepository(Genres::class)
            ->find($id)
        ]);
    }
// Création d'un nouveau genre

    /**
    * @Route("/Genres/create", name="app_genres_create")
    */
    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $genre              = new Genres;
            $genre->setNom($request->request->get('nom'));
            $manager->persist($genre);
            $manager->flush();
            return $this->redirectToRoute('app_genres');
        } else {
            // Affichage du formulaire
            return $this->render('genres/create.html.twig', [
                'controller_name' => 'GenresController',
            ]);
        }
    }
// Modifier un genre
    /**
     * @Route("/genres/{genre}/edit", name="app_genres_edit")
     */
    public function edit(Request $request, Genres $genre): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $genre->setNom($request->request->get('nom'));
            $manager->flush();
            return $this->redirectToRoute('app_genres');
        } else {
            // Affichage du formulaire
            return $this->render('Genres/edit.html.twig', [
                'controller_name' => 'GenresController',
                'genre'           => $genre,
            ]);
        }
    }
// Supprimer un genre

    /**
     * @Route("/genres/{genre}/delete", name="app_genres_delete")
     */
    public function delete(Request $request, Genres $genre): Response
    {
        $this->getDoctrine()->getManager()->remove($genre);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_genres');
    }
}
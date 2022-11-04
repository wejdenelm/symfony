<?php
namespace App\Controller;
use App\Entity\Acteurs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ActeursRepository;
class ActeursController extends AbstractController
{
// Affichage de la liste des acteurs préexistants dans la BDD
    /**
    * @Route("/Acteurs", name="app_acteurs")
    */
    public function index(): Response
    {
        $acteurs = $this->getDoctrine()
            ->getRepository(Acteurs::class)
            ->findAll();
        return $this->render('Acteurs/acteurs.html.twig', [
            'controller_name' => 'ActeursController',
            'acteurs'          => $acteurs
        ]);
    }
//Affichage acteur par acteur
     /**
     * @Route("/acteur/{id}", name="acteur")
     */
    public function tr(ActeursRepository $res ,$id): Response
    {
        $acteur=$res->find($id);
        return $this->render('Acteurs/acteur.html.twig', [
            'controller_name' => 'ActeursController',
            'acteur'          => $acteur
        ]);
    }
// Créer un profil pour un nouveau acteur
    /**
    * @Route("/Acteurs/create", name="app_acteurs_create")
    */
    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $acteur              = new Acteurs;
            $photo = $request->files->get('photo');
            $photo_name = $photo->getClientOriginalName();
            $photo->move($this->getParameter('photo_directory'),$photo_name);

            $acteur->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setDateDeNaissance(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeNaissance'))
                )
                ->setDateDeMort(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeMort')) ?: null
                )
                ->setphoto($photo_name);

                
                
            $manager->persist($acteur);
            $manager->flush();
            
            return $this->redirectToRoute('app_acteurs');
        } else {
            return $this->render('acteurs/create.html.twig', [
                'controller_name' => 'ActeursController',
            ]);
        }
    }

//Modifier les coordonnées d'un acteur
    /**
    * @Route("/Acteurs/{acteur}/edit", name="app_acteurs_edit")
    */
    public function edit(Request $request, Acteurs $acteur): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();

            $photo = $request->files->get('photo');
            $photo_name = $photo->getClientOriginalName();
            $photo->move($this->getParameter('photo_directory'),$photo_name);
            
            // Insertion en BDD
            $acteur->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setDateDeNaissance(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeNaissance'))
                )
                ->setDateDeMort(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('dateDeMort')) ?: null
                )
                -> setPhoto($photo_name);
            $manager->persist($acteur);
            $manager->flush();

            return $this->redirectToRoute('app_acteurs');
        } else {
            // Affichage du formulaire
            return $this->render('acteurs/edit.html.twig', [
                'controller_name' => 'ActeursController',
                'acteur'           => $acteur,
            ]);
        }
    }
// Supprimer l'acteur de la BDD
    /**
    * @Route("/Acteurs/{acteur}/delete", name="app_acteurs_delete")
    */
    public function delete(Request $request, Acteurs $acteur): Response
    {
        $this->getDoctrine()->getManager()->remove($acteur);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_acteurs');
    } 
}
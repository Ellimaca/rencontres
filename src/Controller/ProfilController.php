<?php

namespace App\Controller;

use App\Entity\Critere;
use App\Entity\PhotoProfil;
use App\Entity\Profil;
use App\Entity\User;
use App\Form\CritereType;
use App\Form\PhotoProfilType;
use App\Form\ProfilType;
use App\Repository\PhotoProfilRepository;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

class ProfilController extends AbstractController
{
    /**
     * @Route("/create", name="profil_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $manager, UserRepository $userRepository): Response
    {

        // Récupère l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        // Récupère l'ID de l'utilisateur connecté
        $userid = $this->getUser()->getId();
        // Récupère les infos de l'utilisateur connecté
        $result = $userRepository->find($userid);

        // Si l'utilisateur a déjà un profil, on le redirige avec un message flash
        if ($result->getProfil()) {
            $this->addFlash('warning', 'Vous avez déjà un profil');
            return $this->redirectToRoute('main_home');
        }
        // Sinon, on affiche le formulaire et on crée le profil
        else {
            $profil = new Profil();
            $profil->setUser($user);
            $profil->setCoeur(false);

            $profilForm = $this->createForm(ProfilType::class, $profil);
            $profilForm->handleRequest($request);

            if($profilForm->isSubmitted() && $profilForm->isValid()) {
                $manager->persist($profil);
                $manager->flush();

                $this->addFlash('success', 'Bienvenue chez LoveNest ! ');
                return $this->redirectToRoute('profil_ajoutPhoto');

            }

            return $this->render('profil/create.html.twig', [
                "profileForm" =>$profilForm->createView()
            ]);
        }

    }

    /**
     * @Route("/detail/{id}", name="profil_detail")
     */

    public function detail(int $id,
                           ProfilRepository $profilRepository,
                           PhotoProfilRepository $photoProfilRepository) {



        return $this->render('profil/detail.html.twig', [
            "profil" => $profilRepository->find($id)
        ]);
    }

    /**
     * @Route("/ajoutPhoto", name="profil_ajoutPhoto")
     */

    public function ajoutPhoto(Request $request, EntityManagerInterface $manager) {

        //on récupère le user connecté
        /** @var User $user */
        $user = $this->getUser();

        //on récupère l'id du profil de l'utilisateur connecté
        $userProfilId = $user->getProfil()->getId();

        //on instancie une photo de profil
        $photoProfil = new PhotoProfil();

        //création d'un formulaire pour ajouter la photo de profil !
        $photoForm = $this->createForm(PhotoProfilType::class, $photoProfil);
        $photoForm->handleRequest($request);

        if($photoForm->isSubmitted() && $photoForm->isValid()) {

            /** @var UploadedFile $telechargementPhoto */
            $telechargementPhoto = $photoForm->get('file')->getData();
            $nouveauNomPhoto = ByteString::fromRandom(30). '.' .$telechargementPhoto->guessExtension();

            try {
                $telechargementPhoto->move(__DIR__.'/../../public/profile/img', $nouveauNomPhoto);
            } catch(\Exception $e) {
                dd($e->getMessage());
            }

            $photoProfil->setDateCreation(new \DateTime());
            $photoProfil->setUser($user);
            $photoProfil->setNomFichier($nouveauNomPhoto);

            $manager->persist($photoProfil);
            $manager->flush();

            return $this->redirectToRoute('profil_critere');
        }

        return $this->render('profil/ajoutPhoto.html.twig', [
            'photoForm' => $photoForm->createView()
        ]);
    }

    /**
     * @Route("/critere", name="profil_critere")
     */

    public function critere(Request $request, EntityManagerInterface $entityManager) {

        $user = $this->getUser();


        $critere = new Critere();
        $critereForm = $this->createForm(CritereType::class, $critere);
        $critereForm->handleRequest($request);

        if ($critereForm->isSubmitted() && $critereForm->isValid()) {
               $critere->addUser($user);
               $entityManager->persist($critere);
               $entityManager->flush();
        }

        return $this->render('profil/ajoutCritère.html.twig', [
            'critereForm' => $critereForm->createView()
        ]);
    }


    /**
     * @Route("/profil", name="profil_profil")
     */

    public function profil(){

        //on récupère le user connecté
        /** @var User $user */
        $user = $this->getUser();

        //si l'utilisateur a déjà un profil, on le redirige vers le détail deson profil
        if($user->getProfil()) {
            //on récupère l'id du profil de l'utilisateur connecté
            $userProfilId = $user->getProfil()->getId();
            return $this->redirectToRoute('profil_detail', ['id' => $userProfilId]);

        //sinon on le redirige vers la page de création de profil !
        } else {
            return $this->redirectToRoute('profil_create');
        }




    }



}

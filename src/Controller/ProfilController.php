<?php

namespace App\Controller;

use App\Entity\Critere;
use App\Entity\PhotoProfil;
use App\Entity\Profil;
use App\Entity\User;
use App\Form\CritereType;
use App\Form\ModifierProfilType;
use App\Form\PhotoProfilType;
use App\Form\ProfilType;
use App\Repository\CritereRepository;
use App\Repository\PhotoProfilRepository;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;
use function Sodium\add;

class ProfilController extends AbstractController
{

    /**
     * @Route("/detail/{id}", name="profil_detail")
     */

    public function detail(int $id, ProfilRepository $profilRepository) {

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
            $photoProfil->setProfil($user->getProfil());
            $photoProfil->setNomFichier($nouveauNomPhoto);

            $manager->persist($photoProfil);
            $manager->flush();

            $this->addFlash('success', 'Merci pour la/les photo(s)! ');

            if($user->getProfil()->getCriteres()) {
                return $this->redirectToRoute('profil_detail', ['$user->getProfil()->getId()']);
            }

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

        /** @var User $user */
        $user = $this->getUser();

        $critere = new Critere();
        $critereForm = $this->createForm(CritereType::class, $critere);
        $critereForm->handleRequest($request);

        if ($critereForm->isSubmitted() && $critereForm->isValid()) {

                $critere->addProfil($user->getProfil());
                $entityManager->persist($critere);
                $entityManager->flush();

                $this->addFlash('success', 'Critères bien ajoutés à votre profil ! ');
                return $this->redirectToRoute('main_home');


        }

        return $this->render('profil/ajoutCritere.html.twig', [
            'critereForm' => $critereForm->createView()
        ]);
    }


    //Controler si l'utilisateur connecté à un profil ou non afin de le rediriger vers les bonnes pages.
    /**
     * @Route("/profil", name="profil_controller")
     */

    public function profilController(){

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

    /**
     * @Route("/suggestions", name="profil_suggestions")
     */

    public function suggestions(ProfilRepository $profilRepository){

        //on récupère le user connecté
        /** @var User $user */
        $user = $this->getUser();

        $userId = $user->getProfil()->getId();

        if($user->getProfil()->getCriteres()){
            //on récupère les critères de l'utilisateur connecté
            $criteresUtilisateur = $profilRepository->find($userId);

            //on récupère les critères de tout le monde
            $criteresCorrespondants = $profilRepository->findBy(
                ['sexe' => $criteresUtilisateur->getCriteres()->getSexesRecherches(),
                    'CodePostal' => $criteresUtilisateur->getCriteres()->getDepartementsRecherches(),
                    /*'dateNaissance' => $criteresUtilisateur->getCriteres()->getAgeRecherches()*/
                ]);


            if($criteresCorrespondants) {
                return $this->render('profil/suggestions.html.twig', [
                    'criteresCorrespondants' => $criteresCorrespondants
                    ]);
            } else {
                $this->addFlash('warning', "Désolé, vous n'avez aucune suggestion");
            }


        } else {
            $this->addFlash('warning', "Vous n'avez pas de suggestions, veuillez renseigner vos critères");
            return $this->redirectToRoute('profil_critere');
        }

        return $this->render('profil/suggestions.html.twig');

    }

    /**
     * @Route("/modifierProfil{id}", name="profil_modifier_profil")
     */

    public function modifierProfil(ProfilRepository $profilRepository,EntityManagerInterface $manager, Request $request){

        /** @var User $userid */
        //Je récupère l'id du profil de l'utilisateur connecté
        $userid = $this->getUser()->getProfil()->getId();

        //je récupère le profil de l'utilisateur connecté
        $profil =  $profilRepository->find($userid);

        //je crée le form
        $form = $this->createForm(ProfilType::class, $profil);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $profil->setCoeur(false);

                $manager->persist($profil);
                $manager->flush();

            $this->addFlash('success', 'Profil modifié ! ');

                return $this->redirectToRoute('main_home');
        }

            return $this->render('profil/modifierProfil.html.twig', [
                'modifForm' => $form->createView()
            ]);
    }



    /**
     * @Route("/modifierCritere{id}", name="profil_modifier_critere")
     */

    public function modifierCritere(CritereRepository $critereRepository,
                                    EntityManagerInterface $manager,
                                    Request $request){

        /** @var User $userid */
        //Je récupère le profil de l'utilisateur connecté
        $profil = $this->getUser()->getProfil();

        //Je récupère l'id du critère de l'utilisateur connecté
        $critere = $this->getUser()->getProfil()->getCriteres();

        //je récupère les critères de l'utilisateur connecté avec l'id critère
        $criteres =  $critereRepository->find($critere);

        $form = $this->createForm(CritereType::class, $criteres);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $profil->setCriteres($criteres);

            $manager->persist($profil);
            $manager->flush();

            $this->addFlash('success', 'Critères modifiés ! ');

            return $this->redirectToRoute('main_home');
        }

        return $this->render('profil/modifierCritere.html.twig', [
            'modifForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/create", name="profil_create")
     */
    public function create(Request $request,
                           EntityManagerInterface $manager,
                           UserRepository $userRepository): Response
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



}

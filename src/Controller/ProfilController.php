<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\User;
use App\Form\ProfilType;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                return $this->redirectToRoute('main_home');

            }

            return $this->render('profil/create.html.twig', [
                "profileForm" =>$profilForm->createView()
            ]);
        }

    }

    /**
     * @Route("/detail/{id}", name="profil_detail")
     */

    public function detail(int $id, ProfilRepository $profilRepository) {

        return $this->render('profil/detail.html.twig', [
            "profil" => $profilRepository->find($id)
        ]);
    }

}

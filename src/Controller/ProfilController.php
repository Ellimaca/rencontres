<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\User;
use App\Form\ProfilType;
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
                           EntityManagerInterface $manager): Response
    {

        /** @var User $user */
        $user = $this->getUser();

        $profil = new Profil();
        $profil->setUser($user);
        $profil->setCoeur(false);

        $profilForm = $this->createForm(ProfilType::class, $profil);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()) {

            $manager->persist($profil);
            $manager->flush();

            $this->addFlash('success', 'Bienvenue chez LoveNest ! ');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/create.html.twig', [
                "profileForm" =>$profilForm->createView()
        ]);
    }

}

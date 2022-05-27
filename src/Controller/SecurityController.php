<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController 
{

    #[Route('/inscription' , name:'security_registration')]
    public function registration(Request $request , EntityManagerInterface $manager ,  UserPasswordHasherInterface $passwordHasher) {

        $user = new User();
        $form = $this->createForm(RegistrationType::class , $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $user->getPassword();

            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig' , [
            'form' => $form->createView(), 
            'title' => 'Inscription'
        ]);
    }


    #[Route('/connexion', name:'security_login')]
    public function login() :Response 
    {
     
        return $this->render('security/login.html.twig' , [
         'title' => 'Connexion'
        ]);
    }

    #[Route('/deconnexion' , name:'security_logout')]
    public function logout() {}
}

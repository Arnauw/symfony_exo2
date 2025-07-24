<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordUserUpdateFormType;
use App\Form\UserUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_user_profile')]
    public function profile($id): Response
    {
        return $this->render('user_profile/index.html.twig', [
            'controller_name' => 'UserProfileController',
        ]);
    }

    #[Route('/profile/{id}/update', name: 'app_user_profile_update')]
    public function profileUpdate($id, Request $req, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('app_user_profile', ['id' => $id]);
        }

        return $this->render('user_profile/update.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/profile/{id}/update/password', name: 'app_user_profile_update_password')]
    public function profileUpdatePassword($id, Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(PasswordUserUpdateFormType::class, $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/update_password.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/profile/{id}/delete', name: 'app_user_profile_delete')]
    public function profileDelete($id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );
        return $this->redirectToRoute('app_homepage');
    }


}

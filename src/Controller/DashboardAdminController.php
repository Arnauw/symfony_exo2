<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class DashboardAdminController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('dashboard_admin/admin.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/update/{id}', name: 'app_dashboard_admin_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function update($id, Request $req, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        $form = $this->createForm(AdminUpdateFormType::class, $user);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('app_dashboard_admin');

        }
        return $this->render('dashboard_admin/update.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_admin_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete($id, EntityManagerInterface $em): Response
    {

        $user = $em->getRepository(User::class)->find($id);


        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );

        return $this->redirectToRoute('app_dashboard_admin');
    }
}

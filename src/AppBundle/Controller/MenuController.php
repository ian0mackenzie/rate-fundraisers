<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuController extends AbstractController
{
    #[Route('/', name: 'menu_index')]
    public function indexAction(AuthorizationCheckerInterface $authChecker): Response
    {
        $menu[] = ["name" => "Fundraisers","link" => $this->generateUrl('fundraiser_index')];
        $menu[] = ["name" => "Quick Stats","link" => $this->generateUrl('app_stats')];

        if($authChecker->isGranted('ROLE_USER')){
            $menu[] = ["name" => "Log Out","link" => $this->generateUrl('fos_user_security_logout')];
        } else {
            $menu[] = ["name" => "Sign In/Register","link" => $this->generateUrl('fos_user_security_login')];
        }

        return $this->render('menu/index.html.twig', [
            'menuItems' => $menu,
        ]);
    }
}
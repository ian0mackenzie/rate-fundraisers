<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class MenuController extends Controller
{
    /**
     *
     * @Route("/", name="menu_index")
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
		$menu[] = array("name" => "Fundraisers","link" => $this->generateUrl('fundraiser_index'));

        if($auth_checker->isGranted('ROLE_USER')){
	       	$menu[] = array("name" => "Log Out","link" => $this->generateUrl('fos_user_security_logout'));
        } else {
    		$menu[] = array("name" => "Sign In/Register","link" => $this->generateUrl('fos_user_security_login'));
        }

        return $this->render('menu/index.html.twig', [
            'menuItems' => $menu,
        ]);
    }
} 
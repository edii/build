<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository(Page::class);
        $pages = $em->findAll();
        $token = $this->get('security.token_storage')->getToken()->setAuthenticated(false);
        return $this->render('default/index.html.twig', ['title' => 'Accueil', 'menu' => $pages]);
    }


    /**
     * @Route(name="menu")
     */
    public function dislayMenuAction($classNames = ''){
        $em = $this->getDoctrine()->getRepository(Page::class);
        $pages = $em->findAll();
        return $this->render('menu.html.twig', ['pages' => $pages, 'classNames' => $classNames]);
    }
    /**
     * @Route("/{slug}", name="page")
     */
    public function showPageAction(Request $request, $slug){
        $em = $this->getDoctrine()->getRepository(Page::class);
        $page = $em->findOneBy(['slug' => $slug]);
        if($page == null){
            throw new NotFoundHttpException('Page non trouvé');
        }
        return $this->render('default/page.html.twig', ['page' => $page]);
    }





}

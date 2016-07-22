<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectCategory;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{

    /**
     * @Route("/", name="admin_index")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request){
        return $this->render('admin/index.html.twig', ['test']);


    }


    /**
     * @Route("/posts/", name="blog_list_posts")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listPostAction(Request $request){
        $em = $this->getDoctrine()->getRepository(Post::class);
        $posts = $em->findAll('DESC');
        return $this->render('admin/blog_list_posts.html.twig', ['posts' => $posts]);


    }

    /**
     * @Route("/projects/", name="portfolio_list_projects")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listProjectsAction(Request $request){
        $em = $this->getDoctrine()->getRepository(Project::class);
        $projects = $em->findAll();
        return $this->render('admin/portfolio_list_projects.html.twig', ['projects' => $projects]);


    }





}

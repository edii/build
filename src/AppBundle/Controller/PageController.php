<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @Route("/admin/page")
 * @Security("has_role('ROLE_ADMIN')")
 */
class PageController extends Controller
{




    /**
     * @Route("/", name="page_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexction(Request $request){
        return $this->render('page/page_index.html.twig', ['pages' => $this->getDoctrine()->getRepository(Page::class)->findAll()]);
    }


    /**
     * @Route("/add", name="page_add")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request){
        if($request->getMethod() == 'POST'){
            $parsedown = new \Parsedown();
            $page = new Page();
            $em = $this->getDoctrine()->getManager();
            $page->setName($request->get('pageTitle'));
            $page->setSubtitle($request->get('pageSubtitle'));
            $page->setContent($parsedown->text($request->get('pageContent')));
            $page->setIsFullscreen(($request->get('fullscreen') == null) ? false : true);
            $page->setRandomHeaderColors(true);
            $page->setSlug(self::slugify($page->getName()));
            $searchPage = $this->getDoctrine()->getRepository(Page::class)->findOneBy(['name' => $page->getName()]);
            if($searchPage == null) {
                $em->persist($page);
                $em->flush();
                $this->addFlash('success', "La page \"" . $page->getName() . "\" a bien été ajouté ! <a href=\"" . $this->generateUrl('page', ['slug' => $page->getSlug()]) . "\">Voir la page</a>");
                return $this->redirectToRoute('page_list');
            } else {
                $this->addFlash('danger', 'La page existe déjà ! Mais je t\'ai ai redirigé directement vers la page de modification de cette page, me remercie pas jsuis tron bon');
                return $this->redirectToRoute('page_edit', ['id' => $searchPage->getId()]);

            }



        } else {
            return $this->render('page/page_add.html.twig');

        }
    }

    /**
     * @Route("/edit/{id}", name="page_edit")
     */
    public function editAction(Request $request, $id){
        $parsedown = new \Parsedown();
        $em = $this->getDoctrine()->getManager();
        $page = $this->getDoctrine()->getRepository(Page::class)->find($id);
        if(!$page){
            throw $this->createNotFoundException();
        }
        if($request->getMethod() == 'POST'){
            $page->setName($request->get('pageTitle'));
            $page->setSubtitle($request->get('pageSubtitle'));
            $page->setContent($parsedown->text($request->get('pageContent')));
            $page->setIsFullscreen(($request->get('fullscreen') == null) ? false : true);
            $page->setRandomHeaderColors(true);
            $page->setSlug(self::slugify($page->getName()));

            $em->flush();
            $this->addFlash('success', 'La page a bien été modifié !');
            return $this->redirectToRoute('page', ['slug' => $page->getSlug()]);

        } else {
            return $this->render('page/page_edit.html.twig', ['page' => $page]);

        }
    }

    /**
     * @Route("/delete/{id}", name="page_delete")
     */
    public function deleteAction(Request $request, $id){
        $page = $this->getDoctrine()->getRepository(Page::class)->find($id);
        if(!$page){
            throw $this->createNotFoundException("La page n'a pas été trouvé");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();
        $this->addFlash('success', 'La page a bien été supprimé !');
        return $this->redirectToRoute('page_list');
    }

    static public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }
}

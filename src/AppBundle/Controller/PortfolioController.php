<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectCategory;
use AppBundle\Entity\ProjectPlatform;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PortfolioController extends Controller
{
    /**
     * @Route("/portfolio/", name="portfolio_index")
     */
    public function indexAction(Request $request)
    {
        $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();
        return $this->render('portfolio/index.html.twig', ['projects' => $projects]);
    }

    /**
     * @Route("/portfolio/project/{slug}", name="project_detail")
     */
    public function projectDetailAction(Request $request, Project $project){
        if(!$project) {
            throw $this->createNotFoundException('Projet non trouvé !');
        }
        return $this->render('portfolio/project_detail.html.twig', ['project' => $project]);
    }

    /**
     * @Route("/admin/project/edit/{id}", name="project_edit")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editProjectAction(Request $request, Project $project){
        $parsedown = new \Parsedown();
        if(!$project){
            throw $this->createNotFoundException('Projet non trouvé !');
        }
        if($request->getMethod() == 'POST'){
            $project->setName($request->get('projectName'));
            $project->setSlug(self::slugify($project->getName()));
            $project->setDescription($request->get('projectDescription'));
            $project->setContent($parsedown->text($request->get('projectContent')));
            foreach($project->getCategories() as $category){
                $project->removeCategory($category);
            }
            if($request->get('categories') != null){
                foreach($request->get('categories') as $categoryId){
                    $category = $this->getDoctrine()->getRepository(ProjectCategory::class)->find($categoryId);
                    $project->addCategory($category);
                }
            }
            foreach($project->getPlatforms() as $platform){
                $project->removePlatform($platform);
            }
            if($request->get('platforms') != null){
                foreach($request->get('platforms') as $platformId){
                    $platform = $this->getDoctrine()->getRepository(ProjectPlatform::class)->find($platformId);
                    $project->addPlatform($platform);
                }
            }
            if($request->get('projectLanguages') != null){
                $languages = $request->get('projectLanguages');
                $languages = explode(",", trim($languages));
            } else $languages = null;
            $project->setLanguages($languages);
            $project->setImageUrl($request->get('projectThumbnail'));
            $project->setProjectImages($request->get('imageUrls'));
            $project->setProjectImagesDescription($request->get('imageDescriptions'));
            $searchProject = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['name' => $project->getName()]);
            if($searchProject == null || $project->getId() == $searchProject->getId()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
                $this->addFlash('success', 'Le projet a bien été modifié !');
                return $this->redirectToRoute('project_detail', ['slug' => $project->getSlug()]);
            } else {
                $this->addFlash('danger', "Le projet existe déjà ! Veuillez choisir un autre nom.");
                return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
            }
        } else {
            return $this->render('portfolio/edit_project.html.twig', ['project' => $project,
                'categories' => $this->getDoctrine()->getRepository('AppBundle:ProjectCategory')->findAll(),
                'platforms' => $this->getDoctrine()->getRepository(ProjectPlatform::class)->findAll()]);

        }

    }

    /**
     * @Route("/admin/project/add", name="project_add")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addProjectAction(Request $request){
        $parsedown = new \Parsedown();
        if($request->getMethod() == 'POST'){
            $project = new Project();
            $project->setName($request->get('projectName'))
            ->setSlug(self::slugify($project->getName()))
            ->setDescription($request->get('projectDescription'))
            ->setContent($parsedown->text($request->get('projectContent')))
            ->setImageUrl($request->get('projectThumbnail'));
            if($request->get('categories') != null){
                foreach($project->getCategories() as $category){
                    $project->removeCategory($category);
                }
                foreach($request->get('categories') as $categoryId){
                    $category = $this->getDoctrine()->getRepository(ProjectCategory::class)->find($categoryId);
                    $project->addCategory($category);
                }
            }
            if($request->get('platforms') != null){
                foreach($project->getPlatforms() as $platform){
                    $project->removePlatform($platform);
                }
                foreach($request->get('platforms') as $platformId){
                    $platform = $this->getDoctrine()->getRepository(ProjectPlatform::class)->find($platformId);
                    $project->addPlatform($platform);
                }
            }
            if($request->get('projectLanguages') != null){
                $languages = $request->get('projectLanguages');
                $languages = explode(",", trim($languages));
            } else $languages = null;
            $project->setLanguages($languages)
            ->setCreatedAt(new \DateTime())
            ->setProjectImages($request->get('imageUrls'))
            ->setProjectImagesDescription($request->get('imageDescriptions'));
            $searchProject = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['name' => $project->getName()]);
            if($searchProject == null || $project->getId() == $searchProject->getId()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
                $this->addFlash('success', 'Le projet a bien été ajouté !');
                return $this->redirectToRoute('project_detail', ['slug' => $project->getSlug()]);
            } else {
                $this->addFlash('danger', "Le projet existe déjà ! Veuillez choisir un autre nom.");
                return $this->redirectToRoute('project_add');
            }
        } else {
            return $this->render('portfolio/add_project.html.twig', ['categories' => $this->getDoctrine()->getRepository('AppBundle:ProjectCategory')->findAll(),
                'platforms' => $this->getDoctrine()->getRepository(ProjectPlatform::class)->findAll()]);

        }

    }

    /**
     * @Route("/admin/project/delete/{id}", name="project_delete")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteProjectAction(Request $request, Project $project){
        if(!$project){
            throw new NotFoundHttpException("Le projet n'a pas été trouvé !");
        }
        $this->getDoctrine()->getManager()->remove($project);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "Le projet a bien été supprimé !");
        return $this->redirectToRoute('portfolio_list_projects');
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

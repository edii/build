<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;

class BlogController extends Controller
{
    /**
     * @Route("/blog/", name="blog_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository(Post::class);
        $posts = $em->findAll();
        return $this->render('blog/blog_index.html.twig', ['posts' => $posts]);
    }

//    /**
//     * @Route("/{day}/{month}/{year}/", name="blog_show_post_created_at", requirements={"day": "\d+", "month": "\d+", "year": "\d+"})
//     */
//    public function showPostCreatedAtAction(Request $request, $day, $month, $year){
//        $em = $this->getDoctrine()->getRepository(Post::class);
//        $post = $em->getPostsByDate($day, $month, $year);
//        var_dump($post);
//    }

    /**
     * @Route("/blog/{day}/{month}/{year}/{slug}/", name="blog_show_post", requirements={"day": "\d+", "month": "\d+", "year": "\d+"})
     */
    public function showPostAction(Request $request, $day, $month, $year, $slug){
        $em = $this->getDoctrine()->getRepository(Post::class);
        $post = $em->getPostsByDateAndSlug($day, $month, $year, $slug);
        return $this->render('blog/blog_show_post.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/blog/{day}/{month}/{year}/{slug}/comment/add/", name="blog_add_comment_to_post", requirements={"day": "\d+", "month": "\d+", "year": "\d+"})
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentToPostAction(Request $request, $day, $month, $year, $slug){
        $em = $this->getDoctrine()->getRepository(Post::class);
        $post = $em->getPostsByDateAndSlug($day, $month, $year, $slug);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime('now'))
            ->setContent($request->get('commentContent'))
            ->setAuthor($user)
            ->setPost($post);
        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('blog_show_post', ["day" => $day, "month" => $month, "year" => $year, "slug" => $slug]);

    }

    /**
     * @Route("/blog/{day}/{month}/{year}/{slug}/comment/delete/{id}", name="blog_delete_comment_from_post", requirements={"day": "\d+", "month": "\d+", "year": "\d+"})
     * @Method({"POST"})
     */
    public function deleteCommentFromPostAction(Request $request, $day, $month, $year, $slug, $id){
        $this->getDoctrine()->getManager()->remove($this->getDoctrine()->getRepository(Comment::class)->find($id));
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('blog_show_post', ["day" => $day, "month" => $month, "year" => $year, "slug" => $slug]);

    }


    /**
     * @Route("/admin/post/edit/{id}/", name="blog_edit_post", requirements={"day": "\d+", "month": "\d+", "year": "\d+"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_WRITER')")
     */
    public function editPostAction(Request $request, Post $post){
        if(!$post){
            throw $this->createNotFoundException("L'article n'a pas été trouvé");
        }
        $parsedown = new \Parsedown();
        if($post->getAuthor()->getUsername() != $this->get('security.token_storage')->getToken()->getUsername() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier l'article d'un autre");
        }
        if($request->getMethod() == 'POST'){
            $post->setTitle($request->get('postTitle'));
            $post->setSubtitle($request->get('postSubtitle'));
            $post->setContent($parsedown->text($request->get('postContent')));
            $date = new \DateTime('now');
            $post->setEditedAt($date);
            $post->setCategory($this->getDoctrine()->getRepository(Category::class)->find($request->get('postCategory')));
            $post->setSlug(self::slugify($post->getTitle()));
            $post->setThumbnailUrl($request->get('postThumbnail'));
            $searchPost = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['title' => $post->getTitle()]);
            if($searchPost == null || $searchPost->getId() == $post->getId()){
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('success', 'L\'article a bien été modifié !');
                return $this->redirectToRoute('blog_show_post', ['day' => $post->getCreatedAt()->format('d'),
                    'month' => $post->getCreatedAt()->format('m'),
                    'year' => $post->getCreatedAt()->format('Y'),
                    'slug' => $post->getSlug()]);
            } else {
                $this->addFlash('danger', "L'article existe déjà ! Veuillez choisir un autre nom.");
                return $this->redirectToRoute('blog_edit_post', ['id' => $post->getId()]);
            }


        } else {
            return $this->render('blog/blog_edit_post.html.twig', ['post' => $post, 'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()]);

        }
    }

    /**
     * @Route("/admin/post/add/", name="blog_add_post")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_WRITER')")
     * @Method({"GET", "POST"})
     */
    public function addPostAction(Request $request){
        $parsedown = new \Parsedown();
        if($request->getMethod() == 'POST'){
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $post = new Post();
            $post->setTitle($request->get('postTitle'));
            $post->setSubtitle($request->get('postSubtitle'));
            $post->setContent($parsedown->text($request->get('postContent')));
            $date = new \DateTime('now');
            $post->setCreatedAt($date);
            $post->setAuthor($user);
            $post->setCategory($this->getDoctrine()->getRepository(Category::class)->find($request->get('postCategory')));
            $post->setEditedAt($date);
            $post->setSlug(self::slugify($post->getTitle()));
            $post->setThumbnailUrl($request->get('postThumbnail'));
            $em = $this->getDoctrine()->getManager();
            $searchPost = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['title' => $post->getTitle()]);
            if($searchPost == null) {
                $em->persist($post);
                $em->flush();
                $this->addFlash('success', "L'article \"" . $post->getTitle() . "\" a bien été ajouté !");
                if($this->isGranted('ROLE_ADMIN')){
                    return $this->redirectToRoute('blog_list_posts');
                } elseif($this->isGranted('ROLE_WRITER')) {
                    return $this->redirectToRoute('blog_show_post', ['day' => $post->getCreatedAt()->format('d'),
                        'month' => $post->getCreatedAt()->format('m'),
                        'year' => $post->getCreatedAt()->format('Y'),
                        'slug' => $post->getSlug()]);
                }
            } else {
                $this->addFlash('danger', 'L\'article existe déjà ! Mais je t\'ai ai redirigé directement vers la page de modification de cet article, me remercie pas jsuis tron bon');
                return $this->redirectToRoute('blog_edit_post', ['id' => $searchPost->getId()]);

            }
        }
        else
        {
            return $this->render('blog/blog_add_post.html.twig', ['categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()]);
        }
    }

    /**
     * @Route("/admin/post/delete/{id}", name="blog_post_delete")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_WRITER')")
     */
    public function deletePostAction(Request $request, Post $post){
        if(!$post){
            throw $this->createNotFoundException('L\'article n\'a pas été troué !');
        }
        if($post->getAuthor()->getUsername() != $this->get('security.token_storage')->getToken()->getUsername() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer l'article d'un autre");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', "L'article à bien été supprimé !");
        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('blog_list_posts');
        } elseif ($this->isGranted('ROLE_WRITER')){
            return $this->redirectToRoute('blog_index');
        }
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

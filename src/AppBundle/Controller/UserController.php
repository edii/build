<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{

    /**
     * @Route("/admin/user/", name="user_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('user/user_list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/admin/user/edit/{username}", name="edit_profile")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editProfileAction(Request $request, User $user){
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur n'a pas été trouvé");
        }
        if($request->getMethod() == "GET"){
            $roles = $this->getDoctrine()->getRepository(Role::class)->findAll();
            return $this->render('user/user_edit.html.twig', ['user' => $user, 'availableRoles' => $roles]);
        } else {
            $canBeAdmashallah = ($user->getUsername() == "Linking") ? true : false;
            $rolesId = $request->get('roles');
            foreach($user->getRolesObject() as $role){
                $user->removeRole($role);
            }
            foreach($rolesId as $roleId){
                $tempRole = $this->getDoctrine()->getRepository(Role::class)->find($roleId);
                if($tempRole->getName() == "L'admashallah" && !$canBeAdmashallah) {
                    $this->addFlash('danger', "Si tu ne sais pas lire, y'a que Omar (Linking) qui peut devenir Admashallah. FDP lis mieux la prochaine fois");
                    return $this->redirectToRoute('edit_profile', ['username' => $user->getUsername()]);
                }
                $user->addRole($tempRole);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Les permissions ont bien été modifiées");
            return $this->redirectToRoute('edit_profile', ["username" => $user->getUsername()]);
        }
    }

    /**
     * @Route("/admin/user/delete/{username}", name="delete_profile")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteProfileAction(Request $request, User $user){
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur n'a pas été trouvé");
        }
        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "L'utilisateur a bien été supprimé !");
        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/profile/me", name="my_profile")
     */
    public function showMyProfileAction(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user != "anon."){
            return $this->render('user/my_profile.html.twig', ['title' => $user->getUsername(), 'subtitle' => 'Votre profil', 'isFullscreen' => true]);
        }
        else {
            $this->addFlash('danger', "Vous devez être connecté avant d'accéder à cette page !");
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/profile/{username}", name="profile")
     */
    public function showProfileAction(User $user){
        if(!$user){
            throw $this->createNotFoundException("L'utilisateur n'a pas été trouvé");
        }
        return $this->render('user/profile.html.twig', ['user' => $user, 'title' => $user->getUsername(), 'subtitle' => 'Son profil', 'isFullscreen' => true]);
    }


}

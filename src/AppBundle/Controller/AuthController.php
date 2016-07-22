<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserType;

class AuthController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request){
        $authUtils = $this->get('security.authentication_utils');
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        if($error){
            switch ($error->getCode()){
                case 0:
                    $this->addFlash('danger', "Mauvaise combinaison utilisateur/mot de passe");
                    break;
            }

        }

        return $this->render('auth/login.html.twig', ['last_username' => $lastUsername]);

    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request){
        $this->addFlash('success', 'Vous avez bien été déconnecté');
    }


    /**
     * @Route("/register", name="register")
     * @Method({"POST", "GET"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);

        if($request->getMethod() == 'POST'){
            $user->setUsername($request->get('username'));
            $user->setEmail($request->get('email'));
            $user->setPlainPassword($request->get('plainPassword'));
            $pass = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($pass);
            $user->setCreatedAt(new \DateTime());
            $user->addRole($this->getDoctrine()->getRepository(Role::class)->findOneBy(['role' => 'ROLE_USER']));
            $em = $this->getDoctrine()->getManager();
            if($em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]) == null || $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]) == null){
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('homepage');

            } else {
                return $this->render('auth/register.html.twig', ['form' => $form->createView(), 'error' => "L'utilisateur ou l'adresse email est déjà utilisé !"]);

            }

        } else return $this->render('auth/register.html.twig');



    }
}

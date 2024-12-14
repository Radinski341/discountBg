<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Favourite;
use App\Entity\User;
use App\Form\Public\ChangePasswordFormType;
use App\Form\Public\LoginFormType;
use App\Form\Public\PersonalInfoFormType;
use App\Form\Public\RegisterFormType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class);
        $formView = $form->createView();
        $formView['email']->vars['id'] = 'loginForm';
        $formView['email']->vars['full_name'] = 'email';

        $formView['password']->vars['full_name'] = 'password';
        $formView['password']->vars['id'] = 'inputPassword';

        $formView['_token']->vars['full_name'] = '_csrf_token';



        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $formView
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', 'app_register')]
    public function register(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper, MailerService $mailerService) :Response
    {

        $form = $this->createForm(RegisterFormType::class, null, [
            'validation_groups' => 'registration'
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $formEmail = $form->getData()->getEmail();
            $formPassword = $form->getData()->getPlainPassword();
            $formRepeatPassword = $form->get('repeatPassword')->getData();
            $formCity = $form->getData()->getCity();
            $formPopulatedPlace = $form->getData()->getPopulatedPlace();
            if($userRepository->findOneBy(['email' => $formEmail])){
                $this->addFlash('error', 'Потребител сос е-мейл '.$formEmail.' вече постой!');
                return $this->redirectToRoute('app_register');
            }

            if($formPassword !== $formRepeatPassword){
                $this->addFlash('error', 'Паролите не совпадат, опитайте од ново');
                return $this->redirectToRoute('app_register');
            }

            $user->setPassword(
                $passwordHasher->hashPassword($user, $formPassword)
            );
            if($formPopulatedPlace === null) $user->setPopulatedPlace($formCity);

            $cart = new Cart();
            $user->setCart($cart);

            $favourite = new Favourite();
            $user->setFavourite($favourite);

            $entityManager->persist($favourite);
            $entityManager->persist($user);
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('change-password', name: 'app_change_password')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            // Verify current password
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Грещна парола, опитайте от ново');
                return $this->redirectToRoute('app_change_password');
            } elseif ($newPassword !== $confirmPassword) {

                $this->addFlash('error', 'Паролите не съвпадат, опитайте от ново');
                return $this->redirectToRoute('app_change_password');
            } else {

                // Hash new password and save
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Успешно променихте паролата си');
                return $this->redirectToRoute('app_public_profile');
            }
        }

        return $this->render('security/change-password.html.twig', [
            'changePasswordForm' => $form->createView(),
            'currentProfilePage' => 'change-password'
        ]);
    }

    #[Route('change-info', name: 'app_change_info')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function changePersonalInfo(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(PersonalInfoFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Personal information updated successfully.');
            return $this->redirectToRoute('app_public_profile');
        }

        return $this->render('security/change-info.html.twig', [
            'personalInfoForm' => $form->createView(),
            'currentProfilePage' => 'personal-info'
        ]);
    }
}

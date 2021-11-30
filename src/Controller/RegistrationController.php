<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\UserRegisterEvent;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
    
            $event = $dispatcher->dispatch(new UserRegisterEvent($user));
            if(! $event->getUser()->isVerified()) {
                $this->addFlash('flash_message', 'Для завершения регистрации подтвердите ваш email');
            }
            
            return $this->redirectToRoute('app_register');
        }

        return $this->render('register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        EmailVerifier $emailVerifier
    ): Response {
        $id = $request->get('id');
    
        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }
    
        $user = $userRepository->find($id);
    
        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Адрес электронной почты был успешно подтверждён!');

        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
    }
}

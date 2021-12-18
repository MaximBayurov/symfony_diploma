<?php

namespace App\Controller\Dashboard;

use App\Entity\FlashMessage;
use App\Entity\User;
use App\Events\User\BeforeProfileChangedEvent;
use App\Events\UserEvent;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    #[Route('/dashboard/profile', name: 'app_dashboard_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator,
    ): Response {
        
        /**
         * @var PasswordAuthenticatedUserInterface|User $user
         * @var PasswordAuthenticatedUserInterface|User $currentUser
         */
        $currentUser = clone $this->getUser();
        $user = $this->getUser();
        
        if ($user->getApiToken()->isExpired()) {
            $this->addFlash('flash_message',
                new FlashMessage(
                    $translator->trans('Api token expired'),
                    'warning'
                )
            );
        }
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
        
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
    
            /**
             * @var UserEvent $event
             */
            $event = $dispatcher->dispatch(new BeforeProfileChangedEvent($user, $currentUser));
            foreach ($event->getFlashMessages() as $flashMessage) {
                $this->addFlash('flash_message',
                    $flashMessage
                );
            }
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_dashboard_profile');
        }
    
        return $this->render('dashboard/profile.html.twig', [
            'userForm' => $form->createView(),
            'token' => $user->getApiToken()?->getToken()
        ]);
    }
}

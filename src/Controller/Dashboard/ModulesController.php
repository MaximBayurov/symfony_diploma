<?php

namespace App\Controller\Dashboard;

use App\Entity\FlashMessage;
use App\Entity\GeneratorModule;
use App\Entity\User;
use App\Form\GeneratorModuleType;
use App\Repository\GeneratorModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModulesController extends AbstractController
{
    #[Route('/dashboard/modules', name: 'app_dashboard_modules')]
    public function index(
        GeneratorModuleRepository $moduleRepository,
        PaginatorInterface  $paginator,
        Request $request,
        EntityManagerInterface $manager,
        TranslatorInterface $translator,
    ): Response    {
        if (!$this->isGranted('SUBSCRIPTION_PRO')) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('To add your own modules, you need a PRO subscription')
            ));
            
            return $this->redirectToRoute('app_dashboard_subscription');
        }
    
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(
            GeneratorModuleType::class,
            new GeneratorModule()
        );
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            /**
             * @var GeneratorModule $module
             */
            $module = $form->getData();
            $module
                ->setUser($user)
                ->setUpdatedAt(new \DateTime('now'))
                ->setCreatedAt(new \DateTime('now'))
            ;
    
            $manager->persist($module);
            $manager->flush();

            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('Module added successfully')
            ));
            
            return $this->redirectToRoute('app_dashboard_modules');
        }
        
        $pagination = $paginator->paginate(
            $moduleRepository->getUserLatest($user->getId()),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('dashboard/modules.html.twig', [
            'pagination' => $pagination,
            'moduleForm' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller\Api;

use App\Entity\FlashMessage;
use App\Entity\GeneratorModule;
use App\Entity\User;
use App\Repository\GeneratorModuleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModulesController extends AbstractController
{
    #[IsGranted('SUBSCRIPTION_PRO')]
    #[Route('/api/v1/modules/{id}/delete', name: 'api_modules')]
    public function index(
        GeneratorModule $module,
        TranslatorInterface $translator,
        GeneratorModuleRepository $moduleRepository,
    ): Response {
    
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userModules = $user->getGeneratorModules();
        if (!$userModules->contains($module)) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }
    
        if ($moduleRepository->deleteByID($module->getId())) {
            $this->addFlash('flash_message', new FlashMessage(
                $translator->trans('Module #id successfully removed', [
                    'id' => $module->getId()
                ])
            ));
        }
        
        return $this->json([]);
    }
}

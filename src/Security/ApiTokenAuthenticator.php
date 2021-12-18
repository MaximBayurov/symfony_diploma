<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;
    
    const AUTH_HEADER_PREFIX = 'Bearer ';
    
    public function __construct(
        private ApiTokenRepository $repository,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }
    
    public function supports(Request $request): ?bool
    {
        return (
            $request->headers->has('Authorization')
            && 0 === mb_strpos($request->headers->get('Authorization'), self::AUTH_HEADER_PREFIX)
        );
    }
    
    public function authenticate(Request $request): PassportInterface
    {
        $token = mb_substr($request->headers->get('Authorization'), strlen(self::AUTH_HEADER_PREFIX));
        $apiToken = $this->repository->findOneBy([
            'token' => $token
        ]);
        
        $user = $apiToken->getUser();
        $request->getSession()->set(Security::LAST_USERNAME, $user->getEmail());
        
        if (empty($apiToken)) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('Invalid token')
            );
        }
        if ($apiToken->isExpired()) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('Api token expired')
            );
        }
        
        return new Passport(
            new UserBadge($user->getEmail()),
            new CustomCredentials(
                function ($credentials, $user) {
                    return true;
                },
                [
                    'apiToken' => $apiToken,
                ]
            )
        );
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'message' => $exception->getMessage(),
            ],
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}

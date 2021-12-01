<?php

namespace App\DataFixtures;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    const USERS_PASSWORD = '123456';
    
    private UserPasswordHasherInterface $userPasswordHasher;
    
    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
    ) {
        
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function loadData(): void
    {
        $this->create(
            User::class,
            function (User $user) {
                $user
                    ->setEmail('admin@symfony.skillbox')
                    ->setFullName('Add Man')
                    ->setIsVerified(true)
                    ->setRoles(["ROLE_ADMIN"])
                ;
    
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        self::USERS_PASSWORD
                    )
                );
            }
        );
    
        $this->createMany(
            User::class,
            10,
            function (User $user) {
                $user
                    ->setEmail($this->faker->email)
                    ->setFullName($this->faker->firstName." ". $this->faker->lastName)
                    ->setIsVerified(true)
                ;
    
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        self::USERS_PASSWORD
                    )
                );
            
                if ($this->faker->boolean(30)) {
                    $user->setIsVerified(false);
                }
            }
        );
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Avatar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $json = file_get_contents(__DIR__ . '/usersDatas.json');

        // Convertir le JSON en tableau associatif
        $usersArray = json_decode($json, true);

        // Itérer sur chaque utilisateur et créer une entité User correspondante
        foreach ($usersArray as $key => $userAttr) {
            $user = $this->createUser(
                $userAttr['firstName'],
                $userAttr['lastName'],
                $userAttr['email'],
                $userAttr['password'],
            );
            $manager->persist($user);
            $this->addReference('user_0' . $key, $user);
        }
        $manager->flush();
    }
    private function createUser(string $email, string $password, string $firstName, string $lastName): User
    {
        $user = new User();
        $user->setEmail($email)
            ->setPassword($this->hasher->hashPassword($user, $password))
            ->setRoles(['ROLE_USER']) // rôle par défaut
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUserUuid(Uuid::v4());
            $avatar = new Avatar();
             $avatar->setImageUrl('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
             $user->setAvatar($avatar);

        return $user;
    }
}

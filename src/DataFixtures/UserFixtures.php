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

    public const USER_REFERENCE = "user-ref";

    public function load(ObjectManager $manager): void
    {
        $usersDatas = json_decode(file_get_contents(__DIR__ . '/usersDatas.json'), true);
        $allUsers = [];


        foreach ($usersDatas as $userAtt) {
            $user = $this->createUser(
                $userAtt['firstName'],
                $userAtt['lastName'],
                $userAtt['email'],
                $userAtt['password'],
            );
            $manager->persist($user);

            $allUsers[] = $user;
        }
        $manager->flush();
    }
    private function createUser(string $firstName, string $lastName,string $email, string $password): User
    {
        $user = new User();
        $user->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail($email)
            ->setPassword($this->hasher->hashPassword($user, $password))
            ->setRoles(['ROLE_USER']) // rôle par défaut
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUserUuid(Uuid::v4());
        $avatar = new Avatar();
        $avatar->setName('avatar');
        $avatar->setImageUrl('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
        $avatar->setPath('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
        $avatar->setAvatar('https://img.freepik.com/vecteurs-libre/homme-affaires-caractere-avatar-isole_24877-60111.jpg?size=626&ext=jpg');
        $user->setAvatar($avatar);

        $this->addReference(self::USER_REFERENCE . '_' . $firstName . '-' . $lastName, $user);

        return $user;
    }
}

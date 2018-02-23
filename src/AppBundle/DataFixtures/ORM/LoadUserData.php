<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('testuser')
            ->setEmail('test@test.com')
            ->setEnabled(true)
            ->setPlainPassword('p@ssword')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->addReference('user', $user);
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 3;
    }
}

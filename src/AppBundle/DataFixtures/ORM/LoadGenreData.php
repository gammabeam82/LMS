<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Genre;

class LoadGenreData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getGenres() as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    private function getGenres(): array
    {
        return [
            'НФ',
            'Программирование',
            'Приключения',
            'Современная литература',
            'Юмор',
            'Документальная литература',
            'Справочная литература',
            'Детектив',
            'Историческая литература',
            'Ужасы'
        ];
    }
}

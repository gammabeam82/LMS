<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Author;

class LoadAuthorData extends AbstractFixture implements OrderedFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		foreach ($this->getAuthors() as $authorData) {
			$nameParts = explode(" ", $authorData);
			$author = new Author();
			$author->setFirstName($nameParts[0]);
			$author->setLastName($nameParts[1]);
			$manager->persist($author);
		}

		$manager->flush();
	}

	public function getOrder()
	{
		return 2;
	}

	private function getAuthors()
	{
		return [
			'Владимир Сорокин',
			'Чарльз Буковски',
			'Луи-Фердинанд Селин',
			'Питер Уоттс',
			'Аластер Рейнольдс',
			'Роберт Уилсон',
			'Питер Гамильтон',
			'Джеральд Даррел',
			'Сергей Довлатов',
			'Клайв Баркер',
			'Чарли Стросс',
			'Владимир Короткевич',
		];
	}
}
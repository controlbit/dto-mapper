<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony\App\Fixtures;

use ControlBit\Dto\Tests\Bridge\Symfony\App\Entity\SampleEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public static string $ID;

    public function load(ObjectManager $manager): void
    {
        $entity = new SampleEntity('018f3f21-f0da-7084-982e-848b18bfecd1');
        $manager->persist($entity);
        $manager->flush();

        self::$ID = $entity->getId()->toRfc4122();
    }
}
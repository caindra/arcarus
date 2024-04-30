<?php

namespace App\DataFixtures;

use App\Factory\AcademicYearFactory;
use App\Factory\GroupFactory;
use App\Factory\OrganizationFactory;
use App\Factory\ProfessorFactory;
use App\Factory\StudentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        StudentFactory::createMany(200);

        ProfessorFactory::createMany(40);

        AcademicYearFactory::createMany(2);


/*
        GroupFactory::createMany(5, function (){
            return [
                'organization' => OrganizationFactory::createOne(),
                'students' => StudentFactory::randomRange(18, 20),
                'professors' => ProfessorFactory::randomRange(5, 7),
                'mentors' => ProfessorFactory::randomRange(1, 2),
                'academicYear' => AcademicYearFactory::random()
            ];
        });
*/


        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Factory\AcademicYearFactory;
use App\Factory\ClassPictureFactory;
use App\Factory\GroupFactory;
use App\Factory\OrganizationFactory;
use App\Factory\ProfessorFactory;
use App\Factory\SectionContentFactory;
use App\Factory\SectionFactory;
use App\Factory\StudentFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserPictureFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        StudentFactory::createMany(200);

        ProfessorFactory::createMany(40);

        AcademicYearFactory::createMany(2);

        TemplateFactory::createMany(5);

        SectionFactory::createMany(4);

        UserPictureFactory::createMany(200);

        GroupFactory::createMany(5, function (){
            return [
                'organization' => OrganizationFactory::createOne(),
                'students' => StudentFactory::randomRange(18, 20),
                'professors' => ProfessorFactory::randomRange(5, 7),
                'mentors' => ProfessorFactory::randomRange(1, 2),
                'academicYear' => AcademicYearFactory::random()
            ];
        });



        $manager->flush();
    }
}

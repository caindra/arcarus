<?php

namespace App\DataFixtures;

use App\Entity\ClassPicture;
use App\Entity\Professor;
use App\Entity\Student;
use App\Entity\UserPicture;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        UserPictureFactory::createMany(35);

        StudentFactory::createMany(50, [
            'password' => $this->passwordHasher->hashPassword(
                new Student(), 'prueba'
            )
        ]);

        ProfessorFactory::createMany(20, [
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'prueba'
            )
        ]);

        ProfessorFactory::createOne([
            'name' => 'Sheldon',
            'surnames' => 'Cooper Tucker',
            'email' => 'sheldon@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'bazinga'
            ),
            'userName' => 'sheldonAdmin',
            'isAdmin' => true
        ]);

        ProfessorFactory::createOne([
            'name' => 'Aelin',
            'surnames' => 'Ashryver Galathynius',
            'email' => 'aelin@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'terrasen'
            ),
            'userName' => 'flameQueen',
            'isAdmin' => false
        ]);

        ProfessorFactory::createOne([
            'name' => 'Nesta',
            'surnames' => 'Archeron Prythian',
            'email' => 'nesta@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'ataraxia'
            ),
            'userName' => 'ladyDeath',
            'isAdmin' => false
        ]);

        StudentFactory::createOne([
            'name' => 'Cassian',
            'surnames' => 'Velaris Illyrian',
            'email' => 'cassian@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Student(), 'general'
            ),
            'userName' => 'cassianIlliyrian',
            'picture' => UserPictureFactory::createOne()
        ]);

        AcademicYearFactory::createMany(2);

        TemplateFactory::createMany(5);

        OrganizationFactory::createMany(3);

        GroupFactory::createMany(5, function (){
            return [
                'organization' => OrganizationFactory::random(),
                'students' => StudentFactory::randomRange(18, 20),
                'professors' => ProfessorFactory::randomRange(5, 7),
                'mentors' => ProfessorFactory::randomRange(1, 2),
                'academicYear' => AcademicYearFactory::random()
            ];
        });



        ClassPictureFactory::createOne([
            'group' => GroupFactory::random(),
            'sectionContents' => SectionContentFactory::randomRange(1, 3),
        ]);

        SectionFactory::createMany(4, function (){
            return [
                'sectionContents' => SectionContentFactory::randomRange(1, 3),
            ];
        });



        $manager->flush();
    }
}

/**
 * TODO: implementar las siguientes clases en las fixtures:
 * - SectionContent
 * - UserSectionContent
 */

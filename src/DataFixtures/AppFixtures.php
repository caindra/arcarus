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
use App\Factory\UserSectionContentFactory;
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
        // Crear 120 imágenes de usuario para asegurar suficiente cantidad
        $userPictures = UserPictureFactory::createMany(70);

        // Crear 100 estudiantes y asignarles las primeras 100 imágenes
        $students = StudentFactory::createMany(50, [
            'password' => $this->passwordHasher->hashPassword(
                new Student(), 'prueba'
            )
        ]);

        foreach ($students as $index => $student) {
            $student->object()->setPicture($userPictures[$index]->object());
        }

        // Crear 20 profesores y asignarles las imágenes restantes
        $professors = ProfessorFactory::createMany(20, [
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'prueba'
            )
        ]);

        // Crear profesores adicionales con datos específicos
        $sheldon = ProfessorFactory::createOne([
            'name' => 'Sheldon',
            'surnames' => 'Cooper Tucker',
            'email' => 'sheldon@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'bazinga'
            ),
            'userName' => 'sheldonAdmin',
            'isAdmin' => true,
            'picture' => UserPictureFactory::createOne()->object()
        ]);

        $aelin = ProfessorFactory::createOne([
            'name' => 'Aelin',
            'surnames' => 'Ashryver Galathynius',
            'email' => 'aelin@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'terrasen'
            ),
            'userName' => 'flameQueen',
            'isAdmin' => true,
            'picture' => UserPictureFactory::createOne()->object()
        ]);

        $nesta = ProfessorFactory::createOne([
            'name' => 'Nesta',
            'surnames' => 'Archeron Prythian',
            'email' => 'nesta@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'ataraxia'
            ),
            'userName' => 'ladyDeath',
            'isAdmin' => false,
            'picture' => UserPictureFactory::createOne()->object()
        ]);

        $cassian = StudentFactory::createOne([
            'name' => 'Cassian',
            'surnames' => 'Velaris Illyrian',
            'email' => 'cassian@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Student(), 'general'
            ),
            'userName' => 'cassianIlliyrian',
            'picture' => UserPictureFactory::createOne()->object()
        ]);

        AcademicYearFactory::createMany(2);

        AcademicYearFactory::createOne([
            'description' => 'Academic Year 2023 - 2024',
            'startDate' => new \DateTime('2023-09-15'),
            'endDate' => new \DateTime('2024-06-30')
        ]);

        $template = TemplateFactory::createOne([
            'styleName' => 'classic',
            'layout' => 'classic.png'
        ]);

        $oretania = OrganizationFactory::createOne([
            'name' => 'IES Oretania'
        ]);

        OrganizationFactory::createMany(2);

        $daw = GroupFactory::createOne([
            'name' => '2nd DAW',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$aelin],
            'academicYear' => AcademicYearFactory::random()
        ]);

        GroupFactory::createOne([
            'name' => '2nd ASIR',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$sheldon, $nesta],
            'academicYear' => AcademicYearFactory::random()
        ]);

        GroupFactory::createOne([
            'name' => '2nd DAM',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$nesta],
            'academicYear' => AcademicYearFactory::random()
        ]);

        $dawClassPicture = ClassPictureFactory::createOne([
            'description' => 'Class picture of 2nd DAW',
            'group' => $daw,
            'template' => $template
        ]);

        $pictures = SectionFactory::createOne([
            'template' => $template,
            'height' => 200,
            'width' => 400,
            'maxColQuantity' => 11,
            'positionTop' => 0,
            'positionLeft' => 0,
        ]);

        $titles = SectionFactory::createOne([
            'template' => $template,
            'height' => 200,
            'width' => 400,
            'maxColQuantity' => 3,
            'positionTop' => 200,
            'positionLeft' => 0,
        ]);

        SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Name of the organization',
            'section' => $titles
        ]);

        SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Academic year, year y name of the class',
            'section' => $titles
        ]);

        $faculty = SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Faculty',
            'section' => $pictures
        ]);

        $studentBody = SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Student Body',
            'section' => $pictures
        ]);

        UserSectionContentFactory::createOne([
            'description' => 'Pictures from faculty',
            'orderNumber' => 1,
            'containedUsers' => ProfessorFactory::randomSet(7),
            'sectionContent' => $faculty
        ]);

        UserSectionContentFactory::createOne([
            'description' => 'Pictures from student body',
            'orderNumber' => 2,
            'containedUsers' => StudentFactory::randomSet(20),
            'sectionContent' => $studentBody
        ]);

        $manager->flush();
    }
}
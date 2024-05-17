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

        $sheldon = ProfessorFactory::createOne([
            'name' => 'Sheldon',
            'surnames' => 'Cooper Tucker',
            'email' => 'sheldon@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'bazinga'
            ),
            'userName' => 'sheldonAdmin',
            'isAdmin' => true
        ]);

        $aelin = ProfessorFactory::createOne([
            'name' => 'Aelin',
            'surnames' => 'Ashryver Galathynius',
            'email' => 'aelin@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'terrasen'
            ),
            'userName' => 'flameQueen',
            'isAdmin' => false
        ]);

        $nesta = ProfessorFactory::createOne([
            'name' => 'Nesta',
            'surnames' => 'Archeron Prythian',
            'email' => 'nesta@prueba.com',
            'password' => $this->passwordHasher->hashPassword(
                new Professor(), 'ataraxia'
            ),
            'userName' => 'ladyDeath',
            'isAdmin' => false
        ]);

        $cassian = StudentFactory::createOne([
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

        AcademicYearFactory::createOne([
            'description' => 'Curso 2023 - 2024',
            'startDate' => new \DateTime('2023-09-15'),
            'endDate' => new \DateTime('2024-06-30')
        ]);

        $plantilla = TemplateFactory::createOne([
            'styleName' => 'clásico',
            'layout' => 'clasico.png'
        ]);

        $oretania = OrganizationFactory::createOne([
            'name' => 'IES Oretania'
        ]);

        OrganizationFactory::createMany(2);

        $daw = GroupFactory::createOne([
            'name' => '2ª DAW',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$aelin],
            'academicYear' => AcademicYearFactory::random()
        ]);

        GroupFactory::createOne([
            'name' => '2ª ASIR',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$sheldon, $nesta],
            'academicYear' => AcademicYearFactory::random()
        ]);

        GroupFactory::createOne([
            'name' => '2ª DAM',
            'organization' => $oretania,
            'students' => StudentFactory::randomRange(18, 20),
            'professors' => ProfessorFactory::randomRange(5, 7),
            'mentors' => [$nesta],
            'academicYear' => AcademicYearFactory::random()
        ]);

        $dawClassPicture = ClassPictureFactory::createOne([
            'description' => 'Orla de 2ºDAW',
            'group' => $daw,
            'template' => $plantilla
        ]);

        $fotos = SectionFactory::createOne([
            'template' => $plantilla,
            'height' => 200,
            'width' => 400,
            'maxColQuantity' => 11,
            'positionTop' => 0,
            'positionLeft' => 0,
        ]);

        $titulos = SectionFactory::createOne([
            'template' => $plantilla,
            'height' => 200,
            'width' => 400,
            'maxColQuantity' => 3,
            'positionTop' => 200,
            'positionLeft' => 0,
        ]);

        SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Nombre del centro',
            'section' => $titulos
        ]);

        SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Curso, promoción y nombre de la clase',
            'section' => $titulos
        ]);

        $profesorado = SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Profesorado',
            'section' => $fotos
        ]);

        $alumnado = SectionContentFactory::createOne([
            'classPicture' => $dawClassPicture,
            'title' => 'Alumnado',
            'section' => $fotos
        ]);

        UserSectionContentFactory::createOne([
            'description' => 'fotos del profesorado',
            'orderNumber' => 1,
            'containedUsers' => ProfessorFactory::randomSet(7),
            'sectionContent' => $profesorado
        ]);

        UserSectionContentFactory::createOne([
            'description' => 'fotos del alumnado',
            'orderNumber' => 2,
            'containedUsers' => StudentFactory::randomSet(20),
            'sectionContent' => $alumnado
        ]);

        $manager->flush();
    }
}
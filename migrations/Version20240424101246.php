<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424101246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE academic_year (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE class_picture (id INT AUTO_INCREMENT NOT NULL, decoration VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, academic_year_id INT NOT NULL, class_picture_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_595AAE3432C8A3DE (organization_id), INDEX IDX_595AAE34C54F3401 (academic_year_id), UNIQUE INDEX UNIQ_595AAE3458484F1D (class_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_professor (grade_id INT NOT NULL, professor_id INT NOT NULL, INDEX IDX_B444EA8FE19A1A8 (grade_id), INDEX IDX_B444EA87D2D84D5 (professor_id), PRIMARY KEY(grade_id, professor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professor (id INT NOT NULL, mentored_class_id INT DEFAULT NULL, is_admin TINYINT(1) NOT NULL, INDEX IDX_790DD7E38D96E056 (mentored_class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, template_id INT NOT NULL, height INT NOT NULL, width INT NOT NULL, max_col_quantity INT NOT NULL, position_top INT NOT NULL, position_left INT NOT NULL, INDEX IDX_2D737AEF5DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_content (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, class_picture_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_2C82FDA6D823E37A (section_id), INDEX IDX_2C82FDA658484F1D (class_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, grade_id INT DEFAULT NULL, INDEX IDX_B723AF33FE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, name VARCHAR(255) NOT NULL, layout VARCHAR(255) NOT NULL, INDEX IDX_97601F8332C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, user_section_content_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, surnames VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649EE45BDBF (picture_id), INDEX IDX_8D93D649B5547FC9 (user_section_content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_picture (id INT AUTO_INCREMENT NOT NULL, binary_code VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_section_content (id INT AUTO_INCREMENT NOT NULL, section_content_id INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, order_number INT NOT NULL, INDEX IDX_3E5E912919109932 (section_content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE3432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34C54F3401 FOREIGN KEY (academic_year_id) REFERENCES academic_year (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE3458484F1D FOREIGN KEY (class_picture_id) REFERENCES class_picture (id)');
        $this->addSql('ALTER TABLE grade_professor ADD CONSTRAINT FK_B444EA8FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grade_professor ADD CONSTRAINT FK_B444EA87D2D84D5 FOREIGN KEY (professor_id) REFERENCES professor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professor ADD CONSTRAINT FK_790DD7E38D96E056 FOREIGN KEY (mentored_class_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE professor ADD CONSTRAINT FK_790DD7E3BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('ALTER TABLE section_content ADD CONSTRAINT FK_2C82FDA6D823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE section_content ADD CONSTRAINT FK_2C82FDA658484F1D FOREIGN KEY (class_picture_id) REFERENCES class_picture (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template ADD CONSTRAINT FK_97601F8332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES user_picture (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B5547FC9 FOREIGN KEY (user_section_content_id) REFERENCES user_section_content (id)');
        $this->addSql('ALTER TABLE user_section_content ADD CONSTRAINT FK_3E5E912919109932 FOREIGN KEY (section_content_id) REFERENCES section_content (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE3432C8A3DE');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE34C54F3401');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE3458484F1D');
        $this->addSql('ALTER TABLE grade_professor DROP FOREIGN KEY FK_B444EA8FE19A1A8');
        $this->addSql('ALTER TABLE grade_professor DROP FOREIGN KEY FK_B444EA87D2D84D5');
        $this->addSql('ALTER TABLE professor DROP FOREIGN KEY FK_790DD7E38D96E056');
        $this->addSql('ALTER TABLE professor DROP FOREIGN KEY FK_790DD7E3BF396750');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF5DA0FB8');
        $this->addSql('ALTER TABLE section_content DROP FOREIGN KEY FK_2C82FDA6D823E37A');
        $this->addSql('ALTER TABLE section_content DROP FOREIGN KEY FK_2C82FDA658484F1D');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33FE19A1A8');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33BF396750');
        $this->addSql('ALTER TABLE template DROP FOREIGN KEY FK_97601F8332C8A3DE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B5547FC9');
        $this->addSql('ALTER TABLE user_section_content DROP FOREIGN KEY FK_3E5E912919109932');
        $this->addSql('DROP TABLE academic_year');
        $this->addSql('DROP TABLE class_picture');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE grade_professor');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE professor');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE section_content');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_picture');
        $this->addSql('DROP TABLE user_section_content');
    }
}

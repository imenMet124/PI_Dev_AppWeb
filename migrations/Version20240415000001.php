<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240415000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert initial data for users, departments and photos';
    }

    public function up(Schema $schema): void
    {
        // Insert departments
        $this->addSql("INSERT INTO department (iyedIdDep, iyedNomDep, iyedDescriptionDep, iyedLocationDep, iyedManagerId) VALUES
            (11, 'IT', 'Information Technology Department', 'Building B', 15),
            (12, 'BOUSTA', 'MAIL', 'TUNIS', 30),
            (17, 'it works', 'please work', 'im beggiiiiiinggggg', 45),
            (18, 'i know', 'technical', 'lebron james', 30),
            (19, 'Friday', 'DAMNNN', 'Smokey', 22),
            (20, 'lklk', 'l,kl', 'k,lk', 21),
            (24, 'IT', 'Information Technology Department', 'Building B', 43),
            (25, 'Informatique', 'jnkmjnmkjn', 'jknkjnm', 30),
            (26, 'ESPRIT', 'SHFDSKFDFHKDSJ', 'ARIANA', 58)");

        // Insert users
        $this->addSql("INSERT INTO user (iyedIdUser, iyedNomUser, iyedEmailUser, iyedPhoneUser, iyedPasswordUser, iyedRoleUser, iyedPositionUser, iyedSalaireUser, iyedDateEmbaucheUser, iyedStatutUser, iyedIdDepUser) VALUES
            (15, 'NOTiyedssss', 'iyed.blick@example.co', '667788990d', '', 'EMPLOYE', 'Software Engineer chief', 9000.00, '2023-07-06', 'ON_LEAVE', 12),
            (17, 'hhh', 'hhh@hhhh.hhhh', '321', '', 'EMPLOYE', 'hhhhh', 4444.00, '2025-01-31', 'INACTIVE', 18),
            (20, 'fsdqfds', 'dsqfsdqf', '3241234', '', 'EMPLOYE', 'RSDFFG', 345132.00, '2025-01-30', 'INACTIVE', 11),
            (21, 'TOUHAMI', 'TOUHAMI@DKHILA.COMSSS', '234231', '', 'EMPLOYE', 'MAIL', 2314.00, '2025-02-15', 'ACTIVE', 19),
            (22, 'jlassii', 'jlassi@jlassi.comn', '2341132', '', 'CHEF_PROJET', 'KLDJFSLQMKDS', 423124.00, '2025-02-12', 'SUSPENDED', 11),
            (24, 'AAAAAABB', 'sfdfsdf@pls.pls', '99999', '', 'CHEF_PROJET', 'AAAAA', 22222.00, '2024-09-12', 'RESIGNED', 18),
            (30, 'bale gareth', 'djjs@plea.COM', '14321111', '', 'CHEF_PROJET', 'kkkk', 43564.00, '2026-01-15', 'TERMINATED', 12),
            (31, 'KKK', 'KKK', '212', '', 'EMPLOYE', 'JLK', 4556.00, '2025-02-08', 'CONTRACT_ENDED', 18),
            (35, 'iyed ', 'jlas@jlassi.com', '1111111111', '', 'CHEF_PROJET', 'csqc', 13213.00, '2025-02-05', 'TERMINATED', 11),
            (36, 'frank abegnale', 'frank@dreaam.com', '132321', '$2a$10$EegAPdqj6PLbWNeMNaR.nOIMTg8OSe/Eppf1cHgT.MkkQBYhtcM7e', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (37, 'danielle moore', 'danielle@gmail', '9809', '$2a$10$FtP7WnCCeLRARmZ6aWScd.EQ0kSL75jNM4HpmS5rdyd02wsaWoeJO', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (38, 'dani', 'dani@gang.gang', '132', '$2a$10$AD2t/07Evx5T7pM/8exRPuL3Uj0sXhVG37.keklun.887GehyCd5O', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (39, 'iyed', 'iyed', '66', '$2a$10$MmCdVkatfOZQAooog0dShuNhMRCl4yIGKS3jJXfzEQ7g7oArhqHsG', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (41, 'bag', 'bag', '312', '$2a$10$Jfb5zcuOLXMjDCQxHkAd9.Y2bVJGKvP3bbMCmj4ntCok74ZXRqody', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (42, 'fool', 'fool', '11432', '$2a$10$iBSroGUz4DT1NQiBBKFmCe3ZRPlaitXXlUgEjq4rNbweOS23Eavfm', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (43, 'Iyed Blick', 'iyed.blick@example.com', '667788990', '$2a$10$Dcx4HfX/7z0HRKEi.7EWvunm9cq/Wu0dL1jYDZeD52Ho7EeWy3qBa', 'EMPLOYE', 'Software Engineer', 9000.00, '2023-07-01', 'ACTIVE', 24),
            (44, 'lord', 'lord', '2', '$2a$10$MYgoA3WUqBCoXsn.d9Hn1OCeZB93uRCFS.NJ6LwcwD5YcFlMU9NIe', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', NULL),
            (45, 'finally', 'finally@gmail.com', '123', '$2a$10$ScO53dniWmWYCb/moXoY5ulVfCHCeBPdReRC9M2uwOaGZ2JUiTTXO', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'ACTIVE', 12),
            (46, 'iheb benyounes', 'i@g.com', '0788', '$2a$10$kazu.RRjApPHJmJdsYmQ8Oqn0alOZtET3lzyroTxsC0YtuaU18j3q', 'EMPLOYE', 'New Position', 3000.00, '2025-02-24', 'CONTRACT_ENDED', NULL),
            (47, 'Ahmed Mtimet', 'ahmedmtimet@gmail.com', '99887766', '$2a$10$jBY0V.5MaKOChPcq/jnz0eUg3RBqqUAr.zDgyfKHjLLNzhxL9LleO', 'EMPLOYE', 'New Position', 3000.00, '2025-02-25', 'ACTIVE', NULL),
            (48, 'Test User', 'test.user@example.com', '+21692088033', '$2a$10$D1wiY9NbwfyOlsB7TIA8puUX/OhI8qgXQz5Rm3X320eIKoA922C4K', 'EMPLOYE', 'Software Engineer', 5000.00, '2025-03-02', 'ACTIVE', NULL),
            (49, 'forsen', 'forsen@forsen.net', '1234313245', '$2a$10$sYYqDGm0/H15iozY6PpaJebMACtRgEO3PKxjce9.hlXMakJwRNe22', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (50, 'dany', 'dany@dany.com', '0000000', '$2a$10$nwXB4rT8etZNhcrykUmgMe352pC0I/VryPWUr1x83Guj811ogesM6', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (51, 'hugo', 'hugo@hugo.com', '4312413', '$2a$10$Ad2DGQ5o/2u1Okh5Ma9Kqu5Pa1I.llgNEorhqPtWSTGGv5/zFYi5K', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (52, 'Eustass Kid', 'kid@captain.sea', '4322134', '$2a$10$ae7D04fEvmGE1VPHLIAjqOg2PyEWbxYXD5CQbqLGEw3.pZycB82ta', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (53, 'Marshall D. Teach', 'marshall@captain.com', '143', '$2a$10$8hpLtUe3rVi2WBghh6v7vOAq2zFTMBTW8wHYXd096bDC/LDPwPTrm', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (54, 'Vinsmoke Sanji', 'sanji@muybien.com', '1432234', '$2a$10$usTfeMi4sYOrrTN3AutA6usLudC8DUIZvXuNLVGuv7Aj33k49upoa', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (55, 'Edward Newgate', 'whitebeard@newworld.com', '213423', '$2a$10$eepcqYUf7kKF1Sbg1OgEAehJJa82q7I/6dXnPWwjM5.0M.M0B92gS', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (56, 'kaido', 'kaido@beastpirates.com', '12342314', '$2a$10$HaILCtzHxIcLG4TuGEnZReLLJxl/kG0c.J5s5CXF/UDo7XMw47TTK', 'EMPLOYE', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', NULL),
            (57, 'dracule mihawk', 'dracule@sword.com', '23141234', '$2a$10$oMCe0nA5nNYIhCjQUuwDWuzIrv3jvE6cj0Uo6Wa6Hg5fnQhQixZdy', 'RESPONSABLE_RH', 'New Position', 3000.00, '2025-03-02', 'ACTIVE', 25),
            (58, 'Monkey D. Garp', 'garp@marines.com', '123123123', '$2a$10$jLxK.okMqmA79vGKes2YWeUU8EEvizP080cZ0VQFs4zAUD34.l2Gy', 'EMPLOYE', 'New Position', 3000.00, '2025-03-03', 'ACTIVE', NULL),
            (59, 'iyed', 'jlassiiyed01@gmail.com', '+21692088033', '$2a$10$D1wiY9NbwfyOlsB7TIA8puUX/OhI8qgXQz5Rm3X320eIKoA922C4K', 'RESPONSABLE_RH', 'LORD', 3000.00, '2025-03-18', 'ACTIVE', 17),
            (60, 'Iheb Benyounes', 'iheb.benyounes@esprit.tn', '65465456', '$2a$12$xkLNNqgj.MwNHFjmHMYccuRMo6JKcSWu71wokyqDmsh5QqxWI7uau', 'EMPLOYE', 'New Position', 3000.00, '2025-03-04', 'ACTIVE', NULL),
            (61, 'iyed', 'bread@baker.net', '12343244', '$2a$10$Iij5HlKwXlzOrMkwUt1W0.ChHFGZMtFdg1mxT5HzsOjsCZnN6bgkC', 'RESPONSABLE_RH', 'New Position', 3000.00, '2025-03-04', 'ACTIVE', NULL),
            (62, 'Imen', 'imen@gmail.com', '92088033', '$2a$10$USfygMjfOprMeg7G1PFeh.POar.g39ohOKpH1IvHc9zhncOORx2JC', 'EMPLOYE', 'New Position', 3000.00, '2025-03-04', 'INACTIVE', NULL)");

        // Insert user photos
        $this->addSql("INSERT INTO userphoto (id, user_id, photo_path) VALUES
            (1, 54, 'images/users/54.jpg'),
            (2, 55, 'src/main/resources/images/users/55.jpg'),
            (3, 56, 'src/main/resources/images/users/56.jpg'),
            (4, 57, 'src/main/resources/images/users/57.jpg'),
            (5, 58, 'src/main/resources/images/users/58.jpg'),
            (6, 60, 'src/main/resources/images/users/60.jpg'),
            (7, 61, 'src/main/resources/images/users/61.jpg'),
            (8, 62, 'src/main/resources/images/users/62.jpg')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM userphoto');
        $this->addSql('DELETE FROM user');
        $this->addSql('DELETE FROM department');
    }
} 
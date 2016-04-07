<?php

namespace LaDanseDomain\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160407194414 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Account (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, displayName VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_B28B6F3892FC23A8 (username_canonical), UNIQUE INDEX UNIQ_B28B6F38A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ActivityQueueItem (id INT AUTO_INCREMENT NOT NULL, activityType VARCHAR(255) NOT NULL, activityOn DATETIME NOT NULL, rawData LONGTEXT DEFAULT NULL, processedOn DATETIME DEFAULT NULL, activityBy INT DEFAULT NULL, INDEX IDX_8A274BCA93C757EE (activityBy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CalendarExport (id INT AUTO_INCREMENT NOT NULL, exportNew TINYINT(1) NOT NULL, exportAbsence TINYINT(1) NOT NULL, secret VARCHAR(100) NOT NULL, accountId INT NOT NULL, INDEX IDX_6E28848862DEB3E8 (accountId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE GuildCharacter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, realm VARCHAR(255) NOT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE GuildCharacterVersion (id INT AUTO_INCREMENT NOT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, level SMALLINT NOT NULL, guild VARCHAR(255) DEFAULT NULL, characterId INT NOT NULL, gameClassId INT NOT NULL, gameRaceId INT NOT NULL, INDEX IDX_A70EBD185AF690F3 (characterId), INDEX IDX_A70EBD18F3B4E37B (gameClassId), INDEX IDX_A70EBD18E036C39A (gameRaceId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CharacterClaim (id INT AUTO_INCREMENT NOT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, accountId INT NOT NULL, characterId INT NOT NULL, INDEX IDX_E115ED7862DEB3E8 (accountId), INDEX IDX_E115ED785AF690F3 (characterId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, inviteTime DATETIME NOT NULL, startTime DATETIME NOT NULL, endTime DATETIME NOT NULL, lastModifiedTime DATETIME NOT NULL, topicId LONGTEXT NOT NULL, organiserId INT NOT NULL, INDEX IDX_FA6F25A34BDD3C8 (organiserId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE FeatureToggle (id INT AUTO_INCREMENT NOT NULL, feature VARCHAR(255) NOT NULL, toggle TINYINT(1) NOT NULL, toggleFor INT NOT NULL, INDEX IDX_D25E05DD612E729E (toggleFor), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE FeatureUse (id INT AUTO_INCREMENT NOT NULL, usedOn DATETIME NOT NULL, feature VARCHAR(255) NOT NULL, rawData LONGTEXT DEFAULT NULL, usedBy INT DEFAULT NULL, INDEX IDX_E504F432FCEF271C (usedBy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Feedback (id INT AUTO_INCREMENT NOT NULL, postedOn DATETIME NOT NULL, feedback LONGTEXT NOT NULL, postedBy INT NOT NULL, INDEX IDX_2B5F260E9DD8CB47 (postedBy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ForRole (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(15) NOT NULL, signUpId INT NOT NULL, INDEX IDX_16186B55A966702F (signUpId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE GameClass (id INT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE GameRace (id INT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MailSend (id INT AUTO_INCREMENT NOT NULL, sendOn DATETIME NOT NULL, fromAddress VARCHAR(255) NOT NULL, toAddress VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE NotificationQueueItem (id INT AUTO_INCREMENT NOT NULL, activityType VARCHAR(255) NOT NULL, activityOn DATETIME NOT NULL, rawData LONGTEXT DEFAULT NULL, processedOn DATETIME DEFAULT NULL, activityBy INT DEFAULT NULL, INDEX IDX_C656D44393C757EE (activityBy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PlaysRole (id INT AUTO_INCREMENT NOT NULL, fromTime DATETIME NOT NULL, endTime DATETIME DEFAULT NULL, role VARCHAR(15) NOT NULL, claimId INT NOT NULL, INDEX IDX_7A9E9B239113A92D (claimId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(2048) NOT NULL, accountId INT NOT NULL, INDEX IDX_50C9810462DEB3E8 (accountId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SignUp (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(15) NOT NULL, eventId INT NOT NULL, accountId INT NOT NULL, INDEX IDX_DC8B3F7B2B2EBB6C (eventId), INDEX IDX_DC8B3F7B62DEB3E8 (accountId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SocialConnect (id INT AUTO_INCREMENT NOT NULL, resource VARCHAR(255) NOT NULL, resourceId VARCHAR(255) NOT NULL, accessToken VARCHAR(255) NOT NULL, refreshToken VARCHAR(255) DEFAULT NULL, connectTime DATETIME NOT NULL, lastRefreshTime DATETIME DEFAULT NULL, accountId INT NOT NULL, INDEX IDX_EF740E2962DEB3E8 (accountId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Forum (forumId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name LONGTEXT NOT NULL, description LONGTEXT NOT NULL, lastPostDate DATETIME DEFAULT NULL, lastPostTopic CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', lastPostPoster INT DEFAULT NULL, INDEX IDX_44EA91C91CA16452 (lastPostTopic), INDEX IDX_44EA91C922F0147C (lastPostPoster), PRIMARY KEY(forumId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ForumLastVisit (visitId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', lastVisitDate DATETIME NOT NULL, accountId INT NOT NULL, INDEX IDX_F17408662DEB3E8 (accountId), PRIMARY KEY(visitId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Post (postId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', postDate DATETIME NOT NULL, message LONGTEXT NOT NULL, posterId INT DEFAULT NULL, topicId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_FAB8C3B3581A197 (posterId), INDEX IDX_FAB8C3B3E2E0EAFB (topicId), PRIMARY KEY(postId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Topic (topicId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', postDate DATETIME NOT NULL, subject VARCHAR(255) NOT NULL, lastPostDate DATETIME DEFAULT NULL, posterId INT NOT NULL, forumId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', lastPostPoster INT DEFAULT NULL, INDEX IDX_5C81F11F581A197 (posterId), INDEX IDX_5C81F11F7830F151 (forumId), INDEX IDX_5C81F11F22F0147C (lastPostPoster), PRIMARY KEY(topicId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE UnreadPost (unreadId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', accountId INT NOT NULL, postId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_6B0B9B3E62DEB3E8 (accountId), INDEX IDX_6B0B9B3EE094D20D (postId), PRIMARY KEY(unreadId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Comment (commentId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', postDate DATETIME NOT NULL, message LONGTEXT NOT NULL, posterId INT DEFAULT NULL, groupId CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_5BC96BF0581A197 (posterId), INDEX IDX_5BC96BF0ED8188B0 (groupId), PRIMARY KEY(commentId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE CommentGroup (groupId CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', postDate DATETIME NOT NULL, PRIMARY KEY(groupId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ActivityQueueItem ADD CONSTRAINT FK_8A274BCA93C757EE FOREIGN KEY (activityBy) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE CalendarExport ADD CONSTRAINT FK_6E28848862DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE GuildCharacterVersion ADD CONSTRAINT FK_A70EBD185AF690F3 FOREIGN KEY (characterId) REFERENCES GuildCharacter (id)');
        $this->addSql('ALTER TABLE GuildCharacterVersion ADD CONSTRAINT FK_A70EBD18F3B4E37B FOREIGN KEY (gameClassId) REFERENCES GameClass (id)');
        $this->addSql('ALTER TABLE GuildCharacterVersion ADD CONSTRAINT FK_A70EBD18E036C39A FOREIGN KEY (gameRaceId) REFERENCES GameRace (id)');
        $this->addSql('ALTER TABLE CharacterClaim ADD CONSTRAINT FK_E115ED7862DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE CharacterClaim ADD CONSTRAINT FK_E115ED785AF690F3 FOREIGN KEY (characterId) REFERENCES GuildCharacter (id)');
        $this->addSql('ALTER TABLE Event ADD CONSTRAINT FK_FA6F25A34BDD3C8 FOREIGN KEY (organiserId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE FeatureToggle ADD CONSTRAINT FK_D25E05DD612E729E FOREIGN KEY (toggleFor) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE FeatureUse ADD CONSTRAINT FK_E504F432FCEF271C FOREIGN KEY (usedBy) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Feedback ADD CONSTRAINT FK_2B5F260E9DD8CB47 FOREIGN KEY (postedBy) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE ForRole ADD CONSTRAINT FK_16186B55A966702F FOREIGN KEY (signUpId) REFERENCES SignUp (id)');
        $this->addSql('ALTER TABLE NotificationQueueItem ADD CONSTRAINT FK_C656D44393C757EE FOREIGN KEY (activityBy) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE PlaysRole ADD CONSTRAINT FK_7A9E9B239113A92D FOREIGN KEY (claimId) REFERENCES CharacterClaim (id)');
        $this->addSql('ALTER TABLE Setting ADD CONSTRAINT FK_50C9810462DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE SignUp ADD CONSTRAINT FK_DC8B3F7B2B2EBB6C FOREIGN KEY (eventId) REFERENCES Event (id)');
        $this->addSql('ALTER TABLE SignUp ADD CONSTRAINT FK_DC8B3F7B62DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE SocialConnect ADD CONSTRAINT FK_EF740E2962DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Forum ADD CONSTRAINT FK_44EA91C91CA16452 FOREIGN KEY (lastPostTopic) REFERENCES Topic (topicId)');
        $this->addSql('ALTER TABLE Forum ADD CONSTRAINT FK_44EA91C922F0147C FOREIGN KEY (lastPostPoster) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE ForumLastVisit ADD CONSTRAINT FK_F17408662DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B3581A197 FOREIGN KEY (posterId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B3E2E0EAFB FOREIGN KEY (topicId) REFERENCES Topic (topicId)');
        $this->addSql('ALTER TABLE Topic ADD CONSTRAINT FK_5C81F11F581A197 FOREIGN KEY (posterId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Topic ADD CONSTRAINT FK_5C81F11F7830F151 FOREIGN KEY (forumId) REFERENCES Forum (forumId)');
        $this->addSql('ALTER TABLE Topic ADD CONSTRAINT FK_5C81F11F22F0147C FOREIGN KEY (lastPostPoster) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE UnreadPost ADD CONSTRAINT FK_6B0B9B3E62DEB3E8 FOREIGN KEY (accountId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE UnreadPost ADD CONSTRAINT FK_6B0B9B3EE094D20D FOREIGN KEY (postId) REFERENCES Post (postId)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0581A197 FOREIGN KEY (posterId) REFERENCES Account (id)');
        $this->addSql('ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0ED8188B0 FOREIGN KEY (groupId) REFERENCES CommentGroup (groupId)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ActivityQueueItem DROP FOREIGN KEY FK_8A274BCA93C757EE');
        $this->addSql('ALTER TABLE CalendarExport DROP FOREIGN KEY FK_6E28848862DEB3E8');
        $this->addSql('ALTER TABLE CharacterClaim DROP FOREIGN KEY FK_E115ED7862DEB3E8');
        $this->addSql('ALTER TABLE Event DROP FOREIGN KEY FK_FA6F25A34BDD3C8');
        $this->addSql('ALTER TABLE FeatureToggle DROP FOREIGN KEY FK_D25E05DD612E729E');
        $this->addSql('ALTER TABLE FeatureUse DROP FOREIGN KEY FK_E504F432FCEF271C');
        $this->addSql('ALTER TABLE Feedback DROP FOREIGN KEY FK_2B5F260E9DD8CB47');
        $this->addSql('ALTER TABLE NotificationQueueItem DROP FOREIGN KEY FK_C656D44393C757EE');
        $this->addSql('ALTER TABLE Setting DROP FOREIGN KEY FK_50C9810462DEB3E8');
        $this->addSql('ALTER TABLE SignUp DROP FOREIGN KEY FK_DC8B3F7B62DEB3E8');
        $this->addSql('ALTER TABLE SocialConnect DROP FOREIGN KEY FK_EF740E2962DEB3E8');
        $this->addSql('ALTER TABLE Forum DROP FOREIGN KEY FK_44EA91C922F0147C');
        $this->addSql('ALTER TABLE ForumLastVisit DROP FOREIGN KEY FK_F17408662DEB3E8');
        $this->addSql('ALTER TABLE Post DROP FOREIGN KEY FK_FAB8C3B3581A197');
        $this->addSql('ALTER TABLE Topic DROP FOREIGN KEY FK_5C81F11F581A197');
        $this->addSql('ALTER TABLE Topic DROP FOREIGN KEY FK_5C81F11F22F0147C');
        $this->addSql('ALTER TABLE UnreadPost DROP FOREIGN KEY FK_6B0B9B3E62DEB3E8');
        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0581A197');
        $this->addSql('ALTER TABLE GuildCharacterVersion DROP FOREIGN KEY FK_A70EBD185AF690F3');
        $this->addSql('ALTER TABLE CharacterClaim DROP FOREIGN KEY FK_E115ED785AF690F3');
        $this->addSql('ALTER TABLE PlaysRole DROP FOREIGN KEY FK_7A9E9B239113A92D');
        $this->addSql('ALTER TABLE SignUp DROP FOREIGN KEY FK_DC8B3F7B2B2EBB6C');
        $this->addSql('ALTER TABLE GuildCharacterVersion DROP FOREIGN KEY FK_A70EBD18F3B4E37B');
        $this->addSql('ALTER TABLE GuildCharacterVersion DROP FOREIGN KEY FK_A70EBD18E036C39A');
        $this->addSql('ALTER TABLE ForRole DROP FOREIGN KEY FK_16186B55A966702F');
        $this->addSql('ALTER TABLE Topic DROP FOREIGN KEY FK_5C81F11F7830F151');
        $this->addSql('ALTER TABLE UnreadPost DROP FOREIGN KEY FK_6B0B9B3EE094D20D');
        $this->addSql('ALTER TABLE Forum DROP FOREIGN KEY FK_44EA91C91CA16452');
        $this->addSql('ALTER TABLE Post DROP FOREIGN KEY FK_FAB8C3B3E2E0EAFB');
        $this->addSql('ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0ED8188B0');
        $this->addSql('DROP TABLE Account');
        $this->addSql('DROP TABLE ActivityQueueItem');
        $this->addSql('DROP TABLE CalendarExport');
        $this->addSql('DROP TABLE GuildCharacter');
        $this->addSql('DROP TABLE GuildCharacterVersion');
        $this->addSql('DROP TABLE CharacterClaim');
        $this->addSql('DROP TABLE Event');
        $this->addSql('DROP TABLE FeatureToggle');
        $this->addSql('DROP TABLE FeatureUse');
        $this->addSql('DROP TABLE Feedback');
        $this->addSql('DROP TABLE ForRole');
        $this->addSql('DROP TABLE GameClass');
        $this->addSql('DROP TABLE GameRace');
        $this->addSql('DROP TABLE MailSend');
        $this->addSql('DROP TABLE NotificationQueueItem');
        $this->addSql('DROP TABLE PlaysRole');
        $this->addSql('DROP TABLE Setting');
        $this->addSql('DROP TABLE SignUp');
        $this->addSql('DROP TABLE SocialConnect');
        $this->addSql('DROP TABLE Forum');
        $this->addSql('DROP TABLE ForumLastVisit');
        $this->addSql('DROP TABLE Post');
        $this->addSql('DROP TABLE Topic');
        $this->addSql('DROP TABLE UnreadPost');
        $this->addSql('DROP TABLE Comment');
        $this->addSql('DROP TABLE CommentGroup');
    }
}

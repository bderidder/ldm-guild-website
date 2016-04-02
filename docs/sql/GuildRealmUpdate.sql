ALTER TABLE GuildCharacter ADD realm VARCHAR(255) DEFAULT NULL;
UPDATE GuildCharacter SET realm='Defias Brotherhood' WHERE realm IS NULL;
ALTER TABLE GuildCharacter CHANGE COLUMN `realm` `realm` VARCHAR(255) NOT NULL;

ALTER TABLE GuildCharacterVersion ADD guild VARCHAR(255) DEFAULT NULL;
UPDATE GuildCharacterVersion SET guild='La Danse Macabre' WHERE guild IS NULL;
ALTER TABLE GuildCharacterVersion CHANGE COLUMN `guild` `guild` VARCHAR(255) NOT NULL;


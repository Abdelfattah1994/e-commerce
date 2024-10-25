<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241025091816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('CREATE TABLE cart_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cart_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F0FE25271AD5CDBF ON cart_item (cart_id)');
        $this->addSql('CREATE INDEX IDX_F0FE25274584665A ON cart_item (product_id)');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, active BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, status VARCHAR(20) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , transaction_id VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
        $this->addSql('CREATE TABLE order_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER NOT NULL, product_id INTEGER NOT NULL, quantity INTEGER NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F094584665A ON order_item (product_id)');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, price DOUBLE PRECISION NOT NULL, stock INTEGER NOT NULL, featured BOOLEAN NOT NULL, image VARCHAR(255) DEFAULT NULL, active BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
        $this->addSql('CREATE TABLE site_settings (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, site_name VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, favicon VARCHAR(255) DEFAULT NULL, hero_title VARCHAR(255) DEFAULT NULL, hero_subtitle VARCHAR(255) DEFAULT NULL, hero_description VARCHAR(255) DEFAULT NULL, hero_image VARCHAR(255) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, contact_phone VARCHAR(255) DEFAULT NULL, contact_address VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE support_ticket (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, subject VARCHAR(255) NOT NULL, message CLOB NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_1F5A4D53A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1F5A4D53A76ED395 ON support_ticket (user_id)');
        $this->addSql('CREATE TABLE "transaction" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, virtual_card_id INTEGER NOT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_723705D17CBB49EF FOREIGN KEY (virtual_card_id) REFERENCES virtual_card (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_723705D17CBB49EF ON "transaction" (virtual_card_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE virtual_card (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, card_number VARCHAR(16) NOT NULL, expiration_date VARCHAR(5) NOT NULL, cvv VARCHAR(3) NOT NULL, balance DOUBLE PRECISION NOT NULL, active BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_FF9C85EBA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FF9C85EBA76ED395 ON virtual_card (user_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE site_settings');
        $this->addSql('DROP TABLE support_ticket');
        $this->addSql('DROP TABLE "transaction"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE virtual_card');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

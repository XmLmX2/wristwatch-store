<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122001446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, payment_type_id INT NOT NULL, currency_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, created_at DATETIME NOT NULL, payment_status INT NOT NULL, total DOUBLE PRECISION NOT NULL, token VARCHAR(32) NOT NULL, INDEX IDX_F5299398DC058279 (payment_type_id), INDEX IDX_F529939838248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_payment_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, store_order_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, INDEX IDX_2530ADE64584665A (product_id), INDEX IDX_2530ADE6B3E812C2 (store_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_validation_request (id INT AUTO_INCREMENT NOT NULL, merchant VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(10) NOT NULL, credit_card_name VARCHAR(100) NOT NULL, credit_card_number VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, back_to LONGTEXT NOT NULL, token VARCHAR(32) NOT NULL, order_id INT NOT NULL, feedback_url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, currency_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD38248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_image (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64617F034584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398DC058279 FOREIGN KEY (payment_type_id) REFERENCES order_payment_type (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939838248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6B3E812C2 FOREIGN KEY (store_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id)');

        // Dummy data
        $this->addSql("INSERT INTO `brand` VALUES (1,'Fossil'),(2,'SKMEI'),(3,'SMAEL'),(4,'T-WINNER'),(5,'Naviforce'),(6,'Tommy Hilfiger'),(7,'Huawei');");
        $this->addSql("INSERT INTO `currency` VALUES (1,'RON');");
        $this->addSql("INSERT INTO `order_payment_type` VALUES (1,'Credit card');");
        $this->addSql("INSERT INTO `product` VALUES (1,2,1,'Ceas barbatesc Smael, Shock Resistant, Militar, Sport, Digital, Army, Dual time, Cronograf',80,'Acest ceas barbatesc este unul cu un design sport, militar, este rezistent la socuri, rezistent la apa ( 5 atm ), se poate citi ora chiar si pe intuneric, este electronic, iar celelalte functii pe care le-am omis aici, le gasiti mai jos.'),(2,2,1,'Ceas barbatesc, Skmei, Cronograf, Army, Militar, Sport, Digital, Rezistent la apa si socuri, culoare Albastru',90,'Produsul se livreaza in cutie personalizata, impreuna cu un manual de utilizare si un certificat de garantie.'),(3,1,1,'Ceas pentru Barbati Fossil Coachman CH2564',489.99,'Produsul se livreaza in cutie personalizata, impreuna cu un manual de utilizare si un certificat de garantie.'),(4,1,1,'Fossil, Ceas automatic cu o curea de piele, Maro inchis/Negru',829.99,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.'),(5,3,1,'Ceas Barbatesc Smael, Sport, Shock Resistant, Cronograf, Militar, Army, Dual time, Cronometru, Alarma, Rezistent la apa',60,'Produsul se livreaza in cutie personalizata, impreuna cu un manual de utilizare si un certificat de garantie.'),(6,1,1,'Ceas pentru barbati Fossil Grant FS4735',634,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.'),(7,5,1,'Ceas de mana barbatesc, NaviForce, Digital/Analog, Mecanism Quartz, Cronometru, Calendar, Elegant, Business, Maro-Coffe',130,'A se evita socurile mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive. si scufundarea sa in apa'),(8,6,1,'Tommy Hilfiger, Ceas cu functii multiple si curea de piele, Maro/Argintiu',399.99,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.'),(9,4,1,'Ceas brabatesc Winner, bratara din otel inoxidabil neagra, rezistent la zgarieturi, automatic',140,'Produsul este livrat in ambalaj personalizat.'),(10,7,1,'Ceas Smartwatch Huawei Watch GT 2, 42mm, Refined Gold',649.99,'Watch GT 2  este un ceas sport de 42mm, rezistent la apa pana la 50 metri, cu functii integrate precum redarea muzicii (utilizand casti), monitorizarea calitatii somnului si a activitatii fizice, monitorizarea ritmului cardiac, GPS si multe altele. Designul sau usor si la moda, rezistenta la zgarieturi, ghidarea fitness si monitorizarea corecta a sanatatii il fac sa fie ceasul ideal pentru uzura zilnica si profesionala a exercitiilor profesionale pentru consumatorii pasionati de tehnologie.'),(11,1,1,'Fossil, Ceas cu bratara metalica Jacqueline, Auriu rose',916,'Produsul este livrat in ambalaj personalizat.'),(12,1,1,'Fossil, Ceas quartz cu cadran cu model floral',419.99,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.\r\nDecorat cu cristal zirconia\r\nPlacaj IP'),(13,1,1,'Fossil, Ceas analog cu carcasa rotunda Virginia, Auriu rose',399.99,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.'),(14,1,1,'Ceas pentru Barbati Fossil Coachman CH2891',499.99,'A se evita socurile termice si mecanice, zgarierea, contactul cu alcool, parfum, acetona, detergent, suprafete abrazive.'),(15,7,1,'Ceas smartwatch Huawei Watch 3 Pro, 48mm, Classic, Brown Leather',1949.99,'Lasa ceea ce porti la incheietura sa se integreze perfect in viata ta. Finisajul rotund si ecranul curbat al HUAWEI WATCH 3 Pro ii ofera un design clasic si fluid. Cadranul din titan si sticla din safir ale smartwatch-ului HUAWEI WATCH 3 Pro iti aduc acel look profesionist in fiecare secunda a zilei. Si datorita afisajului AMOLED mare, de 1.43\", in culori ultra vii, poti sa te bucuri mai intens de orice moment care ti se dezvaluie.');");
        $this->addSql("INSERT INTO `product_image` VALUES (1,2,'res-761e2c19b3c0210f9049b83d662c66e8-61993392753b0.jpg'),(2,2,'res-07305925835db446ed5b1e7a1c0b7721-6199339275b4c.jpg'),(3,2,'res-e0ea537456114f59643652c5664b6751-6199339275cfe.jpg'),(4,1,'res_33ad28c5a67176194333220c848d5e6a-6199339275cfe.jpg'),(5,3,'res-ade831c77a34f2f1069e30ce234232db-619947b55dc29.webp'),(6,3,'res-f000c823e39158447bd572ad15fef876-619947b55e0f3.webp'),(7,3,'res-44ec7266631080568fe1878ef042684e-619947b4e75c8.webp'),(8,3,'res-52cc305e4a4a5a23a71ab835cd5fbe7f-619947b55d8a1.webp'),(10,4,'res-41f5ba2fcc32ee53e9ed9c85b907da25-6199f47098102.jpg'),(11,4,'res-a0b224196218c1b774aeeba6b065adc1-6199f47098c39.webp'),(12,4,'res-ade831c77a34f2f1069e30ce234232db-6199f47098efe.webp'),(13,4,'res-dc6afc6ff62f86d01b593c64d0912369-6199f47099364.webp'),(14,5,'res-0244d77ee3c02380833a60316378264b-6199f54c3c882.webp'),(15,5,'res-bb1da048a423e7294f51ca42e5cfdce3-6199f54c3ce0c.jpg'),(16,5,'res-c776eb55ebda3978933ff5c9734757ca-6199f54c3cf95.webp'),(17,6,'1-6199f713579a6.webp'),(18,6,'res-440e99332e1b6e3bd74c6dce1da560b9-6199f71358027.webp'),(19,6,'res-44438cb55ce463bd2e51c8ea326dd128-6199f71358620.webp'),(20,7,'1-6199f7bd38f03.jpg'),(21,7,'res-02c600491c89bcf0215ba150973846d4-6199f7bd39614.webp'),(22,7,'res-40c7c714e73ba765d34ef02baa10f709-6199f7bd3999b.jpg'),(23,8,'res-d0057eca6952f7d91cd08ac0be71dd32-6199f895a0836.webp'),(24,8,'res-ef5b83b5fdc32dd8ea6194682caf99da-6199f895a10f1.webp'),(25,9,'1-6199fa4e05fbe.webp'),(26,9,'res-2ead91f46533fd8d42b43a6fbb421fb0-6199fa4e064f0.webp'),(27,9,'res-d2e060758f89994dbdb8e3110a0fcfb0-6199fa4e06962.webp'),(28,9,'res-e3636e099fe8e01f9d507dc968f363f9-6199fa4e06bb2.webp'),(29,14,'1-6199fb51ca87c.webp'),(30,14,'res-9a2abac724aa1902fba0c1acb70f32b9-6199fb51caddf.webp'),(31,14,'res-261701974fb1ab5bf93382088ee86983-6199fb51cb2f5.webp'),(32,15,'1-6199fbd7522c6.webp'),(33,15,'res-4d42e95cbf94bb7a5e08477a021399c3-6199fbd752bec.webp'),(34,15,'res-36c544c2a91581a32fd59375f165646a-6199fbd75306f.webp'),(35,15,'res-083a5c6d43a5fd21b2fce9be883a3556-6199fbd7533b3.webp'),(36,15,'res-d2827aa84d5ea4d7557aab54499c9730-6199fbd753742.webp'),(37,10,'1-619a0abf2aee7.webp'),(38,10,'res-1d67b9555a441d26a2795c0f5aec35dc-619a0abf2b9c0.webp'),(39,10,'res-7f15aced070f9813778a49b147abec8b-619a0abf2bc18.webp'),(40,10,'res-3175854e2198262b0dc0e885edf676a5-619a0abf2bf69.webp'),(41,11,'res-43bcdec5590a15748d97d5b3f3117de4-619a0b70a0016.webp'),(42,11,'res-757e86b58af3bd792fefe4755e1d3db5-619a0b70a0639.webp'),(43,12,'1-619a0bcccc999.webp'),(44,12,'res-09cec10d872ffca2911848f9fdbb06f9-619a0bccccee1.webp'),(45,12,'res-835a71457d936e2762e0c4a3daec70aa-619a0bcccd0f2.webp'),(46,13,'res-0b9fa988a349727e60be163a5b7c28a5-619a0c062272a.webp'),(47,13,'res-c40eec4b58a7af33ac3c676f0dbe69ac-619a0c0622e37.webp');");
        $this->addSql("INSERT INTO `user` VALUES (1,'marius','[\"ROLE_USER\", \"ROLE_ADMIN\"]','$2y$13\$qTzlR9mhDeQTWKZMFv90MeZnt0jMOSilMTaWHnlAxErLP8IN46XOi');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939838248176');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD38248176');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6B3E812C2');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398DC058279');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_payment_type');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE payment_validation_request');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE user');
    }
}

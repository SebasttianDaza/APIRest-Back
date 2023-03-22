/*Data Base APIRest*/

/*DROP DATABASE IF EXISTS APIRest;*/

/*CREATE DATABASE IF NOT EXISTS `APIRest`;*/

USE `ships-rest`; 

/* Table Principal */

CREATE TABLE IF NOT EXISTS `Embarcaciones` (
    `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    `country` VARCHAR(45) NOT NULL,
    `continent` VARCHAR(45) NOT NULL,
    `coordinates` VARCHAR(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `Embarcaciones` (
    `id`,
    `name`,
    `country`,
    `continent`,
    `coordinates`
) VALUES ( NULL, 'Colombia SAS', 'Colombia', 'South America','1,1'),
( NULL, 'Panama PSE', 'Panama', 'South America','2,2'),
( NULL, 'Argentina Chocolate', 'Argentina', 'South America','3,3'),
( NULL, 'England Exports', 'England', 'Europe','4,4'),
( NULL, 'PSG', 'France', 'Europe','5,5'),
( NULL, 'Bavaria', 'Germany', 'Europe','6,6'),
( NULL, 'Pique de Os', 'Portugal', 'Europe','7,7'),
( NULL, 'Barcelona Technologies', 'Spain', 'Europe','8,8'),
( NULL, 'Real Madrid', 'Spain', 'Europe','9,9'),
( NULL, 'Hasbro', 'USA', 'North America','10,10'),
( NULL, 'Disney', 'USA', 'North America','11,11'),
( NULL, 'Nike', 'USA', 'North America','12,12'),
( NULL, 'Adidas', 'USA', 'North America','13,13'),
( NULL, 'Pepsi', 'USA', 'North America','14,14'),
( NULL, 'Coca-Cola', 'USA', 'North America','15,15'),
( NULL, 'McDonalds', 'USA', 'North America','16,16'),
( NULL, 'KFC', 'USA', 'North America','17,17'),
( NULL, 'Samsung', 'USA', 'North America','18,18'),
( NULL, 'Apple', 'USA', 'North America','19,19'),
( NULL, 'Microsoft', 'USA', 'North America','20,20'),
( NULL, 'Google', 'USA', 'North America','21,21'),
( NULL, 'El tiempo', 'Colombia', 'South America','22,22'),
( NULL, 'El Nuevo Día', 'Colombia', 'South America','23,23'),
( NULL, 'Globant', 'Colombia', 'South America','24,24');

/* ID Embarcaciones 1-24 

-- Archicteture Table `usuarios` -- Duenos de las Embarcaciones */

CREATE TABLE IF NOT EXISTS `usuarios` (
    `userID` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(45) NOT NULL,
    `lastname` VARCHAR(45) NOT NULL,
    `email` VARCHAR(45) NOT NULL,
    `embarcacionesID` INT(11) NOT NULL,
    CONSTRAINT UNIQUE (email)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `usuarios` (
    `userID`,
    `username`,
    `lastname`,
    `email`,
    `embarcacionesID`
) VALUES ( NULL, 'Juan', 'Daza', 'jsebastian@gmail.com', 1),
( NULL, 'Pedro', 'Rodriguez', 'pedro@hotmail.com', 2),
( NULL, 'Maria', 'Perez',  'mariaagilar12@hotmail.com', 3),
( NULL, 'Daniel', 'Gonzalez', 'danielgonzales@gmail.com', 4),
( NULL, 'Esteban', 'Castillo', 'estcas@email.com', 17),
( NULL, 'Cristian', 'Parra', 'cristianpara@hotmail.com', 18),
( NULL, 'Daniela', 'Ruiz', 'danielaruiz456@asyrys.com', 17),
( NULL, 'Daniela', 'Rodriguez', 'danirodriQAdww@hotmail.com', 9 ),
( NULL, 'Jully', 'Falla', 'jullyfallita@google.com.co', 10),
( NULL, 'Jessica', 'Marina', 'marinajessica@cargonfive.com', 14),
( NULL, 'Esperanza', 'Gomez', 'esperanza@cargonfive.com', 13),
( NULL, 'Geraldine', 'Gomez', 'geraldinelove@gmail.com', 12),
( NULL, 'Estefany', 'Rhoades', 'rhoadeseste@emprendeyourlifestyle.com', 11),
( NULL, 'Julian', 'Villalba', 'julianvillalba@cargonfive.com', 15),
( NULL, 'Harold', 'Arias', 'haroldelsena@sena.com', 16),
( NULL, 'Martin', 'Velandia', 'martinfhjdsjf12@space.com', 21),
( NULL, 'Sonia', 'Marsella', 'soniamarsella@tesla.com', 20),
( NULL, 'Sandra', 'Gonzalez', 'gonzas@gmail.com', 23);

/* ID Embarcaciones 1-18

-- Archicteture Table `sale` -- Ventas de las embarcaciones */

CREATE TABLE IF NOT EXISTS `sale` (
    `saleID` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `embarcacionesID` INT(11) NOT NULL,
    `quantity` INT(25) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `sale` (
    `saleID`,
    `embarcacionesID`,
    `quantity`
) VALUES ( NULL, 1, 5),
( NULL, 2, 10),
( NULL, 4, 12),
( NULL, 3, 3),
( NULL, 5, 12),
( NULL, 7, 16),
( NULL, 22, 13),
( NULL, 19, 2),
( NULL, 20, 30),
( NULL, 15, 300),
( NULL, 17, 1231),
( NULL, 10, 2303),
( NULL, 11, 123),
( NULL, 12, 400),
( NULL, 18, 567),
( NULL, 24, 120),
( NULL, 23, 678),
( NULL, 8, 1364),
( NULL, 9, 123),
( NULL, 6, 123),
( NULL, 14, 345);


-- Archicteture Users Table `users` -- Usuarios de la plataforma --

CREATE TABLE IF NOT EXISTS `users` (
    `userID` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(45) NOT NULL,
    `password` VARCHAR(45) NOT NULL,
    `email` VARCHAR(45) NOT NULL,
    `status` INT(11) NOT NULL DEFAULT 1, -- 1 = Active, 0 = Inactive
    CONSTRAINT UNIQUE (email)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `users` (
    `userID`,
    `username`,
    `password`,
    `email`,
    `status`
) VALUES ( NULL, 'admin', 'admin', 'admin2002@gmail.com', status);

-- Archicteture Table `users_tokens` -- Tokens de los usuarios --
CREATE TABLE IF NOT EXISTS `users_tokens` (
    `userID` INT(11) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `status` INT(11) NOT NULL DEFAULT 1, -- 1 = Active, 0 = Inactive
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`userID`, `token`),
    FOREIGN KEY (`userID`) REFERENCES `users`(`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


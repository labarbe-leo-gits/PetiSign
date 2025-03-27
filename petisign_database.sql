CREATE TABLE CAPTCHA(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    question TEXT,
    answer VARCHAR(30)
);

CREATE TABLE USER (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(30) UNIQUE,
    password TEXT NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    gender ENUM('Homme', 'Femme', 'Autre', 'Non Renseigné') DEFAULT 'Non Renseigné',
    birthdate DATE DEFAULT '2000-01-01',
    description TEXT DEFAULT 'Aucune description disponible',
    is_admin BOOLEAN DEFAULT 0,
    is_benevole BOOLEAN DEFAULT 0
    avatar_hat INT DEFAULT 1, -- Attribute for avatar hat
    avatar_mouth INT DEFAULT 1, -- Attribute for avatar mouth
    avatar_eyes INT DEFAULT 1, -- Attribute for avatar eyes
    signature INT REFERENCES SIGNATURE(id_petition) --A REVOIR--
);


CREATE TABLE DONATION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    amount INT,--SOMME A REVOIR--
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    id INT REFERENCES USER(id)
);

CREATE TABLE MESSAGE (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    user INTEGER REFERENCES USER(id)
);

CREATE TABLE COMMENT (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    user INTEGER REFERENCES USER(id),
    id_petition INT REFERENCES PETITION(id),
    id_user INT REFERENCES USER(id)
);

CREATE TABLE CATEGORY(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(30)
)

CREATE TABLE PETITION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title VARCHAR(60),
    description TEXT,
    category INT REFERENCES CATEGORY(id),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    signature_goal INT,
    image_id INT,
    user INTEGER REFERENCES USER(id)
);

CREATE TABLE SIGNATURE (
    id_user INT,
    PRIMARY KEY (id_user, id_petition),
    id_petition INT REFERENCES PETITION(id)
);

CREATE TABLE NEWSLETTER (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title VARCHAR(60),
    content TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

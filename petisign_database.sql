CREATE DATABASE IF NOT EXISTS petisign;
USE petisign;

CREATE TABLE CAPTCHA(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    question TEXT,
    answer VARCHAR(30),
    state BOOLEAN DEFAULT 1
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
    is_benevole BOOLEAN DEFAULT 0,,
    avatar_hat INT DEFAULT 1,
    avatar_mouth INT DEFAULT 1,
    avatar_eyes INT DEFAULT 1,
    avatar_skin INT DEFAULT 1,
    avatar_hat_color INT DEFAULT 1,
    avatar_mouth_color INT DEFAULT 1,
    avatar_eyes_color INT DEFAULT 1,
    avatar_skin_color INT DEFAULT 6,
    signature INT REFERENCES SIGNATURE(id_petition),
    newsletter INT DEFAULT 1,
    mail_notification INT DEFAULT 1,
    last_activity DATETIME,
    last_login DATE DEFAULT '2025-04-01'
);

CREATE TABLE TEAM(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(60),
    sector TEXT DEFAULT 'Aucun secteur renseigné',
    description TEXT DEFAULT 'Aucune description disponible',
    leader INT REFERENCES USER(id)
);

CREATE TABLE TEAM_MEMBER(
    id_user INT REFERENCES USER(id),
    id_team INT REFERENCES TEAM(id),
    PRIMARY KEY (id_user, id_team)
);

CREATE TABLE TEAM_ACTIVITY(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(60),
    description TEXT,
    event_date DATE  DEFAULT '2000-01-01',
    creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT REFERENCES USER(id),
    id_team INT REFERENCES TEAM(id),
    city VARCHAR(30),
    postal_code VARCHAR(5),
    rue VARCHAR(30),
    num INT,
    max_participants INT
);

CREATE TABLE DONATION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    amount INT,--SOMME A REVOIR--
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT,
    id INT REFERENCES USER(id)
);

CREATE TABLE DISCUSSION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_user INT REFERENCES USER(id),
    id_second_user INT REFERENCES USER(id)
);

CREATE TABLE MESSAGE (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content TEXT,
    sender INT REFERENCES USER(id),
    id_discussion INT REFERENCES DISCUSSION(id),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE COMMENT(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    content TEXT,
    target_type INTEGER,
    id_user INT REFERENCES USER(id),
    id_target INT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE CATEGORY(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(30)
);

CREATE TABLE PETITION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title VARCHAR(60),
    description TEXT,
    category INT REFERENCES CATEGORY(id),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    signature_goal INT,
    signature_count INT DEFAULT 0,
    signature_stage_one INT DEFAULT 0,
    signature_stage_two INT DEFAULT 0,
    signature_stage_three INT DEFAULT 0,
    signature_stage_four INT DEFAULT 0,
    image_id INT,
    user INTEGER REFERENCES USER(id),
    statut VARCHAR(6) DEFAULT 'OPEN'
);

CREATE TABLE SIGNATURE (
    id_user INT REFERENCES USER(id),
    id_petition INT REFERENCES PETITION(id),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user, id_petition)
);

CREATE TABLE NEWSLETTER (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    title VARCHAR(60) UNIQUE,
    content TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status BOOLEAN DEFAULT 0
);

ALTER TABLE NEWSLETTER CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE PETITION CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE ABONNEMENT(
    id_user INT REFERENCES USER(id),
    id_newsletter INT REFERENCES NEWSLETTER(id),
    PRIMARY KEY (id_user, id_newsletter)
);

CREATE TABLE BAN(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_user INT REFERENCES USER(id),
    id_admin INT REFERENCES USER(id),
    reason TEXT,
    expiration DATE,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE REPORT(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    report_type INT,
    id_user INT REFERENCES USER(id),
    id_target INT,
    reason TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE DON(
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_user INT REFERENCES USER(id),
    amount INT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ACTIVITY_INSCRIPTION(
    id_user INT REFERENCES USER(id),
    id_activity INT REFERENCES TEAM_ACTIVITY(id),
    PRIMARY KEY (id_user, id_activity)
);
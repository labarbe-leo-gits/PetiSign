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
);

CREATE TABLE SESSION (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT,
    token VARCHAR(255) UNIQUE NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expiration TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USER(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_token (token),
    INDEX idx_expiration (expiration)
);

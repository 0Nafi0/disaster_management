CREATE TABLE Disaster (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255)
);

CREATE TABLE Volunteer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    skill VARCHAR(255)
);

CREATE TABLE Resource (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(50) 
);


CREATE TABLE Relief_Camp (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    capacity INT,
    disaster_id INT,
    FOREIGN KEY (disaster_id) REFERENCES Disaster(id)
);
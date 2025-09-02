CREATE TABLE Disaster (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255)
);

CREATE TABLE Victim (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    disaster_id INT,
    camp_id INT,
    FOREIGN KEY (disaster_id) REFERENCES Disaster(id),
    FOREIGN KEY (camp_id) REFERENCES Relief_Camp(id)
);

CREATE TABLE Volunteer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    skill VARCHAR(100),
    camp_id INT,
    FOREIGN KEY (camp_id) REFERENCES Relief_Camp(id)
);

CREATE TABLE Resource (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    quantity INT,
    unit VARCHAR(20) 
);


CREATE TABLE Relief_Camp (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    capacity INT,
    disaster_id INT,
    FOREIGN KEY (disaster_id) REFERENCES Disaster(id)
);
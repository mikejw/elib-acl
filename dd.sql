


CREATE TABLE role(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
name                    VARCHAR(32)             NOT NULL) ENGINE=InnoDB;


create TABLE role_user(
id                      INT(11)                 AUTO_INCREMENT PRIMARY KEY,
role_id                 INT(11)                 NOT NULL,
user_id                 INT(11)                 NOT NULL,
FOREIGN KEY (role_id) REFERENCES role(id),
FOREIGN KEY (user_id) REFERENCES user(id)) ENGINE=InnoDB;

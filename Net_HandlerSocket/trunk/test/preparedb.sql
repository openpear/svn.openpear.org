drop table if exists table1;
create table table1 (id int NOT NULL AUTO_INCREMENT,
       enable tinyint(4) NOT NULL,
       body varchar(255) NOT NULL,
       PRIMARY KEY(`id`),
       KEY `idx_enable` (`enable`)
) ENGINE=InnoDB;
INSERT INTO table1 VALUES(null, 1, 'hoge');
INSERT INTO table1 VALUES(null, 0, 'fuga');
INSERT INTO table1 VALUES(null, 1, 'piyo');
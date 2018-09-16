CREATE TABLE category
(
  id    INT AUTO_INCREMENT
    PRIMARY KEY,
  title VARCHAR(50) NOT NULL
);

CREATE INDEX title
  ON category (title);


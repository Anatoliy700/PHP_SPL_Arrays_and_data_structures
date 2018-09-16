CREATE TABLE category_links
(
  parent_id INT                     NOT NULL,
  child_id  INT                     NOT NULL,
  level     SMALLINT(6) DEFAULT '0' NOT NULL,
  PRIMARY KEY (parent_id, child_id),
  CONSTRAINT categoryLinks._ibfk_1
  FOREIGN KEY (parent_id) REFERENCES category (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT categoryLinks._ibfk_2
  FOREIGN KEY (child_id) REFERENCES category (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE INDEX `categoryLinks._ibfk_2`
  ON category_links (child_id);

CREATE INDEX levelCategoryLinks
  ON category_links (level);


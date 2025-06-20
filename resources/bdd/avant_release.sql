-- SCRIPT SQL;

alter table visiteur add column nom_role varchar(255) default 'Visiteur';

-- Creation table role.
create table role(id int primary key, libelle varchar(50));
Insert into role(id, libelle) VALUES(1,'Visiteur');
Insert into role(id, libelle) VALUES(2,'Comptable');
ALTER TABLE role ADD INDEX(libelle);
-- alter table visiteur add FOREIGN KEY (nom_role) REFERENCES role(libelle);

alter table visiteur modify column mdp varchar(255);

CREATE TABLE vehicule(id int primary key, libelle varchar(75), prix double);
alter table visiteur add column id_vehicule int ;
INSERT into vehicule values(1,'Vehicule 4CV Diesel', 0.52);
INSERT into vehicule values(2,'Vehicule 5/6CV Diesel', 0.58);
INSERT into vehicule values(3,'Vehicule 4CV Essence', 0.62);
INSERT into vehicule values(4,'Vehicule 5/6CV Essence', 0.67);
update visiteur set id_vehicule = 1 where id_vehicule is null ;
-- p.ayot -> xiej3uuY0
-- v.artois -> xie2IY8ee
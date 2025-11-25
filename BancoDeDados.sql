//Roda isso aí no seu SQLyog para testar o programa

CREATE DATABASE db_projeto;

USE db_projeto;

CREATE TABLE tb_funcionarios(
	id_funcionarios INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	profissao VARCHAR(255) NOT NULL,
	salario DECIMAL NOT NULL,
	data_inicio DATETIME NOT NULL,
	data_final DATETIME NOT NULL
);

ALTER TABLE tb_funcionarios ADD tempo DATETIME NOT NULL;
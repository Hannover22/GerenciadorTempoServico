CREATE DATABASE db_projeto;

USE db_projeto;

CREATE TABLE tb_funcionarios(
	id_funcionario_clt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	profissao VARCHAR(255) NOT NULL,
	salario DECIMAL NOT NULL,
	clt VARCHAR(10) NOT NULL,
	data_inicio DATETIME NOT NULL,
	data_final DATETIME NOT NULL,
	tempo INT NOT NULL
);
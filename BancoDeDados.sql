CREATE DATABASE db_projeto;

USE db_projeto;

CREATE TABLE tb_funcionarios_clt(
	id_funcionario_clt INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	profissao VARCHAR(255) NOT NULL,
	salario DECIMAL NOT NULL,
	data_inicio DATETIME NOT NULL,
	data_final DATETIME NOT NULL
	tempo INT NOT NULL,
);

CREATE TABLE tb_funcionarios_autarquico(
	id_funcionario_Aut INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	profissao VARCHAR(255) NOT NULL,
	salario DECIMAL NOT NULL,
	data_inicio DATETIME NOT NULL,
	data_final DATETIME NOT NULL
	tempo INT NOT NULL,
);
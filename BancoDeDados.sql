CREATE DATABASE db_projeto;

USE db_projeto;

CREATE TABLE tb_funcionarios(
	id_funcionario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
	profissao VARCHAR(255) NOT NULL,
	salario DECIMAL NOT NULL,
	clt VARCHAR(10) NOT NULL,
	data_inicio DATETIME NOT NULL,
	data_final DATETIME NOT NULL,
	tempo INT NOT NULL
);

CREATE TABLE tb_usuarios(
	id_usuario INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR (255) NOT NULL,
	senha VARCHAR (255) NOT NULL,
	cargo VARCHAR(255) NOT NULL,
);

INSERT INTO tb_usuarios (nome, senha, cargo) VALUES (
	(administrador, 123, adm);
);


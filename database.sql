-- Database..: u187390300_db
-- User......: u187390300_user
-- Password..: u187390300_pass

create database u187390300_db;

use u187390300_db;

create table alunos
( alu_codigo       int(5)       not null auto_increment
, alu_nome         varchar(50)  not null
, alu_email        varchar(50)
, alu_fone         varchar(50)
, alu_status       int(1)       default 1 not null
, constraint pk1_alunos primary key (alu_codigo)
) ;

create table dias
( alu_codigo       int (5)      not null
, dia_dia_semana   int (1)      not null default 2
, dia_hora_ini     time         not null default '00:00'
, dia_hora_fim     time         not null default '00:00'
, dia_preco        numeric(5,2) not null default 0
, constraint pk1_dias primary key (alu_codigo, dia_dia_semana, dia_hora_ini)
, constraint fk1_dias foreign key (alu_codigo) references alunos (alu_codigo)
) ;

create table periodos
( per_codigo        int(9)       not null auto_increment
, per_descricao     varchar(50)  not null
, per_data_ini      date         not null
, per_data_fim      date         not null
, per_status        int(1)       not null default 1
, constraint pk1_periodos primary key (per_codigo)
, constraint uk1_periodos unique (per_data_ini, per_data_fim, per_status)
) ;

create table periodos_alunos
( pal_codigo        int(9)       not null auto_increment
, per_codigo        int(9)       not null
, alu_codigo        int(9)       not null
, pal_vlr_acrescimo numeric(8,2) not null default 0
, pal_vlr_desconto  numeric(8,2) not null default 0
, pal_per_acrescimo numeric(8,2) not null default 0
, pal_per_desconto  numeric(8,2) not null default 0
, constraint pk1_periodos_alunos primary key (pal_codigo)
, constraint fk1_periodos_alunos foreign key (per_codigo) references periodos (per_codigo)
, constraint fk2_periodos_alunos foreign key (alu_codigo) references alunos (alu_codigo)
) ;

create table aulas
( aul_codigo        int(9)       not null auto_increment
, per_codigo        int(9)       not null
, alu_codigo        int(5)       not null
, aul_data_aula     date         not null
, aul_hora_ini      time         not null default '00:00'
, aul_hora_fim      time         not null default '00:00'
, aul_preco         numeric(5,2) not null default 0
, aul_status        int(1)       not null default 1
, constraint pk1_aulas primary key (aul_codigo)
, constraint uk1_aulas unique (alu_codigo, aul_data_aula)
, constraint fk1_aulas foreign key (per_codigo) references periodos (per_codigo)
, constraint fk2_aulas foreign key (alu_codigo) references aulas (alu_codigo)
) ;

create table emails
( ema_codigo             int(9)       not null auto_increment
, ema_data_inclusao      date         not null
, ema_remetente_email    varchar(80)  not null
, ema_remetente_nome     varchar(80)  not null
, ema_destinatario_email varchar(80)  not null
, ema_destinatario_nome  varchar(80)  not null
, ema_assunto            varchar(80)  not null
, ema_mensagem           text         not null
, ema_flg_enviado        char         not null default 'N'
, ema_data_enviado       date
, ema_resposta           text
, constraint pk1_emails primary key (ema_codigo)
) ;
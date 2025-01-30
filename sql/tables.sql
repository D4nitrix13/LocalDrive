-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

-- Tables
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    CHECK (id >= 1) 
);

CREATE TABLE directory (
    id_user BIGSERIAL NOT NULL,
    name TEXT NOT NULL,
    path TEXT NOT NULL UNIQUE,
    parent_directory TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id)
);

CREATE TABLE files (
    id BIGSERIAL PRIMARY KEY NOT NULL UNIQUE,
    id_user BIGINT NOT NULL,
    name TEXT NOT NULL,
    type TEXT NOT NULL,
    path TEXT NOT NULL,
    size BIGINT NOT NULL,
    file_creation_date TIMESTAMPTZ NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id)
);

CREATE TABLE user_email_history (
    id_user BIGINT NOT NULL,
    email TEXT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id)
);

CREATE TABLE deleted_user_history (
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    date_deleted TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shared_directory (
    id_user_property BIGINT NOT NULL,
    id_user_guest BIGINT NOT NULL,
    path TEXT NOT NULL,
    FOREIGN KEY (id_user_property) REFERENCES users(id),
    FOREIGN KEY (id_user_guest) REFERENCES users(id)
);

CREATE TABLE shared_file (
    id_user_property BIGINT NOT NULL,
    id_user_guest BIGINT NOT NULL,
    shared_from_a_directory BOOLEAN NOT NULL DEFAULT FALSE,
    path TEXT NOT NULL,
    FOREIGN KEY (id_user_property) REFERENCES users(id),
    FOREIGN KEY (id_user_guest) REFERENCES users(id)
);

CREATE TABLE notification (
    id BIGSERIAL PRIMARY KEY NOT NULL UNIQUE,
    id_user_remitente BIGINT NOT NULL,
    id_user_destinatario BIGINT NOT NULL,
    motivo TEXT NOT NULL,
    message TEXT NOT NULL,
    visto BOOLEAN NOT NULL,
    FOREIGN KEY (id_user_remitente) REFERENCES users(id),
    FOREIGN KEY (id_user_destinatario) REFERENCES users(id),
    CHECK (id >= 1)
);
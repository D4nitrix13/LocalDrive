-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

-- Indices
CREATE UNIQUE INDEX index_id_user ON users (id);
CREATE INDEX index_data_user ON users (name, email);
-- First and only seeded account for a new installation.
INSERT INTO usuarios (id, usuario_id, nivel_id, nome, senha, status, whatsapp, email, data_add, acesso_remoto)
VALUES (1, 0, 1, 'corte', 'ian123', 'ativo', '', 'admin@localhost', NOW(), 1);

INSERT INTO nivel (id, usuario_id, nome, status, relatorio, data_add)
VALUES (1, 1, 'ADM', 'ativo', 1, NOW());

INSERT INTO nivel_permissoes (usuario_id, nivel_id, permissao, status)
VALUES (1, 1, 'all', 'ativo');

--------------------------------------------
-- Users.
--------------------------------------------
INSERT INTO kl_user (username, algorithm, salt, password, is_active, is_super_admin, last_login, created_at, updated_at)
VALUES ('administrator', 'sha1', '5d90416412b87b1df28e4c0d0d24fed9', 'b3de8c050f73d4c6bcdb1bb68a768e1dfbc87ffd', 1, 0, NOW(), NOW(), NOW());

INSERT INTO kl_user (username, algorithm, salt, password, is_active, is_super_admin, last_login, created_at, updated_at)
VALUES ('guest', 'sha1', '4905f891d185f570a6793ebbcea9cffe', '34aefe0f962601bc617ba0e9efce0fcfc33cbcf1', 1, 0, NOW(), NOW(), NOW());

--------------------------------------------
-- Groups.
--------------------------------------------
INSERT INTO kl_user_group (name, description, created_at, updated_at, created_by, updated_by) VALUES ('guest', 'Гости', NOW(), NOW(), 1, 1);
INSERT INTO kl_user_group (name, description, created_at, updated_at, created_by, updated_by) VALUES ('administrator', 'Администраторы', NOW(), NOW(), 1, 1);
INSERT INTO kl_user_group (name, description, created_at, updated_at, created_by, updated_by) VALUES ('user', 'Пользователи', NOW(), NOW(), 1, 1);
INSERT INTO kl_user_group (name, description, created_at, updated_at, created_by, updated_by) VALUES ('moderator', 'Модераторы', NOW(), NOW(), 1, 1);
INSERT INTO kl_user_group (name, description, created_at, updated_at, created_by, updated_by) VALUES ('author', 'Авторы', NOW(), NOW(), 1, 1);

-------------------------------------------
-- Persmissions.
--------------------------------------------
INSERT INTO kl_permission (name, description, created_at, updated_at, created_by, updated_by) VALUES ('administrator', 'Администраторы', NOW(), NOW(), 1, 1);
INSERT INTO kl_permission (name, description, created_at, updated_at, created_by, updated_by) VALUES ('user', 'Пользователи', NOW(), NOW(), 1, 1);
INSERT INTO kl_permission (name, description, created_at, updated_at, created_by, updated_by) VALUES ('author', 'Авторы', NOW(), NOW(), 1, 1);
INSERT INTO kl_permission (name, description, created_at, updated_at, created_by, updated_by) VALUES ('moderator', 'Модераторы', NOW(), NOW(), 1, 1);

-------------------------------------------
-- Persmissions groups.
--------------------------------------------
INSERT INTO kl_user_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_group WHERE name='administrator' LIMIT 1), (SELECT id FROM kl_user_permission WHERE name='administrator' LIMIT 1), NOW(), NOW());
INSERT INTO kl_user_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_group WHERE name='user' LIMIT 1), (SELECT id FROM kl_user_permission WHERE name='user' LIMIT 1), NOW(), NOW());
INSERT INTO kl_user_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_group WHERE name='moderator' LIMIT 1), (SELECT id FROM kl_user_permission WHERE name='moderator' LIMIT 1), NOW(), NOW());
INSERT INTO kl_user_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_group WHERE name='author' LIMIT 1), (SELECT id FROM kl_user_permission WHERE name='author' LIMIT 1), NOW(), NOW());

-------------------------------------------
-- User groups.
--------------------------------------------
INSERT INTO kl_user_user_group (user_id, group_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_user WHERE username='administrator' LIMIT 1), (SELECT id FROM kl_user_group WHERE name='administrator' LIMIT 1), NOW(), NOW());
INSERT INTO kl_user_user_group (user_id, group_id, created_at, updated_at) VALUES ((SELECT id FROM kl_user_user WHERE username='guest' LIMIT 1), (SELECT id FROM kl_user_group WHERE name='guest' LIMIT 1), NOW(), NOW());

-------------------------------------------
-- User profiles.
--------------------------------------------
--INSERT INTO kl_cms_user (user_id, is_active, created_at, updated_at, created_by, updated_by) VALUES ((SELECT id FROM kl_user_user WHERE username='administrator' LIMIT 1), true, NOW(), NOW(), 1, 1);

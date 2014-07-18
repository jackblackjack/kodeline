--------------------------------------------
-- Groups.
--------------------------------------------
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('guest', 'Гости', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('administrator', 'Администраторы', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('user', 'Пользователи', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('moderator', 'Модераторы', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('manager', 'Менеджеры артистов', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('artist', 'Артисты', NOW(), NOW());
INSERT INTO sf_guard_group (name, description, created_at, updated_at) VALUES ('venue', 'Площадки', NOW(), NOW());

-------------------------------------------
-- Persmissions.
--------------------------------------------
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('administrator', 'Администраторы', NOW(), NOW());
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('user', 'Пользователи', NOW(), NOW());
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('moderator', 'Модераторы', NOW(), NOW());
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('manager', 'Менеджеры артистов', NOW(), NOW());
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('artist', 'Артисты', NOW(), NOW());
INSERT INTO sf_guard_permission (name, description, created_at, updated_at) VALUES ('venue', 'Площадки', NOW(), NOW());

--------------------------------------------
-- Users.
--------------------------------------------
INSERT INTO sf_guard_user (first_name, last_name, email_address, username, algorithm, salt, password, is_active, is_super_admin, last_login, created_at, updated_at)
VALUES ('admin first name', 'admin last name', 'admin@fungo.pro', 'administrator', 'sha1', '5d90416412b87b1df28e4c0d0d24fed9', 'b3de8c050f73d4c6bcdb1bb68a768e1dfbc87ffd', true, false, NOW(), NOW(), NOW());

INSERT INTO sf_guard_user (first_name, last_name, email_address, username, algorithm, salt, password, is_active, is_super_admin, last_login, created_at, updated_at)
VALUES ('guest last name', 'guest last name', 'guest@fungo.pro', 'guest', 'sha1', '4905f891d185f570a6793ebbcea9cffe', '34aefe0f962601bc617ba0e9efce0fcfc33cbcf1', true, false, NOW(), NOW(), NOW());

-------------------------------------------
-- Persmissions groups.
--------------------------------------------
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='administrator' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='administrator' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='user' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='user' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='moderator' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='moderator' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='manager' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='manager' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='artist' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='artist' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_group_permission (group_id, permission_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_group WHERE name='venue' LIMIT 1), (SELECT id FROM sf_guard_permission WHERE name='venue' LIMIT 1), NOW(), NOW());

-------------------------------------------
-- User groups.
--------------------------------------------
INSERT INTO sf_guard_user_group (user_id, group_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_user WHERE username='administrator' LIMIT 1), (SELECT id FROM sf_guard_group WHERE name='administrator' LIMIT 1), NOW(), NOW());
INSERT INTO sf_guard_user_group (user_id, group_id, created_at, updated_at) VALUES ((SELECT id FROM sf_guard_user WHERE username='guest' LIMIT 1), (SELECT id FROM sf_guard_group WHERE name='guest' LIMIT 1), NOW(), NOW());

-------------------------------------------
-- User profiles.
--------------------------------------------
INSERT INTO kl_cms_user (user_id, is_active, created_at, updated_at, created_by, updated_by) VALUES ((SELECT id FROM sf_guard_user WHERE username='administrator' LIMIT 1), true, NOW(), NOW(), 1, 1);

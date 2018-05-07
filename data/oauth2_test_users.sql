INSERT INTO oauth_clients (name, secret, redirect, personal_access_client, password_client)
VALUES ('client_test', '$2y$10$fFlZTo2Syqa./0JJ2QKV4O/Nfi9cqDMcwHBkN/WMcRLLlaxYUP2CK', '/redirect', 1, 1),
('client_test2', '$2y$10$fFlZTo2Syqa./0JJ2QKV4O/Nfi9cqDMcwHBkN/WMcRLLlaxYUP2CK', '/redirect', 0, 0);

INSERT INTO oauth_users (username, password)
VALUES ('user_test', '$2y$10$DW12wQQvr4w7mQ.uSmz37OQkKcIZrRZnpXWoYue7b5v8E/pxvsAru');

INSERT INTO oauth_scopes (id)
VALUES ('test');


DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(80),
  email VARCHAR(255) NOT NULL,
  password VARCHAR(60) NOT NULL
);

INSERT INTO users
  (id, name, email, password)
VALUES
  (1, 'Foo', 'foo@host.com', '$2y$10$34w0udB8WTSKEzkaRzgmHev8Lx5EcK07Fs.SMZXnNc8w3yNPUXjNW'),
  (2, 'Bar', 'bar@host.com', '$2y$10$9wTSa.QrGxP9Q3zjLC74cebwA1ro5a7JOzvFHnSCApPDoutRfvGmW');

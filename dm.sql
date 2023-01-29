

INSERT INTO role VALUES(
NULL, 'free'
);
INSERT INTO role VALUES(
NULL, 'paid'
);
INSERT INTO role VALUES (
NULL, 'admin'
);


INSERT INTO user VALUES(
NULL, 'chad@smith.com', 0, 'chad', '$2y$10$mwCpA3.uIv.ZICeUA/gvGOmzNlNIHBmFFNh3tqLQqjT0BRT6EXJDC',
'', 1, NOW(), NOW(), 'Chad Smith', NULL, NULL
);

INSERT INTO role_user VALUES(
NULL, 1, 1
);

INSERT INTO user VALUES(
NULL, 'money@bags.com', 0, 'money', '$2y$10$mwCpA3.uIv.ZICeUA/gvGOmzNlNIHBmFFNh3tqLQqjT0BRT6EXJDC',
'', 1, NOW(), NOW(), 'Money Bags', NULL, NULL
);

INSERT INTO role_user VALUES (
NULL, 2, 2
);


INSERT INTO user VALUES(
NULL, 'mail@mikejw.co.uk', 2, 'mikejw', '$2y$10$mwCpA3.uIv.ZICeUA/gvGOmzNlNIHBmFFNh3tqLQqjT0BRT6EXJDC',
'', 1, NOW(), NOW(), 'Mike Whiting', NULL, NULL
);


INSERT INTO role_user VALUES(
NULL, 3, 3
);

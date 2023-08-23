CREATE TABLE IF NOT EXISTS User
(
    id        INTEGER PRIMARY KEY AUTO_INCREMENT,
    firstname TEXT NOT NULL,
    lastname  TEXT NOT NULL,
    email     TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Permission
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS UserPermission
(
    user_id       INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (id),
    FOREIGN KEY (permission_id) REFERENCES Permission (id),
    PRIMARY KEY (user_id, permission_id)
);

CREATE TABLE IF NOT EXISTS `Group`
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS UserGroup
(
    user_id  INTEGER NOT NULL,
    group_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (id),
    FOREIGN KEY (group_id) REFERENCES `Group` (id),
    PRIMARY KEY (user_id, group_id)
);

CREATE TABLE IF NOT EXISTS GroupPermission
(
    group_id      INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES `Group` (id),
    FOREIGN KEY (permission_id) REFERENCES Permission (id),
    PRIMARY KEY (group_id, permission_id)
);

CREATE TABLE IF NOT EXISTS AuthKey
(
    id        INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id   INTEGER NOT NULL,
    `key`     TEXT    NOT NULL UNIQUE,
    method    TEXT    NOT NULL,
    once_only BOOLEAN NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (id)
);

CREATE TABLE IF NOT EXISTS Email
(
    email_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    receiver varchar(255) NOT NULL,
    subject  varchar(255) NOT NULL,
    content  TEXT         NOT NULL
);

CREATE TABLE IF NOT EXISTS StudentInformation
(
    user_id      INTEGER PRIMARY KEY,
    year         INTEGER   NOT NULL,
    tutor        TEXT      NOT NULL,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User (id)
);

CREATE TABLE IF NOT EXISTS TimeTable
(
    user_id  INTEGER PRIMARY KEY,
    DoW      INTEGER                                NOT NULL,
    `period` INTEGER                                NOT NULL,
    state    ENUM ('free', 'odd', 'even', 'always') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (id)
);

CREATE TABLE IF NOT EXISTS QuizCategory
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Quiz
(
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,
    category_id INTEGER NOT NULL,
    name        TEXT    NOT NULL,
    FOREIGN KEY (category_id) REFERENCES QuizCategory (id)
);

CREATE TABLE IF NOT EXISTS QuizQuestion
(
    id       INTEGER AUTO_INCREMENT,
    quiz_id  INTEGER NOT NULL,
    question TEXT    NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES Quiz (id),
    PRIMARY KEY (id, quiz_id)
);


CREATE TABLE IF NOT EXISTS QuizAnswer
(
    id            INTEGER AUTO_INCREMENT,
    user_id       INTEGER NOT NULL,
    overall_score INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (id),
    PRIMARY KEY (id, user_id)
);

CREATE TABLE IF NOT EXISTS QuizQuestionAnswer
(
    quiz_answer_id   INTEGER NOT NULL,
    quiz_question_id INTEGER NOT NULL,
    answer_score     INTEGER NOT NULL,
    FOREIGN KEY (quiz_answer_id) REFERENCES QuizAnswer (id),
    FOREIGN KEY (quiz_question_id) REFERENCES QuizQuestion (id),
    PRIMARY KEY (quiz_answer_id, quiz_question_id)
);

CREATE TABLE IF NOT EXISTS EventType
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Room
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Preset
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    tech TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Event
(
    id                 INTEGER PRIMARY KEY AUTO_INCREMENT,
    organizer_id       INTEGER  NOT NULL,
    type_id            INTEGER  NOT NULL,
    room_id            INTEGER  NOT NULL,
    title              TEXT     NOT NULL,
    description        TEXT     NOT NULL,
    needs_consultation BOOLEAN  NOT NULL,
    from_time          DATETIME NOT NULL,
    to_time            DATETIME NOT NULL,
    construction_from  DATETIME NOT NULL,
    construction_to    DATETIME NOT NULL,
    dismantling_from   DATETIME NOT NULL,
    dismantling_to     DATETIME NOT NULL,
    disabled           BOOLEAN  NOT NULL DEFAULT FALSE,
    FOREIGN KEY (organizer_id) REFERENCES User (id),
    FOREIGN KEY (type_id) REFERENCES EventType (id),
    FOREIGN KEY (room_id) REFERENCES Room (id)
);

CREATE TABLE IF NOT EXISTS EventPreset
(
    event_id  INTEGER NOT NULL,
    preset_id INTEGER NOT NULL,
    FOREIGN KEY (event_id) REFERENCES Event (id),
    FOREIGN KEY (preset_id) REFERENCES Preset (id),
    PRIMARY KEY (event_id, preset_id)
);

CREATE TABLE IF NOT EXISTS Shift
(
    id        INTEGER PRIMARY KEY AUTO_INCREMENT,
    name      TEXT     NOT NULL,
    needed    INTEGER  NOT NULL,
    from_time DATETIME NOT NULL,
    to_time   DATETIME NOT NULL,
    event_id  INTEGER  NOT NULL,
    FOREIGN KEY (event_id) REFERENCES Event (id)
);

CREATE TABLE IF NOT EXISTS UserShift
(
    user_id   INTEGER   NOT NULL,
    shift_id  INTEGER   NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES User (id),
    FOREIGN KEY (shift_id) REFERENCES Shift (id),
    PRIMARY KEY (user_id, shift_id)
);

CREATE TABLE IF NOT EXISTS EventLog
(
    id        INTEGER PRIMARY KEY AUTO_INCREMENT,
    event_id  INTEGER                 NOT NULL,
    user_id   INTEGER                 NOT NULL,
    timestamp TIMESTAMP               NOT NULL DEFAULT NOW(),
    message   TEXT                    NOT NULL,
    type      ENUM ('chat', 'change') NOT NULL,
    FOREIGN KEY (event_id) REFERENCES Event (id),
    FOREIGN KEY (user_id) REFERENCES User (id)
);

CREATE TABLE IF NOT EXISTS EquipmentCategory
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS EquipmentLocation
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS EquipmentManufacturer
(
    id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Equipment
(
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
    count           INTEGER NOT NULL,
    name            TEXT    NOT NULL,
    category_id     INTEGER NOT NULL,
    location_id     INTEGER NOT NULL,
    manufacturer_id INTEGER NOT NULL,
    FOREIGN KEY (category_id) REFERENCES EquipmentCategory (id),
    FOREIGN KEY (location_id) REFERENCES EquipmentLocation (id),
    FOREIGN KEY (manufacturer_id) REFERENCES EquipmentManufacturer (id)
);

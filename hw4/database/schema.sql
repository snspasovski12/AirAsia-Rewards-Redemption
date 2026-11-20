CREATE TABLE ACCOUNT (
    accountId INTEGER PRIMARY KEY,
    userId INTEGER NOT NULL
        CHECK (typeof(userId) = 'integer' AND userId > 0),
    accountType TEXT NOT NULL
        CHECK (
            typeof(accountType) = 'text'
            AND length(accountType) BETWEEN 1 AND 100
        ),
    points INTEGER NOT NULL
        CHECK (typeof(points) = 'integer' AND points >= 0)
);

CREATE TABLE GIFTCARD (
    cardId INTEGER PRIMARY KEY,
    cardName TEXT NOT NULL
        CHECK (
            typeof(cardName) = 'text'
            AND length(cardName) BETWEEN 1 AND 100
        ),
    cardType TEXT NOT NULL
        CHECK (
            typeof(cardType) = 'text'
            AND length(cardType) BETWEEN 1 AND 100
        ),
    cardValue REAL NOT NULL
        CHECK (
            typeof(cardValue) IN ('integer', 'real')
            AND cardValue > 0
        ),
    points INTEGER NOT NULL
        CHECK (typeof(points) = 'integer' AND points > 0)
);

CREATE TABLE USER (
    userId INTEGER PRIMARY KEY,
    userName TEXT NOT NULL UNIQUE
        CHECK (
            typeof(userName) = 'text'
            AND length(userName) BETWEEN 1 AND 100
        ),
    password TEXT NOT NULL,
    firstName TEXT NOT NULL
        CHECK (
            typeof(firstName) = 'text'
            AND length(firstName) BETWEEN 1 AND 100
        ),
    lastName TEXT NOT NULL
        CHECK (
            typeof(lastName) = 'text'
            AND length(lastName) BETWEEN 1 AND 100
        ),
    role TEXT NOT NULL
        CHECK (role IN ('Admin', 'Customer'))
);

CREATE TABLE REDEMPTION (
    redeemId INTEGER PRIMARY KEY,
    date TEXT NOT NULL
        CHECK (
            typeof(date) = 'text'
            AND length(date) BETWEEN 1 AND 100
        ),
    accountId INTEGER NOT NULL
        REFERENCES ACCOUNT(accountId),
    cardId INTEGER NOT NULL
        REFERENCES GIFTCARD(cardId),
    pointsRedeemed INTEGER NOT NULL
        CHECK (
            typeof(pointsRedeemed) = 'integer'
            AND pointsRedeemed > 0
        )
);
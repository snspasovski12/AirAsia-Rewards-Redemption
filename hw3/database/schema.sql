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
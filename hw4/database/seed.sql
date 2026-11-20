INSERT INTO USER (userId, userName, password, firstName, lastName, role) VALUES
    (1001, 'bsmith', '$2y$12$Cc5mdP6l7ICwBH..3YdECOXjT4xLUpD44UHgw7OISDkyebcFG/pOK', 'Brian', 'Smith', 'Admin'),
    (1002, 'pjones', '$2y$12$qsdmDWt0BaxbfdWYDlcwEeiN/c87msYkJAdLrOyE/YEgzkohSzB7q', 'Patricia', 'Jones', 'Customer'),
    (1003, 'lchen', '$2y$12$NPcHcf0XFZ2l9qUGLAloCeTmeiW9dboQg8h.JIRq3MZi.0JKYF/kq', 'Li', 'Chen', 'Customer'),
    (1004, 'npatel', '$2y$12$YZQs6UZRiBltd0dYMOC8KeRgRC2Bm8gPyoWKU0H20UBQhoZ.Av40W', 'Neha', 'Patel', 'Customer'),
    (1005, 'adavis', '$2y$12$G43sg7crpXnvzL06iYweH./yCUsKScG7EMph/GhyyY3sSOfLcdehu', 'Alex', 'Davis', 'Customer');

INSERT INTO ACCOUNT (userId, accountType, points) VALUES
    (1001, 'Standard Rewards', 12500),
    (1002, 'Premium Rewards', 28750),
    (1003, 'Standard Rewards', 6400),
    (1004, 'Family Rewards', 41200),
    (1005, 'Standard Rewards', 9300);

INSERT INTO GIFTCARD (cardName, cardType, cardValue, points) VALUES
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000),
    ('AirAsia Travel eGift Card - Platinum', 'Travel eGift Card', 200.00, 15000),
    ('AirAsia Travel eGift Card - Silver', 'Travel eGift Card', 50.00, 5000),
    ('AirAsia Travel eGift Card - Gold', 'Travel eGift Card', 100.00, 10000);
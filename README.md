# AirAsia-Rewards-Redemption
AirAsia Rewards IS 6465 Homework


### Use Case Traceability

| Use Case | PHP Page or Pages | Database Table | Test Evidence |
|---|---|---|---|
| View a List of Gift Cards | `hw3/card-list.php` | GIFTCARD | Opened the Gift Card List and verified the Silver, Gold, and Platinum records displayed from SQLite. |
| View Details of One Gift Card | `hw3/card-details.php` | GIFTCARD | Selected Card #1 and verified its name, type, value, points, and image. |
| Add a Gift Card | `hw3/card-add.php` | GIFTCARD | Submitted test Card #21 and confirmed the database count increased from 20 to 21. |
| Update a Gift Card | `hw3/card-update.php` | GIFTCARD | Updated Card #21 and verified the new value and points using SQLite. |
| Delete a Gift Card | `hw3/card-delete.php` | GIFTCARD | Confirmed deletion using POST and verified the database returned to 20 records. |
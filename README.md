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
| Login to the system | `hw4/login.php`, `hw4/authenticate.php` | USER | Admin `bsmith` and Customer `pjones` successfully authenticated using `password_verify()`, and invalid credentials were rejected. |
| Log out of the system | `hw4/logout.php` | Session only | Logout destroyed the authenticated session and redirected to `login.php`. |
| Add a new customer | `hw4/cust-add.php` | USER, ACCOUNT | Admin created `hw4testuser`, customer ID 1006, with a hashed password and a Standard Rewards account containing 10000 initial points. Customer creation and rollback were also tested. |
| View customer profile and points | `hw4/customer-profile.php` | USER, ACCOUNT | `hw4testuser` successfully viewed their own profile and initial 10000 points. Session-based ownership and role authorization were tested. |
| Redeem a Gift Card | `hw4/redeem.php`, `hw4/process-redemption.php` | ACCOUNT, GIFTCARD, REDEMPTION | Silver redemption ID 1 deducted 5000 points and inserted a REDEMPTION record. A second Silver redemption ID 2 was also recorded, leaving the balance at 0. A later Platinum attempt was rejected for insufficient points. Automated testing separately verified transaction rollback, account ownership, and concurrency handling. |

Authentication and role-based authorization are enforced server-side. Admin-only pages cannot be accessed directly by Customer users, and the existing HW3 Gift Card CRUD functionality remains available to Admin users. The redemption system uses a transaction and conditional account point deduction to prevent an account from being overdrawn. Two Silver redemption records were observed; their cause was not established. Duplicate-submission prevention has not been verified.
## Part 8: Test Report

| Test ID | Test |
|---|---|
| T01 | Validation test harness |
| T02 | View Gift Card List |
| T03 | View Gift Card Details |
| T04 | Add Gift Card |
| T05 | Update Gift Card |
| T06 | Delete Gift Card |
| T07 | Invalid or altered input handling |
| T08 | Invalid Gift Card identifier handling |

### T01 — Validation Test Harness

**Test Steps and Input:**
Ran `php hw3/tests/validation-tests.php` from the repository root.

**Expected Result:**
All validation tests should return PASS, with zero failures.

**Actual Result:**
The test harness completed with 9 passed and 0 failed.

**Status:** PASS

### T02 — View Gift Card List

**Test Steps and Input:**
Opened `card-list.php` in the browser with the SQLite database populated.

**Expected Result:**
The Gift Card List displays saved records with card names,
images, and points required.

**Actual Result:**
The application displayed the AirAsia Silver, Gold, and
Platinum gift cards with their corresponding images and
points requirements.

**Status:** PASS

### T03 — View Gift Card Details

**Test Steps and Input:**
Selected Silver Gift Card #1 from the Gift Card List.

**Expected Result:**
The details page displays the selected card's name, type,
value, points required, and image.

**Actual Result:**
The application displayed the Silver gift card with
a $50.00 value, 5,000 points, and the correct image.

**Status:** PASS

### T04 — Add Gift Card

**Test Steps and Input:**
Submitted a new test gift card through `card-add.php`
with a value of $15.00 and 1,500 points.

**Expected Result:**
The application creates a new GIFTCARD record and
redirects to the Gift Card List.

**Actual Result:**
The application created Card #21, and the database
count increased from 20 to 21.

**Status:** PASS

### T05 — Update Gift Card

**Test Steps and Input:**
Opened `card-update.php?card_id=21` and changed the
test card value from $15.00 to $20.00 and points
required from 1,500 to 2,000.

**Expected Result:**
Only the selected gift card is updated, and its
new information appears in the database.

**Actual Result:**
Card #21 retained its original ID and displayed
a value of $20.00 and 2,000 points in SQLite.

**Status:** PASS

### T06 — Delete Gift Card

**Test Steps and Input:**
Opened `card-delete.php?card_id=21`, reviewed the
confirmation page, and selected Confirm Delete.

**Expected Result:**
The card is deleted only after POST confirmation,
and the application redirects to the Gift Card List.

**Actual Result:**
The server returned HTTP 303 after POST and redirected
to the Gift Card List. SQLite confirmed the database
count returned to 20 records.

**Status:** PASS

### T07 — Invalid or Altered Input Handling

**Test Steps and Input:**
Tested a negative Card Value of -10 using the reusable
validateCardValue() PHP function.

**Expected Result:**
The invalid value is rejected with an appropriate
validation message, and the database count remains 21.

**Actual Result:**
The validation function returned "Card Value must be a
positive number." The database retained 21 gift cards.

**Status:** PASS

### T08 — Invalid Gift Card Identifier Handling

**Test Steps and Input:**
Opened `card-details.php?card_id=abc` to test an invalid
identifier. Then opened `card-details.php?card_id=9999`
to test a nonexistent gift card.

**Expected Result:**
The application rejects invalid identifiers and handles
nonexistent gift cards without displaying incorrect records.

**Actual Result:**
The invalid identifier displayed "Invalid gift card ID."
The nonexistent identifier displayed "Gift card not found."
Both pages retained the AirAsia styling.

**Status:** PASS

## Part 9: HW4 Test Report

The existing report uses T01-T08 for HW3. No different T09-T16 naming convention was present.

| Test ID | Description | Test Steps or Inputs | Expected Result | Actual Result | Status | Evidence / Verification |
|---|---|---|---|---|---|---|
| T09 | Successful Admin login | In the browser, submit username `bsmith` and password `mysecret` on `hw4/login.php`. | Valid credentials authenticate the user as Admin and open the application. | `bsmith` successfully logged in as Admin. | PASS | Manual browser testing; `hw4/authenticate.php` checks the stored password with `password_verify()`. |
| T10 | Successful Customer login | In the browser, submit username `pjones` and password `acrobat` on `hw4/login.php`. | Valid credentials authenticate the user as Customer and open the application. | `pjones` successfully logged in as Customer. | PASS | Manual browser testing; `hw4/authenticate.php` checks the stored password with `password_verify()`. |
| T11 | Invalid credentials rejected | Submit invalid login credentials. | The login is rejected with a generic error that does not disclose which credential was incorrect. | The generic login error was displayed. | PASS | Manual browser testing. |
| T12 | Session protection and logout | Request a protected page without a session; send GET to `hw4/logout.php`; then log out using POST and revisit a protected page. | Unauthenticated protected requests redirect to login; GET logout is rejected; POST logout terminates the session and redirects to login. | Protected access was redirected, GET logout was rejected, and logout terminated access to protected pages. | PASS | Manual browser testing. |
| T13 | Role-based authorization and direct URL restrictions | As a Customer, request the Admin-only gift-card add, update, and delete pages and `hw4/cust-add.php` directly; POST an attempt to modify a gift card. | Admin-only requests are denied with HTTP 403 and Customer modification attempts do not change gift cards. | The Customer requests returned 403; the gift-card count remained 20. | PASS | Manual browser testing and direct database verification. |
| T14 | Add Customer validation, password hashing, and transactional account creation | Admin creates `hw4testuser`; inspect the resulting USER and ACCOUNT records. Separately exercise invalid/duplicate input and a failed account insert. The new customer's password was reset before checking it with `password_verify()`. | Invalid or duplicate input is rejected; successful creation writes a hashed password and related Standard Rewards account with 10000 points; failed account creation leaves no partial USER record. | `hw4testuser` was created as userId 1006 with a Standard Rewards account at 10000 points. The reset password passed `password_verify()`; the initially entered password was not verified. Creation validation and rollback were also tested. | PASS | Manual browser testing and direct database verification for userId/account/balance; Copilot-reported automated testing on a disposable database for validation, password verification, and rollback. |
| T15 | Customer Profile ownership and account balances | Sign in as `hw4testuser` and open `hw4/customer-profile.php`; automated checks cover session ownership, multiple accounts, output escaping, and a customer without an account. | The profile shows only the signed-in customer's information and accurate account balances, safely handling multiple or missing accounts. | The profile displayed `hw4testuser`'s account and initial 10000 points. Automated checks verified session-based ownership, multiple-account handling, safe output, and missing-account behavior. | PASS | Manual browser testing for the profile and balance; Copilot-reported automated testing for ownership, multiple accounts, safe output, and missing-account handling. |
| T16 | Gift Card Redemption | Redeem Silver cardId 1 twice using accountId 6; inspect redemption history and balance; then attempt Platinum. Automated tests separately cover Silver/Gold, insufficient points, account ownership, invalid input, CSRF, rollback, and concurrent requests. | Successful redemptions deduct the database point cost and create records atomically; insufficient or unauthorized requests create no redemption; concurrent requests cannot overdraw points. | Redemption IDs 1 and 2 each recorded a 5000-point Silver redemption on 2026-11-20 for accountId 6; its balance became 0. The later Platinum attempt was rejected for insufficient points, and no Platinum record appeared in the reviewed history. Automated tests separately passed the listed transaction, ownership, invalid-input, CSRF, rollback, and concurrency checks. | PASS | Manual browser testing and direct database verification for redemption IDs, cardId, date, and balance; Copilot-reported automated testing on a disposable database. |

**Notes:** The cause of the second Silver redemption was not established. The two observed records are not being characterized as a confirmed application defect, and these results do not establish that duplicate submissions are prevented.
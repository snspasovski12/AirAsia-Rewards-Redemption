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
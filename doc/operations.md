[⬅️ Back to documentation](../README.md)

---

# Operations

Operations can be imported **from the backoffice** in the `Import operations` admin panel.

The administration displays all the necessary requirements to make this work. Nothing to add here ☺.

Here are the overall requirements:

* File format must be CSV.
* File name must be `{year}-{month}.csv`.
* **First line of the file is ignored**, so it can contain headers or an empty line or anything.
* Each line column must be the following, **in this order**:
  1. Date in `d/m/Y` format (nothing else supported for now).
  2. Operation type (given by bank provider, can be an empty string).
  3. Display type (not mandatory, can be an empty string).
  4. Operation details (can be a longer string).
  5. Amount of the operation. Can be a negative number.
     > Note that this can be a string like `1 234,55` or `1,234.55`. Every non-digit character (except `+` and `-`) will be removed and all remaining numbers will make the amount **in cents**. Do not store floats to avoid [floating point issues](https://0.30000000000000004.com/) (read this link, if you need to know why you should avoid storing floats).

---
[⬅️ Back to documentation](../README.md)

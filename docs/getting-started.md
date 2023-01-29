

ELib ACL
===

Getting started
---

Setup
---

Follow the instructions in the Empathy "getting-started.md" docs:
[Empathy Getting Started](https://github.com/mikejw/empathy/blob/master/docs/getting-started.md).

Hower use the following `composer.json` configuration:

    {
        "require": {
           "mikejw/elib-acl": "dev-main",
        },
        "minimum-stability": "dev"
    }


ELib-Base
---
Follow the instructions here:
[ELib-Base Getting Started](https://github.com/mikejw/elib-base/blob/master/docs/getting-started.md).

However after completing the "Database setup" block, copy and paste the contents of `dd.sql` into your
global `setup.sql` file after the existing `CREATE` statements.  Also append to the end of the `DROP`statment
the new table names so that it looks like this:

    DROP TABLE IF EXISTS user, contact, shippingaddr, role, role_user;    

Also use the contents of `dm.sql` for your `inserts.sql` file instead of the elib-base `dm.sql` file. 
Don't forget to put he `USE` statement at the top of the file:

    use project;

Complete the rest of the steps the elib-base getting started doc.


More info coming soon!





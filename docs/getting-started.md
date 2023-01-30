

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
           "mikejw/elib-acl": "dev-main"
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


Enable plugin
---
Add the plugin to your `config.yml` file:

    plugins:
      - 
        name: Empathy\Elib\MVC\Plugin\Acl
        version: 1.0


Services
---

Create a global `services.php` configuration file, which will make ACL definitions availalbe in your app.  The default
configuration will look like the following (for the roles and users inserted into the database above):


    <?php
    use Laminas\Permissions\Acl\Acl;
    use Laminas\Permissions\Acl\Role\GenericRole as Role;
    use Laminas\Permissions\Acl\Resource\GenericResource as Resource;
    use Empathy\ELib\User\AclUser;

    return [
        'Acl' => function(\DI\Container $c) {
            $acl = new Acl();

            $guest = new Role('guest');
            $free = new Role('free');
            $paid = new Role('paid');
            $admin = new Role('admin');

            $acl->addRole($guest);
            $acl->addRole($free, [$guest]);
            $acl->addRole($paid, [$free]);
            $acl->addRole($admin, [$paid]);

            $acl->addResource(new Resource('public-api'));
            $acl->addResource(new Resource('free-api'));
            $acl->addResource(new Resource('paid-api'));
            $acl->addResource(new Resource('admin-area'));
            
            $acl->allow($guest, 'public-api');
            $acl->allow($free, 'free-api');        
            $acl->allow($paid, 'paid-api');
            $acl->allow($admin, 'admin-area');

            $acl->allow($guest, 'paid-api', 'view');

            return $acl;
        },
        'UserModel' => 'MobileUserItem',
        'CurrentUser' => function (\DI\Container $c) {
            return new AclUser();
        },
    ];


JWT Config
--

Create an arbitrary secret in `elib.yml` used for JWT encyption/decyption:

    ---
    jwt_secret: my_super_secret_key



(Optional) Force vanilla validation for login
---
The user controller for login (at the `/user/login` route) can use the default user model for validation instead of the new one provided by ELib-ACL.  
This means usernames can be used to log into the site backend admin area instead of having to provide an email address.

Do so by overwriting `application/user/user.php` with:
 
    <?php
    namespace Empathy\MVC\Controller;

    class user extends \Empathy\ELib\User\Controller
    {
        public function login()
        {
            $this->userModel = 'UserItem';
            parent::login();
        }    
    }


Secure admin module with admin role
---
We need to tell the user service to secure `application/admin/admin.php` (routes under `/admin`) with the admin role.  This is achieved by overwriting with:

    <?php
    namespace Empathy\MVC\Controller;
    use Laminas\Permissions\Acl\Resource\ResourceInterface;

    class admin extends \Empathy\ELib\AdminController implements ResourceInterface
    {
        public function __construct($boot)
        {
            parent::__construct($boot, false);
        }

        public function getResourceId()
        {
            return 'admin-area';
        }
    }

You may want to experiment at this point by trying different roles returned by `getResourceId()`.  For example if you use the role `public-api`, 
a user can navigate to the admin portion of your app without logging in at all.

NB:  All new classes that you create in the admin module should follow this structure and be secured with your desired role(s).

NB:  If you fail to do this step, you will notice that the admin area authorisation will behave as you expect if you log in with the `mikejw` account as this user has an auth level 2 in the `inserts.sql` fixture data.













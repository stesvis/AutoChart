phpMasterLube
=============

A Symfony 3 project that helps you keep track of your vehicle maintenance.

Here you can see the app live: <a href="http://phpmasterlube.stesvis.com">Master Lube</a>

<h4>Configure FOSUserBundle</h4>
http://symfony.com/doc/current/bundles/FOSUserBundle/index.html
<p>
Dump the database:
    <pre>php bin/console doctrine:database:drop --force</pre>
</p>
<p>
Create the database:
    <pre>php bin/console doctrine:database:create</pre>
</p>
<p>
Create the tables:
    <pre>php bin/console doctrine:schema:update --force</pre>
</p>
<p>
Create a super admin user:
    <pre>php bin/console fos:user:create admin --super-admin</pre>
</p>
<p>
Clear the cache:
    <pre>php bin/console cache:clear</pre>
</p>

<h5>Fixtures</h5>
<p>
Load fixtures:
    <pre>php bin/console doctrine:fixtures:load</pre>
</p>
<h5>Migrations</h5>
<p>
Run Migrations:
    <pre>php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate</pre>
</p>

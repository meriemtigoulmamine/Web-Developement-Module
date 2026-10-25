OBJECTIF 8 - RUN THE PROJECT WITH XAMPP (WINDOWS)

1. Extract the Objectif8 folder into C:\xampp\htdocs\ so the path is C:\xampp\htdocs\Objectif8\.
2. Open the XAMPP Control Panel. Start Apache and MySQL.
3. Open http://localhost/phpmyadmin/ in your browser.
4. Select Import, choose database.sql from C:\xampp\htdocs\Objectif8\, and run the import.
5. Open http://localhost/Objectif8/ to view the site.
6. Register with a valid ESI address ending in @esi.dz. Log in from the researcher login page. The project form appears on the homepage only after researcher login.
7. Create the first administrator at http://localhost/Objectif8/create_admin.php. Choose your own username and password (at least 8 characters). There is no default admin password.
8. Delete create_admin.php from the server after creating the account. Admin login: http://localhost/Objectif8/admin/login.php
9. Submit a researcher project. Sign in as admin; the request appears in the dashboard table. Use the status menu to update it.

If your XAMPP folder is not C:\xampp, use the equivalent htdocs folder. Database connection settings are in db_connection.php (defaults: localhost, root, empty password, database tdw).

The project document upload fields shown in the original page are not implemented in this beginner version; the request form saves the project details in MySQL.
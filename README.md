# Web-based Store Management System README

## 1. Project Overview

This project is a comprehensive web-based management system, specifically designed to assist administrators in handling various management tasks, including commodity management, user management, and role management. With a user-friendly interface and powerful functionality, it aims to streamline administrative operations.

**Caution**: Please note that this project is currently in its framework stage and has significant security vulnerabilities. Use it with extreme caution, and do not deploy it in a production environment without proper security enhancements.

## 2. Overall Structure

The project's directory structure is organized as follows:

**Root Directory (**`mm/`**)**:

`db.php`: Core PHP file for establishing a connection to the MySQL database.

`login.php`: Handles user login functionality.

`index.php`: The main entry page of the system.

`sources/`** Directory**: Stores commodity images, which are located in the `commodity/` sub - directory.

`styles/`** Directory**: Contains CSS files for styling different parts of the application:

`login.css`: Styles for the login page.

Sub - directories for commodity management, role management, and user management, each with their specific styling files.

`js/`** Directory**: Holds JavaScript files to add interactivity to web pages. Similar to the `styles/` directory, it has sub - directories for different management sections.

`store/`** Directory**: Contains PHP files for managing various aspects of the store:

`goods/` (sub - directory for commodity management).

`manage/role/` (sub - directory for role management).

`manage/user/` (sub - directory for user management).

## 3. Key Features

### 3.1 Commodity Management

**List Display**: On the `index.php` page, commodities are presented in a tabular format. Administrators can search for commodities by name or status, and perform batch operations like selecting all, unselecting all, and deleting selected commodities.

**Add Commodity**: The `create.php` page enables administrators to add new commodities. They can input details such as item name, price, sales, category, inventory, status, and upload a corresponding image.

**Edit Commodity**: An `editForm` is provided for modifying existing commodity information. All relevant fields and the associated image can be updated.

**Delete Commodity**: Administrators can delete single or multiple commodities after confirmation.

### 3.2 User Management

While not fully detailed in the code snippets, the system is expected to support the management of administrators, including adding new administrators, editing their information, and removing them from the system.

### 3.3 Role Management

The system likely offers features for managing roles. Administrators can add new roles, ensuring that each role has a unique name.

## 4. Technical Implementation

### 4.1 Backend

**Programming Language**: PHP is used for server - side scripting.

**Database**: A connection to a MySQL database is established using the `mysqli` extension in the `db.php` file.

**User Authentication**: PHP sessions are implemented to ensure that only authenticated administrators can access the management pages.

### 4.2 Frontend

**HTML**: Used for structuring web pages.

**CSS**: Responsible for styling the application.

**JavaScript**: Adds interactivity to web pages.

**SVG Icons**: Employed to enhance the visual appeal of the user interface.

## 5. Conclusion

This project offers a comprehensive framework for store management, enabling administrators to manage commodities, users, and roles efficiently through a unified web - based interface. However, due to its current low security performance, it requires significant security improvements before being used in a real-world scenario.

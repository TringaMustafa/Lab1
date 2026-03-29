# Restaurant Ordering and Reservation System

## Project Description

This project is a full-stack web application developed for managing a restaurant system. It allows users to browse available menu items, place orders online, and reserve tables based on availability. At the same time, it provides an admin panel where the restaurant staff can manage menus, tables, orders, and reservations.

The main goal of this project is to simplify the restaurant workflow by digitalizing ordering and reservation processes, while also giving administrators full control over the system.

---

## Technologies Used

### Backend

The backend of the application is developed using Laravel. It provides a RESTful API that handles all the business logic, data processing, and communication with the database.

### Frontend

The frontend is built using Vue.js. It is responsible for displaying data to the user, handling interactions, and communicating with the backend through API requests. Tailwind CSS is used for styling and creating a responsive user interface.

### Database

The application uses MySQL as a relational database. All data such as users, menus, orders, tables, and reservations are stored and managed there.

### Authentication

JWT (JSON Web Token) is used for authentication. This ensures that only authenticated users can access protected parts of the system.

---

## System Roles

### User

A normal user can:

* Create an account and log in
* Browse the menu
* Add items to cart and place orders
* Reserve a table
* View their order and reservation history
* Edit their own reservations

### Admin

An administrator has additional privileges:

* Access the dashboard
* Manage menu items (create, update, delete)
* Manage orders and update their status
* Manage tables
* Manage reservations

---

## Main Features

* Online ordering system
* Table reservation system with time validation
* Admin dashboard with statistics
* Full CRUD functionality for main entities
* User history for orders and reservations
* Input validation and error handling
* Responsive design for different screen sizes

---

## Installation and Setup

### Backend

1. Clone the project:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git
```

2. Navigate to backend folder:

```bash
cd backend
```

3. Install dependencies:

```bash
composer install
```

4. Configure the `.env` file with your database settings

5. Run migrations:

```bash
php artisan migrate
```

6. Start the server:

```bash
php artisan serve
```

---

### Frontend

1. Navigate to frontend folder:

```bash
cd frontend
```

2. Install dependencies:

```bash
npm install
```

3. Run the development server:

```bash
npm run dev
```

---

## API Overview

### Authentication

* POST /api/register
* POST /api/login
* GET /api/me
* POST /api/logout

### Menus

* GET /api/public-menus
* GET /api/menus
* POST /api/menus
* PUT /api/menus/{id}
* DELETE /api/menus/{id}

### Orders

* POST /api/orders
* GET /api/orders
* GET /api/my-orders
* PUT /api/orders/{id}
* DELETE /api/orders/{id}

### Tables

* GET /api/tables
* GET /api/tables/available
* POST /api/tables
* PUT /api/tables/{id}
* DELETE /api/tables/{id}

### Reservations

* POST /api/reservations
* GET /api/reservations
* GET /api/my-reservations
* PUT /api/reservations/{id}
* PUT /api/my-reservations/{id}
* DELETE /api/reservations/{id}

---

## Database Structure

The system is based on a relational database structure. The main tables include:

* users
* menus
* tables
* orders
* order_items
* reservations

Relationships between entities are implemented using Laravel Eloquent models, for example:

* A user can have multiple orders and reservations
* A table can have multiple reservations
* An order contains multiple order items

---

## Security

The system uses JWT authentication to secure API routes. Protected routes require a valid token, while admin routes are additionally restricted using role-based authorization.

Basic validation is implemented in the backend to prevent invalid data, including checks for phone numbers, reservation conflicts, and input formats.

---

## Performance

To improve performance, the application uses:

* Lazy loading (code splitting) in Vue Router
* Efficient API calls that return only necessary data
* Pagination in admin sections where needed

---

## Browser Compatibility

The application has been tested in:

* Google Chrome
* Microsoft Edge
* Mozilla Firefox
* Brave

---

## Responsive Design

The user interface is designed to be responsive and works on different screen sizes. Layouts, navigation, and components adjust properly for mobile, tablet, and desktop devices.

---

## Future Improvements

Some features that can be added in the future include:

* Payment integration
* Real-time notifications
* More advanced analytics in the dashboard
* Improved session and token management

---

## Conclusion

This project demonstrates the implementation of a complete full-stack web application using modern technologies. It covers both user and admin functionalities, includes security mechanisms, and provides a structured and scalable solution for managing restaurant operations.

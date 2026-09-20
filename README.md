# Farm Direct

**Farm Direct** is a web-based farm produce direct marketing system with price negotiation, developed as a final year project. It connects farmers and buyers directly, allowing them to negotiate prices in real time without intermediaries.

## Features

- **User registration and authentication** (Customer and Farmer roles), with token-based authentication via Laravel Sanctum
- **Admin-controlled farmer verification** — new farmer accounts start as "pending" and must be approved before they can list products
- **Product listing and browsing**, with category filtering and search
- **Price negotiation** — a structured offer/counter-offer mechanism between buyer and farmer, with every exchange recorded
- **Order management** — orders are created automatically once a negotiation is accepted
- **Secure payment processing** via the Paystack payment gateway, with server-side transaction verification
- **Role-specific dashboards** for customers and farmers

## Tech Stack

**Frontend:** React (TypeScript), Tailwind CSS, TanStack Router
**Backend:** PHP (Laravel 13), Laravel Sanctum
**Database:** MySQL
**Payment Gateway:** Paystack
**Deployment:** Vercel (frontend)

## Live Demo

- **Frontend:** [happy-farm-finds.vercel.app](https://happy-farm-finds.vercel.app)
- **Backend repository:** [farmdirect-backend](https://github.com/demiladebabalola/farmdirect-backend)
- **Frontend repository:** [happy-farm-finds](https://github.com/demiladebabalola/happy-farm-finds)

## Local Setup (Backend)

1. Clone this repository:
   ```
   git clone https://github.com/demiladebabalola/farmdirect-backend.git
   cd farmdirect-backend
   ```
2. Install PHP dependencies:
   ```
   composer install
   ```
3. Copy the example environment file and configure your database and Paystack keys:
   ```
   cp .env.example .env
   ```
4. Generate an application key:
   ```
   php artisan key:generate
   ```
5. Run migrations and seed the database with demo data:
   ```
   php artisan migrate --seed
   ```
6. Start the development server:
   ```
   php artisan serve
   ```

## Test Accounts (after seeding)

| Role | Email | Password |
|---|---|---|
| Customer | demilade@farmdirect.test | password |
| Farmer (Green Valley Farm) | greenvalley@farmdirect.test | password |
| Farmer (Berry Bliss Farm) | berrybliss@farmdirect.test | password |

## Author

Babalola Demilade Adesola
B.Sc. Software Engineering, Baze University
Supervised by Dr. Charles Saidu

## License

This project was developed for academic purposes as part of a final year project submission.
# Simple Email Campaign Manager

A Laravel-based email campaign management system built with Vue 3, Inertia.js, and Tailwind CSS. This application allows you to manage contacts, create email campaigns, and track delivery status efficiently.

## Features

- **Contact Management**
  - Create, edit, and delete contacts
  - Search and paginate contacts
  - View campaign history for each contact

- **Campaign Management**
  - Create and edit email campaigns
  - Select recipients from your contact list
  - Send campaigns with queue-based email delivery
  - Track delivery status (sent, pending, failed)

- **Modern UI**
  - Built with shadcn/ui components
  - Responsive design with Tailwind CSS
  - TypeScript support for better type safety

## Tech Stack

- **Backend**
  - Laravel 12 (PHP 8.4.15)
  - Laravel Fortify for authentication
  - Laravel Wayfinder for type-safe routing
  - Laravel Queues for background email processing
  - Pest for testing

- **Frontend**
  - Vue 3 with Composition API
  - TypeScript
  - Inertia.js 2
  - Tailwind CSS 4
  - shadcn/ui components

## Installation

### Prerequisites

- PHP 8.4.15 or higher
- Composer
- Node.js and npm
- MySQL or PostgreSQL database

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/hannanmiah/simple-campaign-manager.git
   cd simple-campaign-manager
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your database**
   Edit your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=simple_campaign_manager
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Queue configuration**
   Configure your queue connection in `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```

8. **Start the development servers**
   ```bash
   # Start the backend
   php artisan serve

   # Start the frontend (in a new terminal)
   npm run dev
   ```

## Usage

### Authentication

The application uses Laravel Fortify for authentication. Default authentication routes are configured:

- Login: `/login`
- Register: `/register`
- Logout: `/logout`

### Managing Contacts

1. **View Contacts**: Navigate to `/contacts` to see all contacts
2. **Add Contact**: Click "Add Contact" button or visit `/contacts/create`
3. **Edit Contact**: Click the dropdown menu on any contact and select "Edit contact"
4. **Delete Contact**: Use the dropdown menu to delete contacts (only if they have no associated campaigns)

### Managing Campaigns

1. **View Campaigns**: Navigate to `/campaigns` to see all campaigns
2. **Create Campaign**:
   - Click "New Campaign" button or visit `/campaigns/create`
   - Fill in subject and message body
   - Select recipients from your contact list
   - Click "Create Campaign" to save as draft
3. **Send Campaign**:
   - View a draft campaign
   - Click "Send Campaign" button
   - Confirm to send emails to all recipients
4. **Track Delivery**:
   - View campaign details to see delivery statistics
   - Individual email statuses are tracked (sent, pending, failed)

### Queue Processing

To process email sending jobs in the background:

```bash
# Process queue jobs
php artisan queue:work

# Or run queue worker in background
php artisan queue:work --daemon
```

## Architecture

### Backend Structure

- **Models**: `Contact`, `Campaign`, `EmailStatus`
- **Controllers**: RESTful controllers for each resource
- **Services**: Business logic separated into service classes
- **Jobs**: `SendCampaignEmail` for background email sending
- **Factories**: Database factories for testing and seeding

### Frontend Structure

- **Pages**: Vue components in `resources/js/Pages/`
- **Components**: Reusable UI components using shadcn/ui
- **Layouts**: AppLayout wrapper for consistent page structure
- **Routes**: Laravel Wayfinder for type-safe routing

### Database Schema

#### Contacts Table
- `id`: Primary key
- `name`: Contact name
- `email`: Email address
- `created_at`, `updated_at`: Timestamps

#### Campaigns Table
- `id`: Primary key
- `subject`: Email subject line
- `body`: Email content
- `status`: Campaign status (draft, sending, sent, failed)
- `sent_at`: Timestamp when sent
- `created_at`, `updated_at`: Timestamps

#### Email_Statuses Table
- `id`: Primary key
- `campaign_id`: Foreign key to campaigns
- `contact_id`: Foreign key to contacts
- `status`: Email status (pending, sent, failed)
- `sent_at`: Timestamp when sent
- `error_message`: Error details if failed
- `created_at`, `updated_at`: Timestamps

#### Campaign_Contact Table (Pivot)
- Links campaigns and contacts (many-to-many relationship)

## Testing

Run the test suite using Pest:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CampaignTest.php

# Run with coverage
php artisan test --coverage
```

## Configuration

### Email Configuration

Configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue Configuration

Choose your queue driver in `.env`:

```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

## Development

### Code Style

The project uses Laravel Pint for code formatting:

```bash
# Fix code style issues
vendor/bin/pint

# Check without fixing
vendor/bin/pint --test
```

### Type Safety

- Frontend uses TypeScript for type safety
- Laravel Wayfinder generates TypeScript types for routes
- All Vue components have proper prop typing

### Adding New Features

1. **Backend**: Follow Laravel conventions
   - Use form request classes for validation
   - Implement service layer for business logic
   - Write tests for new functionality

2. **Frontend**: Follow Vue 3 best practices
   - Use Composition API with TypeScript
   - Implement proper prop types
   - Use shadcn/ui components for consistency

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make your changes
4. Run tests: `php artisan test`
5. Fix code style: `vendor/bin/pint`
6. Commit your changes: `git commit -m 'Add feature'`
7. Push to the branch: `git push origin feature-name`
8. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For support, please open an issue in the GitHub repository or contact the maintainers.

---

**Built with ❤️ using Laravel, Vue 3, and Inertia.js**
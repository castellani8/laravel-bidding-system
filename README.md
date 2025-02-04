# Auction System

Auction System is a real-time auction platform built with Filament PHP and Laravel. Users can participate in active auctions by placing bids, viewing bid history, and managing their auction winnings. Administrators have full control over auctions, including bid approval and user management.

## Features

- **Real-time Bidding**: Users can place bids on active auctions.
- **Bid History**: Users can view their past bids and ongoing auctions.
- **Auction Payments**: Users can pay for the auctions they have won.
- **Admin Controls**: Administrators can approve or reject bids, start new auctions, and manage users.
- **Notifications**: Real-time updates using Reverb and Laravel Notifications.

## Technologies Used

- **Backend**: Laravel (Filament PHP)
- **Frontend**: Filament UI
- **Database**: MySQL
- **Real-time Notifications**: Reverb & Laravel Notifications
- **Deployment**: Virtiz

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/your-repo/auction-system.git
   cd auction-system
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Configure the environment:
   ```sh
   cp .env.example .env
   php artisan key:generate
   ```
4. Set up the database:
   ```sh
   php artisan migrate --seed
   ```
5. Start the application:
   ```sh
   php artisan serve
   ```

## Usage

- **Users**:
    - Place bids on active auctions.
    - View bid history and auction participation.
    - Pay for won auctions.

- **Administrators**:
    - Approve or reject bids.
    - Start and manage auctions.
    - Manage users.

## Deployment

This application is deployed using **Virtiz**.

## License

This project is licensed under the MIT License.

---

Feel free to contribute or suggest improvements!

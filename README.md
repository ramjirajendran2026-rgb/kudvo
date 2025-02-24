<img src="https://github.com/user-attachments/assets/b439b546-db22-45c0-905b-6fb0dfb095d4" alt="Kudvo" width="100"/>

# Kudvo

## Overview

Kudvo is an online platform dedicated to secure and transparent voting systems, inspired by the ancient **Kudavolai** method of democratic elections. It aims to provide a seamless digital voting experience for organizations, communities, and associations.

## Features

-   **Secure Voting System**: Ensures confidentiality and integrity of votes.
-   **User-Friendly Interface**: Simplified navigation for voters and administrators.
-   **Real-Time Results**: Instant tallying of votes with transparency.
-   **Role-Based Access**: Different levels of access for admins, voters, and organizers.
-   **Customizable Elections**: Configure elections based on specific requirements.

## Technology Stack

-   **Frontend**: Laravel Blade, Tailwind CSS, Filament, Livewire, Alphine Js
-   **Backend**: Laravel (PHP)
-   **Database**: MySQL
-   **Authentication**: Laravel Sanctum / JWT
-   **Hosting**: Deployed on cloud-based infrastructure

## Installation

To set up Kudvo locally:

1. **Clone the repository**
    ```sh
    git clone https://github.com/hr-inodesys/kudvo.git
    cd kudvo
    ```
2. **Install Dependencies**
    ```sh
    composer install  # Install Laravel dependencies
    npm install       # Install frontend dependencies
    ```
3. **Set Up Environment Variables**
    - Copy `.env.example` to `.env`
    - Configure database and authentication details
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```
4. **Migrate Database**
    ```sh
    php artisan migrate --seed
    ```
5. **Run the Application**
    ```sh
    php artisan serve
    npm run dev
    ```
    The application will be available at `http://127.0.0.1:8000`

## Usage

1. **Admin Dashboard**
    - Create and manage elections
    - Monitor voter participation
    - Generate reports
2. **Voter Panel**
    - Secure login to cast votes
    - View election details
    - Track election results

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature-name`)
3. Commit changes (`git commit -m 'Add new feature'`)
4. Push to the branch (`git push origin feature-name`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.

## Contact

For support or inquiries, reach out via:

-   Website: [Kudvo](https://kudvo.com)
-   Email: support@kudvo.com

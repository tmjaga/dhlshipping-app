# Create DHL Shipping demo application

Demo application to create DHL Shipping using DHL API

**Used packages and libraries:**

**Frontend:**
- Vue3
- Tailwind CSS
- PrimeVue (form elements)
- Vuelidate (for frontend Add Package form validation)
- PDF-Lib (to create pdf document)
- Vue Toastification (for pop-up messages)
- Axios

**Backend:**
- Laravel 10
- Guzzle HTTP Client

## Requirements
- PHP 8.1 with [composer](https://getcomposer.org/) installed
- [Node.js](https://nodejs.org) >= 19

## Installation

#### 1. Clone the project
```bash
git clone https://github.com/tmjaga/dhlshipping-app.git
```
#### 2. Navigate into the project folder using terminal
```bash
cd dhlshipping-app
```

#### 3. In the project folder run the following commands using terminal:
- Install the project dependencies
```bash
composer install
```
- Install NPM dependencies
```bash
npm install
```
- Compile the front end files
```bash
npm run build
```
## Usage

### With PHP Artisan
In the project folder run:
```bash
php artisan serve
```
Open http://127.0.0.1:8000 or http://localhost:8000 in your favorite browser.

### With Docker
In the project folder run:
```bash
./vendor/bin/sail up -d
```
>If you are using Windows, please make sure to install Docker Desktop.
>Next, you should ensure that Windows Subsystem for Linux 2 (WSL2) is installed and enabled.

Open http://127.0.0.1 or http://localhost in your favorite browser.



Fill the Shipping form and add a several Packages (max 3 Package allowed)

Press Create Shipping button.

In case of successful request execution, you will receive a Shipment Tracking Number and Shipping documents avaliable for dowload in PDF fomat.


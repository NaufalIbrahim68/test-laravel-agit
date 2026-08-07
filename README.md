# Production Plan Balancing System

Laravel project for balancing production plans.

## Setup
1. `composer install`
2. `npm install`
3. Setting `.env` for database connection
4. `php artisan migrate`
5. `npm run dev`
6. `php artisan serve`


## API Endpoints 

1.  POST /api/planning
Membuat planning baru

Contoh request:

{
  "requestCode": "REQ-001",
  "candidateToken": "VEH-123",
  "slots": [
    { "quantity": 10 },
    { "quantity": 20 }
  ]
}

2. GET /api/planning
Menampilkan list planning

Query:

?page=1&per_page=10

3. GET /api/planning/{id}
Menampilkan detail planning berdasarkan ID


## Testing
`php artisan test`


## References

Referensi dokumentasi yang digunakan:

1. Laravel Documentation
2. Laravel Validation
3. Laravel Database Transactions
4. Laravel Eloquent Relationships
5. PHP Documentation

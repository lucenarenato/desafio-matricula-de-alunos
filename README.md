<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



## About 

app/
├── Repositories/     
├── Services/         
├── Managers/         
├── Adapters/         
├── Observers/        
└── Providers/        

# Todos os testes
./vendor/bin/sail artisan test

# Com relatório detalhado
./vendor/bin/sail artisan test --testdox

# Específico
./vendor/bin/sail artisan test tests/Feature/AuthenticationTest.php

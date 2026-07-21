<?php

declare(strict_types=1);

// Auth: rutas personalizadas, bloqueos de wp-login.php / wp-admin y filtros de URL
(new \App\Domain\Auth\Http\Routes\AuthRoutes())->register();


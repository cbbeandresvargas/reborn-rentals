# Resumen de Migraciones - Reborn Rentals

## Migraciones Consolidadas

### Tablas Base de Laravel
1. **0001_01_01_000000_create_users_table.php**
   - Tabla `users` con todos los campos incluidos:
     - Campos básicos: id, name, email, password
     - Campos adicionales: last_name, second_last_name, phone_number, address, username
     - **Campo `role`**: enum('user', 'admin') con default 'user' ✅
   - También crea: password_reset_tokens, sessions

2. **0001_01_01_000001_create_cache_table.php**
   - Tabla de cache de Laravel

3. **0001_01_01_000002_create_jobs_table.php**
   - Tablas de queue/jobs de Laravel (jobs, job_batches, failed_jobs)

### Tablas de Aplicación
4. **2025_10_27_175221_create_categories_table.php**
   - Tabla `categories`

5. **2025_10_27_175227_create_job_locations_table.php**
   - Tabla `job_locations` (ubicaciones de trabajo)

6. **2025_10_27_175229_create_cupons_table.php**
   - Tabla `cupons`

7. **2025_10_27_175231_create_products_table.php**
   - Tabla `products`

8. **2025_10_27_175233_create_orders_table.php**
   - Tabla `orders`

9. **2025_10_27_175236_create_order_items_table.php**
   - Tabla `order_items`

10. **2025_10_27_175241_create_contacts_table.php**
    - Tabla `contacts`

11. **2025_10_27_175243_create_payment_infos_table.php**
    - Tabla `payment_infos`

12. **2025_10_27_175245_add_fields_to_users_table.php**
    - Migración de compatibilidad para bases de datos existentes
    - Migra de `is_admin` a `role` si es necesario

## Para usar con migrate fresh

```bash
php artisan migrate:fresh --seed
```

Esto:
1. Eliminará todas las tablas
2. Ejecutará todas las migraciones en orden
3. Ejecutará los seeders (UserSeeder, CategorySeeder, ProductSeeder)

## Estructura de Roles

- Campo `role` en tabla `users`: enum('user', 'admin')
- Default: 'user'
- Modelo User tiene métodos:
  - `isAdmin()`: retorna bool
  - `isUser()`: retorna bool

## Seeders

1. **UserSeeder**: Crea admin y usuario de prueba
2. **CategorySeeder**: Crea categorías
3. **ProductSeeder**: Crea productos (requiere categorías)


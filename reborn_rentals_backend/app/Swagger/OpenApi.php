<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="API – Proyecto X",
 *   description="Documentación de la API con autenticación JWT (Bearer).",
 *   @OA\Contact(email="soporte@tudominio.com")
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Servidor base"
 * )
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   description="Envia el token como: Bearer {tu_token_jwt}"
 * )
 */
class OpenApi
{
    // Clase vacía: solo sirve para alojar las anotaciones.
}
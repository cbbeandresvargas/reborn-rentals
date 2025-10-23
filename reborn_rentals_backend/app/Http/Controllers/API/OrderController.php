// config/l5-swagger.php (fragmento)
'securityDefinitions' => [
'securitySchemes' => [
'bearerAuth' => [
'type' => 'apiKey',
'description' => 'JWT: use "Bearer {token}"',
'name' => 'Authorization',
'in' => 'header',
],
],
'security' => [ ['bearerAuth' => []] ],
],
<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Task & Project Tracker API",
 * description="Dokumentasi API untuk Energeek Fullstack Test",
 * @OA\Contact(
 * email="admin@energeek.id"
 * )
 * )
 *
 * @OA\Server(
 * url="http://localhost:8000",
 * description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
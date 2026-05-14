<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Meeting Room Booking API",
 *     version="1.0.0",
 *     description="Microservice for booking meeting rooms. No authentication required — pass user_id in request body/query.",
 *     @OA\Contact(email="vachagan.nahapetyan.job@gmail.com")
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\Schema(
 *     schema="Room",
 *     type="object",
 *     @OA\Property(property="id",       type="integer", example=1),
 *     @OA\Property(property="name",     type="string",  example="Alpha"),
 *     @OA\Property(property="location", type="string",  example="Floor 1, Room 101"),
 *     @OA\Property(property="capacity", type="integer", example=6)
 * )
 *
 * @OA\Schema(
 *     schema="Booking",
 *     type="object",
 *     @OA\Property(property="id",               type="integer", example=7),
 *     @OA\Property(property="user_id",           type="integer", example=42),
 *     @OA\Property(property="title",             type="string",  example="Sprint Planning"),
 *     @OA\Property(property="room",              ref="#/components/schemas/Room"),
 *     @OA\Property(property="starts_at",         type="string",  format="datetime", example="2025-06-10 10:00:00"),
 *     @OA\Property(property="ends_at",           type="string",  format="datetime", example="2025-06-10 11:00:00"),
 *     @OA\Property(property="duration_minutes",  type="integer", example=60),
 *     @OA\Property(property="created_at",        type="string",  format="datetime", example="2025-05-11 09:00:00")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Validation error."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
 *     )
 * )
 */
class SwaggerAnnotations
{
}
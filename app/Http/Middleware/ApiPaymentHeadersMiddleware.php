<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiPaymentHeadersMiddleware
{
    /**
     * HTTP response codes and messages
     */
    private const HTTP_RESPONSES = [
        'INVALID_CONTENT_TYPE' => [
            'name' => 'Bad Request',
            'message' => 'Content-Type must be application/json',
            'code' => 400,
            'status' => 400
        ],
        'INVALID_AUTH' => [
            'name' => 'Unauthorized',
            // 'message' => 'Invalid or missing Authorization header',
            'message' => 'Your request was made with invalid credentials',
            'code' => 0,
            'status' => 401
        ],
        'INVALID_CREDENTIALS' => [
            'name' => 'Unauthorized',
            // 'message' => 'Invalid username or password',
            'message' => 'Your request was made with invalid credentials',
            'code' => 0,
            'status' => 401
        ],
        'INVALID_TIMESTAMP' => [
            'name' => 'Bad Request',
            'message' => 'Your request was made with invalid credentials',
            // 'message' => 'Invalid X-Timestamp format. Use YYYY-MM-DD H24:II:SS',
            'code' => 0,
            'status' => 400
        ],
        'MISSING_SIGNATURE' => [
            'name' => 'Unauthorized',
            // 'message' => 'X-Signature header is required',
            'message' => 'Your request was made with invalid credentials',
            'code' => 0,
            'status' => 401
        ],
        'INVALID_SIGNATURE' => [
            'name' => 'Unauthorized',
            // 'message' => 'Invalid X-Signature',
            'message' => 'Your request was made with invalid credentials',
            'code' => 0,
            'status' => 401
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Validate Content-Type
            if (!$this->validateContentType($request)) {
                return $this->errorResponse('INVALID_CONTENT_TYPE');
            }

            // Validate and extract Basic Auth credentials
            $credentials = $this->validateAndExtractAuth($request);
            if (!$credentials) {
                return $this->errorResponse('INVALID_AUTH');
            }

            // Validate user credentials
            $user = $this->validateUser($credentials);
            if (!$user) {
                return $this->errorResponse('INVALID_CREDENTIALS');
            }

            // Validate timestamp
            if (!$this->validateTimestamp($request)) {
                return $this->errorResponse('INVALID_TIMESTAMP');
            }

            // Validate signature
            if (!$this->validateSignature($request, $credentials, $user)['status']) {
                return $this->errorResponse('INVALID_SIGNATURE');
            }

            // Add validated user to request for later use if needed
            $request->attributes->add(['validated_user' => $user]);

            return $next($request);
        } catch (\Exception $e) {
            // Log the error here if needed
            logger()->error('API Payment Headers Middleware Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'name' => 'Internal Server Error',
                'message' => 'An unexpected error occurred',
                'code' => 500,
                'status' => 500
            ], 500);
        }
    }

    /**
     * Validate Content-Type header
     */
    private function validateContentType(Request $request): bool
    {
        return $request->header('Content-Type') === 'application/json';
    }

    /**
     * Validate and extract Basic Auth credentials
     */
    private function validateAndExtractAuth(Request $request): ?array
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            return null;
        }

        try {
            $credentials = base64_decode(substr($authHeader, 6), true);
            if ($credentials === false) {
                return null;
            }

            $parts = explode(':', $credentials, 2);
            if (count($parts) !== 2) {
                return null;
            }

            return [
                'username' => $parts[0],
                'password' => $parts[1]
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate user credentials
     */
    private function validateUser(array $credentials): ?Pengguna
    {
        $user = Pengguna::where('nik', $credentials['username'])->first();

        if (!$user || $user->sandi !== md5($credentials['password'])) {
            return null;
        }

        return $user;
    }

    /**
     * Validate timestamp format
     */
    private function validateTimestamp(Request $request): bool
    {
        $timestamp = $request->header('X-Timestamp');
        if (!$timestamp) {
            return false;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $timestamp) === 1;
    }

    /**
     * Validate signature
     */
    private function validateSignature(Request $request, array $credentials, Pengguna $user): array
    {
        $xSignature = $request->header('X-Signature');
        if (!$xSignature) {
            return false;
        }

        $kodePayment = $request->input('kode_pembayaran');
        if (!$kodePayment) {
            return false;
        }

        $signatureData = "{$credentials['username']}.{$request->header('X-Timestamp')}.{$kodePayment}";



        $expectedSignature = base64_encode(
            hash_hmac('sha256', $signatureData, $credentials['password'], true)
        );

        return
            [
                'status' => hash_equals($xSignature, $expectedSignature),
                'expected' => $expectedSignature,
                'actual' => $xSignature,
                'data' => $signatureData
            ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(string $errorKey): Response
    {
        $error = self::HTTP_RESPONSES[$errorKey];
        return response()->json($error, $error['status']);
    }
}

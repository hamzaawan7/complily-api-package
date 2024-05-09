<?php

namespace CommunicationMarketplaces\UnifiedApiIntegrations\Services;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/**
 * Class UnifiedApiService
 * @package CommunicationMarketplaces\UnifiedApiIntegrations\Services
 */
class UnifiedApiService
{
    /**
     * @var mixed
     */
    private $service;

    /**
     * @var Repository|Application|mixed
     */
    private $config;

    /**
     * @var array|mixed|null
     */
    private $accessToken;

    /**
     * @var array|mixed|null
     */
    private $refreshToken;

    /**
     * @param $service
     */
    public function __construct($service)
    {
        $this->service = $service;
        $this->config = config("integrations.{$service}");
        $this->accessToken = $this->config['access_token'] ?? null;
        $this->refreshToken = $this->config['refresh_token'] ?? null;
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl(): string
    {
        return $this->config['auth_url'] . "?response_type=code&client_id=" . $this->config['client_id'] . "&redirect_uri=" . $this->config['redirect_uri'];
    }

    /**
     * @param $user
     * @return bool
     */
    public function checkAccess($user): bool
    {
        $apiMe = $this->apiGetMe();

        if (!$apiMe[0]) {
            $response = $this->refreshAccessToken($this->refreshToken);

            if ($response[0]) {
                $this->accessToken = $response[1];
                $this->refreshToken = $response[2];

                // Update user tokens for this service
                $user->{$this->service . 'Integration'}()->updateOrInsert([
                    'user_id' => $user->id,
                ], [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * @param $refreshToken
     * @return array
     */
    public function refreshAccessToken($refreshToken): array
    {
        $authorization = base64_encode($this->config['client_id'] . ":" . $this->config['client_secret']);

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$authorization}"
        ])->post($this->config['token_url'], [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->successful()) {
            return [true, $response['access_token'], $response['refresh_token']];
        } else {
            return [false, $response->body()];
        }
    }

    /**
     * @param $code
     * @return array
     */
    public function requestAccessToken($code): array
    {
        $authorization = base64_encode($this->config['client_id'] . ":" . $this->config['client_secret']);

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$authorization}"
        ])->post($this->config['token_url'], [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config['redirect_uri'],
        ]);

        if ($response->successful()) {
            return [true, $response['access_token'], $response['refresh_token']];
        } else {
            return [false, $response->body()];
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public function revokeAccessToken($token): bool
    {
        $authorization = base64_encode($this->config['client_id'] . ":" . $this->config['client_secret']);

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$authorization}"
        ])->post($this->config['revoke_url'], [
            'token' => $token
        ]);

        return $response->successful();
    }

    /**
     * @return array
     */
    public function apiGetMe(): array
    {
        $authorization = "Bearer {$this->accessToken}";

        $response = Http::withHeaders([
            'Authorization' => $authorization
        ])->get($this->config['get_me_url']);

        if ($response->successful()) {
            return [true, $response->json()];
        } else {
            return [false, $response->body()];
        }
    }

    /**
     * @param $userId
     * @return array
     */
    public function apiGetRecordings($userId): array
    {
        $authorization = "Bearer {$this->accessToken}";

        $response = Http::withHeaders([
            'Authorization' => $authorization
        ])->get($this->config['recordings_url'] . '/' . $userId . '/recordings', [
            'page_size' => 300,
            'from' => Carbon::now()->subDays(30)->format('Y-m-d'),
            'to' => Carbon::now()->format('Y-m-d'),
        ]);

        if ($response->successful()) {
            return [true, $response->json()];
        } else {
            return [false, $response->body()];
        }
    }
}

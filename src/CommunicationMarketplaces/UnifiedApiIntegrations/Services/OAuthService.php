<?php

namespace CommunicationMarketplaces\UnifiedApiIntegrations\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OAuthService
{
    private  $authUrl;
    private  $tokenUrl;
    private  $revokeUrl;
    private  $clientId;
    private  $clientSecret;
    private  $redirectUri;
    private  $accessToken = null;
    private  $refreshToken = null;
    private  $authorizationCode = null;

    /**
     * OAuthService constructor
     *
     * @param string $authUrl
     * @param string $tokenUrl
     * @param string $revokeUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct(
        string $authUrl,
        string $tokenUrl,
        string $revokeUrl,
        string $clientId,
        string $clientSecret,
        string $redirectUri
    ) {
        $this->authUrl = $authUrl;
        $this->tokenUrl = $tokenUrl;
        $this->revokeUrl = $revokeUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param string $authorizationCode
     * @return void
     */
    public function setAuthorizationCode(string $authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;
    }

    /**
     * Get Authorization URL for OAuth
     *
     * @param string $responseType
     * @return string
     */
    public function getAuthorizationUrl(string $responseType = 'code'): string
    {
        return "{$this->authUrl}?response_type={$responseType}&client_id={$this->clientId}&redirect_uri={$this->redirectUri}";
    }

    /**
     * @return array|mixed|null
     */
    public function requestAccessToken()
    {
        if (!$this->authorizationCode) {
            Log::error('Authorization code not set.');
            return null;
        }

        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $this->authorizationCode,
            'redirect_uri' => $this->redirectUri
        ];

        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$credentials}",
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($this->tokenUrl, $postData);

        if ($response->successful()) {
            $tokenData = $response->json();
            $this->setAccessToken($tokenData['access_token']);
            $this->setRefreshToken($tokenData['refresh_token']);
            return $tokenData;
        } else {
            Log::error('Access token request failed: ' . $response->body());
            return null;
        }
    }

    /**
     * @return array|false|mixed
     */
    public function refreshAccessToken()
    {
        if (!$this->refreshToken) {
            Log::error('Refresh token not set.');
            return false;
        }

        $postData = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken
        ];

        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$credentials}",
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($this->tokenUrl, $postData);

        if ($response->successful()) {
            $tokenData = $response->json();
            $this->setAccessToken($tokenData['access_token']);
            $this->setRefreshToken($tokenData['refresh_token']);
            return $tokenData;
        } else {
            Log::error('Refresh token request failed: ' . $response->body());
            return false;
        }
    }

    /**
     * Revoke Access Token
     *
     * @return bool
     */
    public function revokeAccessToken(): bool
    {
        if (!$this->accessToken) {
            Log::error('Access token not set.');
            return false;
        }

        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::asForm()->withHeaders([
            'Authorization' => "Basic {$credentials}",
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->post($this->revokeUrl, [
            'token' => $this->accessToken
        ]);

        if ($response->successful()) {
            return true;
        } else {
            Log::error('Revoke token request failed: ' . $response->body());
            return false;
        }
    }
}

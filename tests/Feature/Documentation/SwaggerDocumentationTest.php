<?php

namespace Tests\Feature\Documentation;

use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    public function test_swagger_docs_can_be_generated_for_all_application_controllers(): void
    {
        $docsPath = storage_path('api-docs/api-docs.json');

        if (file_exists($docsPath)) {
            unlink($docsPath);
        }

        $this->artisan('l5-swagger:generate')
            ->assertExitCode(0);

        $this->assertFileExists($docsPath);

        $documentation = json_decode((string) file_get_contents($docsPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Pickleball Backend API', $documentation['info']['title']);

        $expectedOperations = [
            '/api/auth/login' => ['post'],
            '/api/auth/logout' => ['post'],
            '/api/auth/user' => ['get'],
            '/api/players' => ['get'],
            '/api/game-types' => ['get'],
            '/api/game-types/{gameType}/formats' => ['get'],
            '/api/matches' => ['post'],
            '/api/matches/{match}/finish' => ['patch'],
            '/settings/profile' => ['get', 'patch', 'delete'],
            '/settings/security' => ['get'],
            '/settings/password' => ['put'],
        ];

        foreach ($expectedOperations as $path => $methods) {
            $this->assertArrayHasKey($path, $documentation['paths']);

            foreach ($methods as $method) {
                $this->assertArrayHasKey($method, $documentation['paths'][$path]);
            }
        }

        $this->assertArrayHasKey('ApiLoginRequest', $documentation['components']['schemas']);
        $this->assertArrayHasKey('UserData', $documentation['components']['schemas']);
        $this->assertArrayHasKey('PlayerListItemData', $documentation['components']['schemas']);
        $this->assertArrayHasKey('GameFormatData', $documentation['components']['schemas']);
        $this->assertArrayHasKey('GameTypeData', $documentation['components']['schemas']);
        $this->assertArrayHasKey('MatchData', $documentation['components']['schemas']);
        $this->assertArrayHasKey('SettingsProfileUpdateRequest', $documentation['components']['schemas']);
        $this->assertArrayHasKey('SettingsProfileDeleteRequest', $documentation['components']['schemas']);
        $this->assertArrayHasKey('SettingsPasswordUpdateRequest', $documentation['components']['schemas']);
    }

    public function test_swagger_ui_and_json_routes_are_available(): void
    {
        $this->artisan('l5-swagger:generate')
            ->assertExitCode(0);

        $this->get(route('l5-swagger.default.api'))
            ->assertOk()
            ->assertSee(route('l5-swagger.default.docs', [], false), false);

        $this->get(route('l5-swagger.default.docs'))
            ->assertOk()
            ->assertJsonPath('paths./api/auth/login.post.operationId', 'apiAuthLogin')
            ->assertJsonPath('paths./api/players.get.operationId', 'apiPlayersIndex')
            ->assertJsonPath('paths./api/game-types/{gameType}/formats.get.operationId', 'apiGameTypesFormatsIndex')
            ->assertJsonPath('paths./settings/password.put.operationId', 'settingsSecurityUpdatePassword');
    }
}

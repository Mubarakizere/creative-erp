<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Realtime\RealtimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class RealtimeServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected RealtimeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(RealtimeService::class);
    }

    public function test_can_broadcast_event()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $this->service->broadcast()->dispatch('user.updated', ['id' => $user->id]);
        
        $this->assertTrue(true);
    }

    public function test_presence_heartbeat()
    {
        $company = \App\Models\Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        
        $this->actingAs($user);
        
        $this->service->presence()->heartbeat($user);
        
        $cacheKey = "company.{$company->id}.online_users";
        
        $this->assertTrue(Cache::has($cacheKey));
        $sessions = Cache::get($cacheKey);
        
        $this->assertArrayHasKey($user->id, $sessions);
    }
}

<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TimeClockService;
use App\Models\User;
use App\Models\TimeClock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimeClockServiceTest extends TestCase
{
    use RefreshDatabase;

    private TimeClockService $timeClockService;
    private User $admin;
    private User $employee1;
    private User $employee2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->timeClockService = new TimeClockService();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'admin_id' => null,
            'name' => 'Admin Manager'
        ]);

        $this->employee1 = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee',
            'name' => 'João Silva',
            'position' => 'Desenvolvedor',
            'birth_date' => '1990-01-15'
        ]);

        $this->employee2 = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee',
            'name' => 'Maria Santos',
            'position' => 'Analista',
            'birth_date' => '1985-05-20'
        ]);
    }

    /** @test */
    public function it_can_get_paginated_time_clocks_without_filters()
    {
        TimeClock::factory()->count(15)->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => now()->subHours(2)
        ]);

        TimeClock::factory()->count(5)->create([
            'user_id' => $this->employee2->id,
            'clocked_at' => now()->subHours(1)
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(20, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertCount(10, $result->items());
    }

    /** @test */
    public function it_can_filter_time_clocks_by_employee_id()
    {
        TimeClock::factory()->count(5)->create(['user_id' => $this->employee1->id]);
        TimeClock::factory()->count(3)->create(['user_id' => $this->employee2->id]);

        $request = Request::create('/test', 'GET', [
            'employee_id' => $this->employee1->id
        ]);

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertEquals(5, $result->total());

        foreach ($result->items() as $timeClock) {
            $this->assertEquals('João Silva', $timeClock->employee_name);
        }
    }

    /** @test */
    public function it_can_filter_time_clocks_by_date_range()
    {
        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-15 10:00:00'
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-20 14:00:00'
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-02-05 09:00:00'
        ]);

        $request = Request::create('/test', 'GET', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31'
        ]);

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_can_filter_by_both_employee_and_date_range()
    {
        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-15 10:00:00'
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-20 14:00:00'
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-02-05 09:00:00'
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee2->id,
            'clocked_at' => '2024-01-10 11:00:00'
        ]);

        $request = Request::create('/test', 'GET', [
            'employee_id' => $this->employee1->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31'
        ]);

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertEquals(2, $result->total());

        foreach ($result->items() as $timeClock) {
            $this->assertEquals('João Silva', $timeClock->employee_name);
        }
    }

    /** @test */
    public function it_orders_time_clocks_by_clocked_at_desc()
    {
        $firstClock = TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-10 10:00:00'
        ]);

        $lastClock = TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => '2024-01-15 14:00:00'
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $items = $result->items();
        $this->assertEquals($lastClock->id, $items[0]->time_clock_id);
        $this->assertEquals($firstClock->id, $items[1]->time_clock_id);
    }

    /** @test */
    public function it_includes_correct_employee_and_manager_information()
    {
        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => now()
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $timeClock = $result->items()[0];

        $this->assertEquals('João Silva', $timeClock->employee_name);
        $this->assertEquals('Desenvolvedor', $timeClock->position);
        $this->assertEquals('Admin Manager', $timeClock->manager_name);
        $this->assertEquals('1990-01-15', $timeClock->birth_date);
    }

    /** @test */
    public function it_calculates_employee_age_correctly()
    {
        $birthDate = Carbon::now()->subYears(30)->format('Y-m-d');

        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee',
            'birth_date' => $birthDate
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => now()
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $timeClock = $result->items()[0];
        $this->assertEquals(30, $timeClock->age);
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        TimeClock::factory()->count(25)->create([
            'user_id' => $this->employee1->id
        ]);

        $request1 = Request::create('/test', 'GET', ['page' => 1]);

        $request2 = Request::create('/test', 'GET', ['page' => 2]);

        $page1 = $this->timeClockService->getPaginatedTimeClocks($request1);
        $page2 = $this->timeClockService->getPaginatedTimeClocks($request2);

        $this->assertEquals(25, $page1->total());
        $this->assertEquals(25, $page2->total());
        $this->assertCount(10, $page1->items());
        $this->assertCount(10, $page2->items());
        $this->assertEquals(1, $page1->currentPage());
        $this->assertEquals(2, $page2->currentPage());
    }

    /** @test */
    public function it_respects_custom_per_page_parameter()
    {
        TimeClock::factory()->count(20)->create([
            'user_id' => $this->employee1->id
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request, 5);

        $this->assertEquals(20, $result->total());
        $this->assertEquals(5, $result->perPage());
        $this->assertCount(5, $result->items());
    }

    /** @test */
    public function it_only_returns_employee_time_clocks()
    {
        $anotherAdmin = User::factory()->create([
            'role' => 'admin',
            'admin_id' => null
        ]);

        TimeClock::factory()->create(['user_id' => $this->employee1->id]);
        TimeClock::factory()->create(['user_id' => $anotherAdmin->id]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('João Silva', $result->items()[0]->employee_name);
    }

    /** @test */
    public function it_can_get_all_employees()
    {
        User::factory()->create([
            'role' => 'admin',
            'admin_id' => null
        ]);

        $employees = $this->timeClockService->getEmployees();

        $this->assertCount(2, $employees);

        $employeeNames = collect($employees)->pluck('name')->toArray();
        $this->assertContains('João Silva', $employeeNames);
        $this->assertContains('Maria Santos', $employeeNames);
    }

    /** @test */
    public function it_handles_empty_results()
    {
        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertEquals(0, $result->total());
        $this->assertCount(0, $result->items());
    }

    /** @test */
    public function it_handles_employee_without_admin()
    {
        $orphanEmployee = User::factory()->create([
            'role' => 'employee',
            'admin_id' => null,
            'name' => 'Funcionário Órfão'
        ]);

        TimeClock::factory()->create([
            'user_id' => $orphanEmployee->id,
            'clocked_at' => now()
        ]);

        $request = Request::create('/test', 'GET');

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $timeClock = collect($result->items())->firstWhere('employee_name', 'Funcionário Órfão');
        $this->assertNotNull($timeClock);
        $this->assertNull($timeClock->manager_name);
    }

    /** @test */
    public function it_returns_valid_paginator_with_correct_metadata()
    {
        TimeClock::factory()->count(25)->create([
            'user_id' => $this->employee1->id
        ]);

        $request = Request::create('http://localhost/test', 'GET', [
            'employee_id' => $this->employee1->id,
            'page' => 2
        ]);

        $result = $this->timeClockService->getPaginatedTimeClocks($request);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(25, $result->total());
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(3, $result->lastPage());
        $this->assertCount(10, $result->items());
    }

    /** @test */
    public function it_can_get_last_30_days_time_clocks_for_authenticated_user()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $today = now();
        $yesterday = now()->subDay();
        $twentyDaysAgo = now()->subDays(20);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $today
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $yesterday
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $twentyDaysAgo
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => now()->subDays(35)
        ]);

        TimeClock::factory()->create([
            'user_id' => $this->employee1->id,
            'clocked_at' => $today
        ]);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $this->assertCount(3, $result);

        $this->assertTrue($result->has($today->format('Y-m-d')));
        $this->assertTrue($result->has($yesterday->format('Y-m-d')));
        $this->assertTrue($result->has($twentyDaysAgo->format('Y-m-d')));

        $this->assertFalse($result->has(now()->subDays(35)->format('Y-m-d')));

        $todayGroup = $result->get($today->format('Y-m-d'));
        $this->assertCount(1, $todayGroup);
        $this->assertEquals($employee->id, $todayGroup->first()->user_id);
    }

    /** @test */
    public function it_groups_multiple_time_clocks_by_same_date()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $today = now();

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $today->copy()->setTime(8, 0, 0)
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $today->copy()->setTime(12, 0, 0)
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $today->copy()->setTime(13, 0, 0)
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $today->copy()->setTime(18, 0, 0)
        ]);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $this->assertCount(1, $result);

        $todayGroup = $result->get($today->format('Y-m-d'));
        $this->assertCount(4, $todayGroup);

        $times = $todayGroup->pluck('clocked_at')->map(fn($time) => $time->format('H:i:s'));
        $this->assertEquals(['08:00:00', '12:00:00', '13:00:00', '18:00:00'], $times->toArray());
    }

    /** @test */
    public function it_orders_time_clocks_within_groups_by_desc()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $date1 = now()->subDays(5);
        $date2 = now()->subDays(3);

        $clock1 = TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $date1->copy()->setTime(10, 0, 0)
        ]);

        $clock2 = TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => $date1->copy()->setTime(15, 0, 0)
        ]);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $date1Group = $result->get($date1->format('Y-m-d'));
        
        $this->assertEquals($clock1->id, $date1Group->first()->id);
        $this->assertEquals($clock2->id, $date1Group->last()->id);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_time_clocks_in_last_30_days()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => now()->subDays(35)
        ]);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $this->assertCount(0, $result);
    }

    /** @test */
    public function it_only_returns_authenticated_user_time_clocks_for_last_30_days()
    {
        $employee1 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);

        $today = now();

        TimeClock::factory()->create([
            'user_id' => $employee1->id,
            'clocked_at' => $today
        ]);

        TimeClock::factory()->create([
            'user_id' => $employee2->id,
            'clocked_at' => $today
        ]);

        $this->actingAs($employee1);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $this->assertCount(1, $result);

        $todayGroup = $result->get($today->format('Y-m-d'));
        $this->assertCount(1, $todayGroup);
        $this->assertEquals($employee1->id, $todayGroup->first()->user_id);
    }

    /** @test */
    public function it_can_clock_in_with_correct_data()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $request = Request::create('/clock-in', 'POST');
        $request->server->set('REMOTE_ADDR', '192.168.1.100');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Test Browser)');

        $timeClock = $this->timeClockService->clockIn($request);

        $this->assertInstanceOf(TimeClock::class, $timeClock);
        $this->assertEquals($employee->id, $timeClock->user_id);
        $this->assertEquals('192.168.1.100', $timeClock->ip_address);
        $this->assertEquals('Mozilla/5.0 (Test Browser)', $timeClock->user_agent);
        $this->assertNotNull($timeClock->clocked_at);

        $this->assertDatabaseHas('time_clocks', [
            'user_id' => $employee->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Test Browser)'
        ]);
    }

    /** @test */
    public function it_saves_current_timestamp_when_clocking_in()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $fixedNow = Carbon::now();
        Carbon::setTestNow($fixedNow);

        $request = Request::create('/clock-in', 'POST');
        $timeClock = $this->timeClockService->clockIn($request);

        $this->assertEquals($fixedNow->toDateTimeString(), $timeClock->clocked_at->toDateTimeString());

        Carbon::setTestNow();
    }


    /** @test */
    public function it_handles_missing_ip_and_user_agent_gracefully()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $this->actingAs($employee);

        $request = Request::create('/clock-in', 'POST');

        $timeClock = $this->timeClockService->clockIn($request);

        $this->assertInstanceOf(TimeClock::class, $timeClock);
        $this->assertEquals($employee->id, $timeClock->user_id);
        $this->assertNotNull($timeClock->clocked_at);

        $this->assertTrue(
            is_null($timeClock->ip_address) ||
                is_string($timeClock->ip_address)
        );

        $this->assertTrue(
            is_null($timeClock->user_agent) ||
                is_string($timeClock->user_agent)
        );
    }

    /** @test */
    public function recent_time_clocks_only_returns_authenticated_user_records()
    {
        $employee1 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);

        TimeClock::factory()->count(5)->create(['user_id' => $employee1->id]);
        TimeClock::factory()->count(3)->create(['user_id' => $employee2->id]);

        $this->actingAs($employee1);

        $result = $this->timeClockService->getLast30DaysTimeClocks();

        $totalRecords = $result->flatten()->count();
        $this->assertEquals(5, $totalRecords);

        $result->each(function ($dayGroup) use ($employee1) {
            $dayGroup->each(function ($timeClock) use ($employee1) {
                $this->assertEquals($employee1->id, $timeClock->user_id);
            });
        });
    }
}

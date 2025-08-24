<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmployeeService;
use App\Models\User;
use App\Models\TimeClock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeService $employeeService;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->employeeService = new EmployeeService();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'admin_id' => null
        ]);
        
        $this->actingAs($this->admin);
    }

    /** @test */
    public function debug_password_generation()
    {
        $cpf = '987.654.321-09';
        $cleanCpf = preg_replace('/\D/', '', $cpf);
        $expectedPassword = substr($cleanCpf, -6);
        
        $employeeData = [
            'name' => 'Debug User',
            'email' => 'debug@teste.com',
            'cpf' => $cpf,
            'position' => 'Debug',
            'birth_date' => '1990-01-01',
            'zip_code' => '12345-678',
            'address' => 'Debug',
            'number' => '123',
            'complement' => '',
            'neighborhood' => 'Debug',
            'city' => 'Debug',
            'state' => 'SP',
        ];

        $employee = $this->employeeService->create($employeeData);

        $this->assertTrue(
            Hash::check($expectedPassword, $employee->password),
            "Falha na verificação da senha. Esperado: $expectedPassword"
        );
    }
    public function it_can_get_dashboard_stats()
    {
        $employee1 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        TimeClock::factory()->create([
            'user_id' => $employee1->id,
            'clocked_at' => now()
        ]);
        
        TimeClock::factory()->create([
            'user_id' => $employee2->id,
            'clocked_at' => now()
        ]);
        
        TimeClock::factory()->create([
            'user_id' => $employee1->id,
            'clocked_at' => now()->subDay()
        ]);

        $stats = $this->employeeService->getDashboardStats();

        $this->assertEquals(2, $stats['totalEmployees']);
        $this->assertEquals(2, $stats['todayClocks']);
    }

    /** @test */
    public function it_can_get_paginated_employees()
    {
        User::factory()->count(15)->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $paginatedEmployees = $this->employeeService->getPaginatedEmployees();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedEmployees);
        $this->assertEquals(10, $paginatedEmployees->perPage());
        $this->assertEquals(15, $paginatedEmployees->total());
    }

    /** @test */
    public function it_can_create_employee_with_correct_data()
    {
        $employeeData = [
            'name' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '123.456.789-01',
            'position' => 'Desenvolvedor',
            'birth_date' => '1990-01-01',
            'zip_code' => '12345-678',
            'address' => 'Rua das Flores',
            'number' => '123',
            'complement' => 'Apto 1',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
        ];

        $employee = $this->employeeService->create($employeeData);

        $this->assertInstanceOf(User::class, $employee);
        $this->assertEquals('João Silva', $employee->name);
        $this->assertEquals('joao@exemplo.com', $employee->email);
        $this->assertEquals('12345678901', $employee->cpf);
        $this->assertEquals('12345678', $employee->zip_code);
        $this->assertEquals('employee', $employee->role);
        $this->assertEquals($this->admin->id, $employee->admin_id);
        
        $this->assertTrue(Hash::check('678901', $employee->password));
    }

    /** @test */
    public function it_cleans_cpf_and_zip_code_when_creating_employee()
    {
        $employeeData = [
            'name' => 'Maria Santos',
            'email' => 'maria@exemplo.com',
            'cpf' => '987.654.321-09',
            'position' => 'Analista',
            'birth_date' => '1985-05-15',
            'zip_code' => '98765-432',
            'address' => 'Av. Principal',
            'number' => '456',
            'complement' => '',
            'neighborhood' => 'Vila Nova',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
        ];

        $employee = $this->employeeService->create($employeeData);

        $this->assertEquals('98765432109', $employee->cpf);
        $this->assertEquals('98765432', $employee->zip_code);
        
        $expectedPassword = '432109';
        $this->assertTrue(
            Hash::check($expectedPassword, $employee->password),
            "Senha esperada: {$expectedPassword}, mas Hash::check falhou. CPF: {$employee->cpf}"
        );
    }

    /** @test */
    public function it_can_update_employee()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee'
        ]);

        $updateData = [
            'name' => 'Nome Atualizado',
            'email' => 'novo@email.com',
            'cpf' => '111.222.333-44',
            'position' => 'Nova Posição',
            'birth_date' => '1995-12-25',
            'zip_code' => '54321-987',
            'address' => 'Nova Rua',
            'number' => '789',
            'complement' => 'Casa',
            'neighborhood' => 'Novo Bairro',
            'city' => 'Nova Cidade',
            'state' => 'MG',
        ];

        $result = $this->employeeService->update($employee, $updateData);

        $this->assertTrue($result);
        
        $employee->refresh();
        $this->assertEquals('Nome Atualizado', $employee->name);
        $this->assertEquals('novo@email.com', $employee->email);
        $this->assertEquals('11122233344', $employee->cpf);
        $this->assertEquals('54321987', $employee->zip_code);
        $this->assertEquals('Nova Posição', $employee->position);
    }

    /** @test */
    public function it_can_reset_employee_password()
    {
        $employee = User::factory()->create([
            'admin_id' => $this->admin->id,
            'role' => 'employee',
            'cpf' => '12345678901',
            'password' => Hash::make('senha_antiga')
        ]);

        $newPassword = $this->employeeService->resetPassword($employee);

        $this->assertEquals('678901', $newPassword);
        
        $employee->refresh();
        $this->assertTrue(Hash::check('678901', $employee->password));
    }

    /** @test */
    public function it_can_get_filtered_time_clocks_without_filters()
    {
        $employee1 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        TimeClock::factory()->count(5)->create(['user_id' => $employee1->id]);
        TimeClock::factory()->count(3)->create(['user_id' => $employee2->id]);

        $result = $this->employeeService->getFilteredTimeClocks([]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(8, $result->total());
        $this->assertEquals(10, $result->perPage());
    }

    /** @test */
    public function it_can_filter_time_clocks_by_employee_id()
    {
        $employee1 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        TimeClock::factory()->count(3)->create(['user_id' => $employee1->id]);
        TimeClock::factory()->count(2)->create(['user_id' => $employee2->id]);

        $result = $this->employeeService->getFilteredTimeClocks([
            'employee_id' => $employee1->id
        ]);

        $this->assertEquals(3, $result->total());
        
        foreach ($result->items() as $timeClock) {
            $this->assertEquals($employee1->id, $timeClock->user_id);
        }
    }

    /** @test */
    public function it_can_filter_time_clocks_by_date_range()
    {
        $employee = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => '2024-01-15 10:00:00'
        ]);
        
        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => '2024-01-20 14:00:00'
        ]);
        
        TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => '2024-02-01 09:00:00'
        ]);

        $result = $this->employeeService->getFilteredTimeClocks([
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31'
        ]);

        $this->assertEquals(2, $result->total());
    }

    /** @test */
    public function it_orders_time_clocks_by_clocked_at_desc()
    {
        $employee = User::factory()->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        $firstClock = TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => '2024-01-10 10:00:00'
        ]);
        
        $lastClock = TimeClock::factory()->create([
            'user_id' => $employee->id,
            'clocked_at' => '2024-01-15 10:00:00'
        ]);

        $result = $this->employeeService->getFilteredTimeClocks([]);

        $items = $result->items();
        $this->assertEquals($lastClock->id, $items[0]->id);
        $this->assertEquals($firstClock->id, $items[1]->id);
    }

    /** @test */
    public function dashboard_stats_only_counts_employees_from_authenticated_admin()
    {
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        
        User::factory()->count(3)->create(['admin_id' => $this->admin->id, 'role' => 'employee']);
        
        User::factory()->count(2)->create(['admin_id' => $otherAdmin->id, 'role' => 'employee']);

        $stats = $this->employeeService->getDashboardStats();

        $this->assertEquals(3, $stats['totalEmployees']);
    }
}
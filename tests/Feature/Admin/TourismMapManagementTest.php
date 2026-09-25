<?php

namespace Tests\Feature\Admin;

use App\Models\TourismMap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourismMapManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_map_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.map.update'), [
            'map_title' => 'Peta Wisata Baru',
            'map_sub_title' => 'Panduan Interaktif',
            'map_description' => 'Deskripsi peta baru.',
        ])->assertRedirect();

        $this->assertDatabaseHas('tourism_maps', [
            'map_title' => 'Peta Wisata Baru',
            'map_sub_title' => 'Panduan Interaktif',
            'map_description' => 'Deskripsi peta baru.',
        ]);
    }

    public function test_operator_can_manage_map_settings(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $this->actingAs($operator)
            ->get(route('admin.map.edit'))
            ->assertOk();

        $this->actingAs($operator)->put(route('admin.map.update'), [
            'map_title' => 'Peta Operator',
            'map_sub_title' => 'Panduan Operator',
            'map_description' => 'Diperbarui oleh operator.',
        ])->assertRedirect();

        $this->assertDatabaseHas('tourism_maps', ['map_title' => 'Peta Operator']);
    }
}

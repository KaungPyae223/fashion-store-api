<?php

namespace Tests\Unit;

use App\Models\Color;
use App\Repositories\ColorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ColorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $colorRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock authentication
        Auth::shouldReceive('user')->andReturn((object) ['admin' => (object) ['id' => 1]]);

        $this->colorRepository = new ColorRepository();
    }

    public function test_create_color()
    {
        $data = ['color' => 'Red'];

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $color = $this->colorRepository->create($data);

        $this->assertInstanceOf(Color::class, $color);
        $this->assertEquals('Red', $color->color);
    }

    public function test_find_color()
    {
        $color = Color::factory()->create(['color' => 'Blue']);

        $foundColor = $this->colorRepository->find($color->id);

        $this->assertNotNull($foundColor);
        $this->assertEquals('Blue', $foundColor->color);
    }

    public function test_update_color()
    {
        $color = Color::factory()->create(['color' => 'Green']);

        $updateData = ['id' => $color->id, 'color' => 'Yellow'];

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $updatedColor = $this->colorRepository->update($updateData);

        $this->assertEquals('Yellow', $updatedColor->color);
    }

    public function test_delete_color()
    {
        $color = Color::factory()->create();

        $this->colorRepository->delete($color->id);

        $this->assertDatabaseMissing('colors', ['id' => $color->id]);
    }
}

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase; // Resetea la BD en cada prueba

    public function test_can_create_article()
    {
        $author = User::factory()->create();
        $category = Category::factory()->create();

        $payload = [
            'title' => 'Nueva Noticia de Multimedios',
            'slug' => 'nueva-noticia-multimedios',
            'content' => 'Contenido exclusivo de la noticia...',
            'author_id' => $author->id,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/v1/articles', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'title', 'slug']
                 ]);
                 
        $this->assertDatabaseHas('articles', ['slug' => 'nueva-noticia-multimedios']);
    }
}

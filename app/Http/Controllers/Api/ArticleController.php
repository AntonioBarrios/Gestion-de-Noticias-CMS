namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    public function index()
    {
        // Cachear las noticias más leídas por 1 hora (3600 segundos)
        $articles = Cache::remember('articles.popular', 3600, function () {
            return Article::with(['author', 'category'])
                ->where('is_published', true)
                ->orderBy('views', 'desc')
                ->take(10)
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $articles
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:articles',
            'content' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $article = Article::create($validatedData);

        // Limpiar el caché cuando se crea un nuevo artículo para reflejar cambios
        Cache::forget('articles.popular');

        return response()->json([
            'success' => true,
            'message' => 'Artículo creado correctamente',
            'data' => $article
        ], 201);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('admin.articles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail_file' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = (bool)($request->is_published ?? false);

        if ($request->hasFile('thumbnail_file')) {
            $validated['thumbnail'] = $this->uploadAndCompress($request->file('thumbnail_file'), 'articles');
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail_file' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = (bool)($request->is_published ?? false);

        if ($request->hasFile('thumbnail_file')) {
            // Delete old thumbnail if exists
            if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
                Storage::disk('public')->delete($article->thumbnail);
            }
            $validated['thumbnail'] = $this->uploadAndCompress($request->file('thumbnail_file'), 'articles');
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->thumbnail && Storage::disk('public')->exists($article->thumbnail)) {
            Storage::disk('public')->delete($article->thumbnail);
        }
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('image');
        
        // Check if file is image or video
        $mime = $file->getMimeType();
        if (Str::startsWith($mime, 'image/')) {
            $filename = 'articles/media/' . uniqid() . '.webp';
            $image = Image::read($file);
            $image->scale(width: 1000);
            $encoded = $image->toWebp(75);
            Storage::disk('public')->put($filename, (string) $encoded);
        } else {
            // Save video raw
            $filename = 'articles/media/' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($file));
        }

        return response()->json([
            'url' => Storage::url($filename)
        ]);
    }

    private function uploadAndCompress($file, string $folder): string
    {
        $filename = $folder . '/' . uniqid() . '.webp';
        $image = Image::read($file);
        $image->scale(width: 800);
        $encoded = $image->toWebp(75);
        Storage::disk('public')->put($filename, (string) $encoded);
        return $filename;
    }
}

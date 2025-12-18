<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    private const WORDPRESS_API_BASE = 'https://blog.grb-group.com/wp-json/wp/v2';

    public function about()
    {
        return view('about-us');
    }

    public function faq()
    {
        return view('faq');
    }

    public function fees()
    {
        return view('fees-surcharges');
    }

    public function privacy()
    {
        return view('privacy-policy');
    }

    public function sitemap()
    {
        return view('site-map');
    }

    public function terms()
    {
        return view('terms-conditions');
    }

    public function sms()
    {
        return view('sms-policy');
    }

    public function directions()
    {
        return view('directions');
    }

    public function blog(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = 9;
            
            $response = Http::timeout(10)->get(self::WORDPRESS_API_BASE . '/posts', [
                'page' => $page,
                'per_page' => $perPage,
                '_embed' => true, // Incluir datos embebidos como featured image, author, etc.
            ]);

            if ($response->successful()) {
                $posts = $response->json();
                $totalPages = (int) $response->header('X-WP-TotalPages', 1);
                $totalPosts = (int) $response->header('X-WP-Total', 0);
                
                // Procesar posts para formatear datos
                $processedPosts = array_map(function($post) {
                    return $this->formatPost($post);
                }, $posts);

                return view('blog', [
                    'posts' => $processedPosts,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'totalPosts' => $totalPosts,
                ]);
            }

            // Si falla la API, retornar vista vacía
            return view('blog', [
                'posts' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'totalPosts' => 0,
                'error' => 'No se pudieron cargar los posts del blog.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching blog posts: ' . $e->getMessage());
            
            return view('blog', [
                'posts' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'totalPosts' => 0,
                'error' => 'Error al cargar los posts del blog.',
            ]);
        }
    }

    public function showPost($slug)
    {
        try {
            // Intentar obtener por slug primero
            $response = Http::timeout(10)->get(self::WORDPRESS_API_BASE . '/posts', [
                'slug' => $slug,
                '_embed' => true,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $postData = $response->json()[0];
                $post = $this->formatPost($postData, true); // true para contenido completo
                
                // Obtener posts relacionados (misma categoría o tags)
                $relatedPosts = $this->getRelatedPosts($post);
                
                return view('post', [
                    'post' => $post,
                    'relatedPosts' => $relatedPosts,
                ]);
            }

            // Si no se encuentra por slug, intentar por ID
            if (is_numeric($slug)) {
                $response = Http::timeout(10)->get(self::WORDPRESS_API_BASE . '/posts/' . $slug, [
                    '_embed' => true,
                ]);

                if ($response->successful()) {
                    $postData = $response->json();
                    $post = $this->formatPost($postData, true);
                    $relatedPosts = $this->getRelatedPosts($post);
                    
                    return view('post', [
                        'post' => $post,
                        'relatedPosts' => $relatedPosts,
                    ]);
                }
            }

            abort(404, 'Post no encontrado');
        } catch (\Exception $e) {
            \Log::error('Error fetching blog post: ' . $e->getMessage());
            abort(500, 'Error al cargar el post');
        }
    }

    private function formatPost($postData, $fullContent = false)
    {
        $featuredImage = null;
        if (isset($postData['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $featuredImage = $postData['_embedded']['wp:featuredmedia'][0]['source_url'];
        }

        $author = null;
        if (isset($postData['_embedded']['author'][0])) {
            $author = $postData['_embedded']['author'][0]['name'];
        }

        $categories = [];
        if (isset($postData['_embedded']['wp:term'][0])) {
            $categories = array_map(function($cat) {
                return $cat['name'];
            }, $postData['_embedded']['wp:term'][0]);
        }

        $tags = [];
        if (isset($postData['_embedded']['wp:term'][1])) {
            $tags = array_map(function($tag) {
                return $tag['name'];
            }, $postData['_embedded']['wp:term'][1]);
        }

        // Extraer excerpt
        $excerpt = isset($postData['excerpt']['rendered']) 
            ? strip_tags($postData['excerpt']['rendered']) 
            : '';

        // Si es contenido completo, usar content, si no, truncar excerpt
        $content = $fullContent 
            ? $postData['content']['rendered'] ?? ''
            : (strlen($excerpt) > 200 ? substr($excerpt, 0, 200) . '...' : $excerpt);

        return [
            'id' => $postData['id'],
            'slug' => $postData['slug'],
            'title' => $postData['title']['rendered'] ?? $postData['title'],
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'author' => $author,
            'categories' => $categories,
            'tags' => $tags,
            'date' => $postData['date'],
            'date_formatted' => date('F j, Y', strtotime($postData['date'])),
            'modified' => $postData['modified'],
            'link' => $postData['link'],
        ];
    }

    private function getRelatedPosts($currentPost, $limit = 3)
    {
        try {
            $response = Http::timeout(10)->get(self::WORDPRESS_API_BASE . '/posts', [
                'per_page' => $limit + 1, // +1 para excluir el post actual
                '_embed' => true,
                'exclude' => $currentPost['id'],
            ]);

            if ($response->successful()) {
                $posts = $response->json();
                $relatedPosts = array_map(function($post) {
                    return $this->formatPost($post);
                }, array_slice($posts, 0, $limit));
                
                return $relatedPosts;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching related posts: ' . $e->getMessage());
        }

        return [];
    }
}
